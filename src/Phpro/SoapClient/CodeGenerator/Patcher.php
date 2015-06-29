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
        $patchFile = $tmpFile . '.patch';
        $this->filesystem->putFileContents($tmpFile, $newContent);

        // Patch content:
        try {
            $patchData = $this->createPatch($original, $tmpFile, $patchFile);
            // No patch needs to be applied ...
            if (!$patchData) {
                $this->cleanTmpFiles([$tmpFile, $patchFile]);
                return;
            }

            // Apply patch:
            $this->applyPatch($patchFile);
        } catch (PatchException $e) {
            $this->cleanTmpFiles([$tmpFile, $patchFile]);
            throw $e;
        }

        // Write with backup:
        $this->filesystem->replaceFile($original, $tmpFile, true);
        $this->cleanTmpFiles([$tmpFile, $patchFile]);
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
        $process = ProcessBuilder::create(['diff', '-uN', $new, $original])
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
    protected function applyPatch($patch)
    {
        $process = ProcessBuilder::create(['patch', '-N', '--input=' . $patch])
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
