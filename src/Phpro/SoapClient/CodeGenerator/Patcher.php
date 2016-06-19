<?php

namespace Phpro\SoapClient\CodeGenerator;

use Phpro\SoapClient\Exception\InvalidArgumentException;
use Phpro\SoapClient\Exception\PatchException;
use Phpro\SoapClient\Util\Filesystem;
use Symfony\Component\Process\ProcessBuilder;

class Patcher
{

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @param Filesystem $filesystem
     * @param string     $tmpFolder
     */
    public function __construct(Filesystem $filesystem, $tmpFolder = '')
    {
        $this->filesystem = $filesystem;
        $this->tmpFolder = $tmpFolder ? rtrim($tmpFolder, '\\') : sys_get_temp_dir();

        if (!$filesystem->dirextoryExists($this->tmpFolder)) {
            throw new InvalidArgumentException(sprintf('The temporary directory %s is not writable', $this->tmpFolder));
        }
    }

    /**
     * @param string $original
     * @param string $newContent
     */
    public function patch($original, $newContent)
    {
        $original = realpath($original);
        $basename = pathinfo($original, PATHINFO_BASENAME);
        $tmpFile = $this->tmpFolder . DIRECTORY_SEPARATOR . time() . $basename;
        $tmpFilePatched = $tmpFile . 'patch';
        $functions = $this->getFunctions($original);
        $patchFile = $tmpFile . '.patch';
        $this->filesystem->putFileContents($tmpFile, $newContent);
        $this->filesystem->putFileContents($tmpFilePatched, $this->filesystem->getFileContents($original));
        // Patch content:
        try {
            // Try to patch the original file into the new temporary file:
            $patchData = $this->createPatch($tmpFilePatched, $tmpFile, $patchFile);
            // No patch needs to be applied ...
            if (!$patchData) {
                $this->cleanTmpFiles([$tmpFile, $patchFile, $tmpFilePatched]);
                return;
            }

            // Apply patch to the temporary new file:
            $this->applyPatch($patchFile, $tmpFilePatched);
        } catch (PatchException $e) {
            $this->cleanTmpFiles([$tmpFile, $patchFile, $tmpFilePatched]);
            throw $e;
        }

        // Write with backup:
        $this->filesystem->replaceFile($original, $tmpFilePatched, true);
        $endClassPos = strrpos($this->filesystem->getFileContents($original), '}') ;
        $this->filesystem->addToFile($original, $functions, $endClassPos);
        $this->cleanTmpFiles([$tmpFile, $patchFile, $tmpFilePatched]);
    }

    /**
     * @param string $original
     *
     * @return string
     */
    protected function getFunctions($original)
    {
        $originalContent = $this->filesystem->getFileContents($original);
        $functionsStartPos = strpos($originalContent, 'public function');
        if ($functionsStartPos === false) {
            return '';
        }
        //Get the beginning of the functions by looking for the first new line
        // before the first '/*' (PhpDoc) before the first public function. Add 1 to remove the new line symbol.
        $functionsStartPos = strrpos(
            substr($originalContent, 0, strrpos(
                substr($originalContent, 0, $functionsStartPos),
                '/*'
            )),
            "\n"
        ) + 1;
        $functionsLength = strrpos($originalContent, "}", $functionsStartPos) - $functionsStartPos;
        return substr($originalContent, $functionsStartPos, $functionsLength);
    }

    /**
     * @param string $original
     * @param string $new
     * @param string $patchFile
     *
     * @return string
     */
    protected function createPatch($original, $new, $patchFile)
    {
        $process = ProcessBuilder::create(['diff', '-uN', $original, $new])
            ->setWorkingDirectory($this->tmpFolder)
            ->getProcess();
        $process->run();

        if (!$process->isSuccessful() && $process->getExitCode() !== 1) {
            throw new PatchException('Diff failed: ' . $process->getOutput());
        }

        $patchData = $process->getOutput();
        $this->filesystem->putFileContents($patchFile, $patchData);
        return $patchData;
    }

    /**
     * @param $patch
     *
     * @return string
     */
    protected function applyPatch($patch, $targetFile)
    {
        $process = ProcessBuilder::create(['patch', '-N', $targetFile, $patch])
            ->setWorkingDirectory($this->tmpFolder)
            ->getProcess();
        $process->run();

        if (!$process->isSuccessful()) {
            throw new PatchException('Patch failed: ' . $process->getOutput());
        }

        return $process->getOutput();
    }

    /**
     * @param array $files
     */
    protected function cleanTmpFiles(array $files)
    {
        foreach ($files as $file) {
            $this->filesystem->removeFile($file);
        }
    }
}
