<?php

namespace Phpro\SoapClient\Util;

use Phpro\SoapClient\Exception\InvalidArgumentException;
use Phpro\SoapClient\Exception\RuntimeException;
use SplFileInfo;

/**
 * Class Filesystem
 *
 * @package Phpro\SoapClient\Util
 */
class Filesystem
{

    /**
     * @param string $directory
     */
    public function ensureDirectoryExists(string $directory)
    {
        if (is_dir($directory)) {
            return;
        }
        if (file_exists($directory)) {
            throw new RuntimeException($directory.' exists and is not a directory.');
        }
        if (!@mkdir($directory, 0777, true)) {
            throw new RuntimeException($directory.' does not exist and could not be created.');
        }
    }

    /**
     * @param string $file
     *
     * @return bool
     */
    public function fileExists(string $file): bool
    {
        return is_file($file) && is_readable($file);
    }

    /**
     * @param string $path
     * @return string
     */
    public function getFileContents(string $path): string
    {
        if (!$this->fileExists($path)) {
            throw new InvalidArgumentException(sprintf('File %s does not exist.', $path));
        }

        return file_get_contents($path);
    }

    /**
     * @param string $path
     * @param mixed $content
     */
    public function putFileContents(string $path, $content)
    {
        $this->ensureDirectoryExists(\dirname($path));
        file_put_contents($path, $content);
    }

    /**
     * @param string $file
     */
    public function createBackup(string $file)
    {
        if (!$this->fileExists($file)) {
            throw new RuntimeException('Could not create a backup from a non existing file: '.$file);
        }

        $backupFile = preg_replace('{\.backup$}', '', $file).'.backup';
        copy($file, $backupFile);
    }

    /**
     * @param string $file
     */
    public function removeBackup(string $file)
    {
        $backupFile = preg_replace('{\.backup$}', '', $file).'.backup';
        if (!$this->fileExists($backupFile)) {
            return;
        }

        unlink($backupFile);
    }
}
