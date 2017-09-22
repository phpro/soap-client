<?php

namespace Phpro\SoapClient\Util;

use Phpro\SoapClient\Exception\InvalidArgumentException;
use Phpro\SoapClient\Exception\RuntimeException;

/**
 * Class Filesystem
 *
 * @package Phpro\SoapClient\Util
 */
class Filesystem
{

    /**
     * @param $directory
     *
     * @return bool
     */
    public function directoryExists($directory)
    {
        return is_dir($directory) && is_writable($directory);
    }

    /**
     * @param $file
     *
     * @return bool
     */
    public function fileExists($file)
    {
        return is_file($file) && is_readable($file);
    }

    /**
     * @param $path
     * @return string
     */
    public function getFileContents($path)
    {
        if (!$this->fileExists($path)) {
            throw new InvalidArgumentException(sprintf('File %s does not exist.', $path));
        }

        return file_get_contents($path);
    }

    /**
     * @param $path
     * @param $content
     */
    public function putFileContents($path, $content)
    {
        file_put_contents($path, $content);
    }

    /**
     * @param string $file
     */
    public function createBackup($file)
    {
        if (!$this->fileExists($file)) {
            throw new RuntimeException('Could not create a backup from a non existing file: ' . $file);
        }

        $backupFile = preg_replace('{\.backup$}', '', $file) . '.backup';
        copy($file, $backupFile);
    }

    /**
     * @param string $file
     */
    public function removeBackup($file)
    {
        $backupFile = preg_replace('{\.backup$}', '', $file) . '.backup';
        if (!$this->fileExists($backupFile)) {
            return;
        }

        unlink($backupFile);
    }
}
