<?php

/*
 * This file is part of the Phpro application.
 *
 * Copyright (c) 2015-2017 Phpro
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
    public static function create()
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
