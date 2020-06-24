<?php

declare(strict_types=1);

namespace PhproTest\SoapClient\Functional\Wsdl;

use Phpro\SoapClient\Wsdl\Provider\CachedWsdlProvider;
use Phpro\SoapClient\Wsdl\Loader\WsdlLoaderInterface;
use Phpro\SoapClient\Wsdl\Provider\WsdlProviderInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Filesystem\Filesystem;

class CachedWsdlProviderTest extends TestCase
{
    /**
     * @var ObjectProphecy|WsdlLoader
     */
    private $loader;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var CachedWsdlProvider
     */
    private $wsdlProvider;

    /**
     * @var string
     */
    private $wsdl = 'http://localhost/some/service?wsdl';

    /**
     * @var string
     */
    private $targetDir;

    protected function setUp(): void
    {
        $this->loader = $this->prophesize(WsdlLoaderInterface::class);
        $this->loader->load($this->wsdl)->willReturn('wsdl');
        $this->filesystem = new Filesystem();
        $this->wsdlProvider = new CachedWsdlProvider(
            $this->loader->reveal(),
            $this->filesystem,
            $this->targetDir = sys_get_temp_dir().DIRECTORY_SEPARATOR.'soap-test-'.random_int(100, 999)
        );
    }

    protected function tearDown(): void
    {
        $this->filesystem->remove($this->targetDir);
    }

    /** @test */
    public function it_is_a_wsdl_provider(): void
    {
        self::assertInstanceOf(WsdlProviderInterface::class, $this->wsdlProvider);
    }

    /** @test */
    public function it_falls_back_to_temporary_file_if_no_permanent_exists(): void
    {
        $result = $this->wsdlProvider->provide($this->wsdl);

        self::assertStringContainsString(CachedWsdlProvider::LOCATION_TEMPORARY, $result);
        self::assertFileExists($result);
        self::assertStringEqualsFile($result, 'wsdl');
    }

    /** @test */
    public function it_always_downloads_temporary_files_even_if_it_already_exists(): void
    {
        $this->loader->load($this->wsdl)->shouldBeCalledTimes(2);

        $result = $this->wsdlProvider->provide($this->wsdl);
        $result2 = $this->wsdlProvider->provide($this->wsdl);

        self::assertSame($result, $result2);
        self::assertFileExists($result);
        self::assertStringEqualsFile($result, 'wsdl');
    }

    /** @test */
    public function it_uses_permanent_version_if_it_exists(): void
    {
        $this->loader->load($this->wsdl)->shouldBeCalledTimes(1);
        $permanent = $this->wsdlProvider->forcePermanentDownloads()->provide($this->wsdl);
        $result = $this->wsdlProvider->provide($this->wsdl);

        self::assertSame($permanent, $result);
        self::assertStringContainsString(CachedWsdlProvider::LOCATION_PERMANENT, $result);
        self::assertFileExists($result);
        self::assertStringEqualsFile($result, 'wsdl');
    }

    /** @test */
    public function it_is_able_to_force_download_permanent_version(): void
    {
        $this->loader->load($this->wsdl)->shouldBeCalledTimes(2);
        $forcedProvider = $this->wsdlProvider->forcePermanentDownloads();
        $result1 = $forcedProvider->provide($this->wsdl);
        $result2 = $forcedProvider->provide($this->wsdl);

        self::assertSame($result1, $result2);
        self::assertStringContainsString(CachedWsdlProvider::LOCATION_PERMANENT, $result1);
        self::assertFileExists($result1);
        self::assertStringEqualsFile($result1, 'wsdl');
    }
}
