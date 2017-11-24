<?php

namespace Phpro\SoapClient\Wsdl\Provider;

use Phpro\SoapClient\Exception\WsdlException;
use Phpro\SoapClient\Util\Filesystem;

/**
 * Class LocalWsdlProvider
 *
 * @package Phpro\SoapClient\Wsdl\Provider
 */
class LocalWsdlProvider implements WsdlProviderInterface
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * LocalWsdlProvider constructor.
     *
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * @return LocalWsdlProvider
     */
    public static function create(): self
    {
        return new self(new Filesystem());
    }

    /**
     * @param string $source
     *
     * @return string
     */
    public function provide(string $source): string
    {
        if (!$this->filesystem->fileExists($source)) {
            throw WsdlException::notFound($source);
        }

        return $source;
    }
}
