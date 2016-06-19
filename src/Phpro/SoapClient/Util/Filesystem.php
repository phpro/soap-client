<?php

namespace Phpro\SoapClient\Util;

use Phpro\SoapClient\Exception\InvalidArgumentException;

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
    public function dirextoryExists($directory)
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
     * @param $file
     *
     * @return bool
     */
    public function removeFile($file)
    {
        return unlink($file);
    }

    /**
     * @param string $file
     * @param string $new
     * @param bool $backup
     */
    public function replaceFile($file, $new, $backup = true)
    {
        if ($backup) {
            $backupFile = $file . '.backup';
            copy($file, $backupFile);
        }

        $this->putFileContents($file, $this->getFileContents($new));
    }

    /**
     * @param string $file
     * @param string $newContent
     * @param int $pos the position at which to insert the content. (default to NULL, understood as append)
     */
    public function addToFile($file, $newContent, $pos = null)
    {
        if ($pos === null) {
            $content = $this->getFileContents($file);
            $content .= $newContent;
        } else {
            $content = substr($this->getFileContents($file), 0, $pos);
            $content .= $newContent;
            $content .= substr($this->getFileContents($file), $pos);
        }
        $this->putFileContents($file, $content);
    }
}
