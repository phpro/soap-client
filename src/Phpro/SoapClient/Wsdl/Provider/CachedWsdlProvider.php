<?php

declare(strict_types=1);

namespace Phpro\SoapClient\Wsdl\Provider;

use Phpro\SoapClient\Wsdl\Loader\WsdlLoaderInterface;
use Symfony\Component\Filesystem\Filesystem;

final class CachedWsdlProvider implements WsdlProviderInterface
{
    public const LOCATION_PERMANENT = 'permanent';
    public const LOCATION_TEMPORARY = 'temporary';

    /**
     * @var WsdlLoaderInterface
     */
    private $loader;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var string
     */
    private $cacheDir;

    /**
     * @var string
     */
    private $target = self::LOCATION_TEMPORARY;

    /**
     * @var bool
     */
    private $forceDownload = false;

    public function __construct(WsdlLoaderInterface $loader, Filesystem $filesystem, string $cacheDir)
    {
        $this->loader = $loader;
        $this->cacheDir = $cacheDir;
        $this->filesystem = $filesystem;
    }

    public function forcePermanentDownloads(): self
    {
        $new = clone $this;
        $new->target = self::LOCATION_PERMANENT;
        $new->forceDownload = true;

        return $new;
    }

    public function provide(string $source): string
    {
        $filename = md5($source).'.wsdl';
        $permanentLocation = $this->cacheDir.DIRECTORY_SEPARATOR.self::LOCATION_PERMANENT.DIRECTORY_SEPARATOR.$filename;
        $temporaryLocation = $this->cacheDir.DIRECTORY_SEPARATOR.self::LOCATION_TEMPORARY.DIRECTORY_SEPARATOR.$filename;

        if (!$this->forceDownload && $this->filesystem->exists($permanentLocation)) {
            return $permanentLocation;
        }

        $target = self::LOCATION_PERMANENT === $this->target ? $permanentLocation : $temporaryLocation;
        $this->filesystem->dumpFile($target, $this->loader->load($source));

        return $target;
    }
}
