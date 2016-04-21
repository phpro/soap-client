<?php

namespace PhproTest\SoapClient\Unit\CodeGenerator;

use Phpro\SoapClient\CodeGenerator\Patcher;
use Phpro\SoapClient\Util\Filesystem;

/**
 * Class PatcherTest
 *
 * @package PhproTest\SoapClient\Unit\CodeGenerator
 */
class PatcherTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Patcher
     */
    private $patcher;

    /**
     * @var array
     */
    private $createdFiles = array();

    protected function setUp()
    {
        $this->patcher = new Patcher(new Filesystem());
    }

    protected function tearDown()
    {
        foreach ($this->createdFiles as $file) {
            if (file_exists($file)) {
                unlink($file);
            }

            if (file_exists($file . '.rej')) {
                unlink($file . '.rej');
            }
        }
    }

    private function loadFixture($file)
    {
        if (!file_exists($filePath = FIXTURE_DIR . '/code-generator/' . $file)) {
            throw new \RuntimeException('Could not load fixture file: ' . $filePath);
        };

        return file_get_contents($filePath);
    }

    /**
     * @dataProvider patchDataProvider
     * @test
     */
    function it_should_patch_an_existing_file($originalContent, $newContent, $patchedContent)
    {
        $originalFile = $this->createdFiles[] = tempnam(sys_get_temp_dir(), 'patchtest');
        $backupFile = $originalFile . '.backup';

        file_put_contents($originalFile, $originalContent);
        $this->patcher->patch($originalFile, $newContent);

        $this->assertStringEqualsFile(
            $originalFile,
            $patchedContent,
            sprintf('Invalid patched content in original file %s!', $originalFile)
        );

        if ($originalContent !== $newContent) {
            $this->assertStringEqualsFile(
                $backupFile,
                $originalContent,
                sprintf('Invalid backup file %s!', $backupFile)
            );
        }
    }

    function patchDataProvider()
    {
        return array(
            array(
                $this->loadFixture('class-default.php'), 
                $this->loadFixture('class-default.php'),
                $this->loadFixture('class-default.php'),
            ),
            array(
                $this->loadFixture('class-default.php'),
                $this->loadFixture('soap-newfield.php'),
                $this->loadFixture('soap-newfield.php'),
            ),
            array(
                $this->loadFixture('class-default.php'),
                $this->loadFixture('soap-removedfield.php'),
                $this->loadFixture('soap-removedfield.php'),
            ),
            array(
                $this->loadFixture('class-tampered.php'),
                $this->loadFixture('soap-removedfield.php'),
                $this->loadFixture('patched-tampered-removedfield.php'),
            ),
            array(
                $this->loadFixture('class-tampered.php'),
                $this->loadFixture('soap-removedfield.php'),
                $this->loadFixture('patched-tampered-removedfield.php'),
            ),
        );
    }

}
