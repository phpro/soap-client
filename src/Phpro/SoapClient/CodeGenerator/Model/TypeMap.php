<?php

namespace Phpro\SoapClient\CodeGenerator\Model;

use Phpro\SoapClient\CodeGenerator\Util\Normalizer;
use Phpro\SoapClient\Soap\SoapClient;

/**
 * Class TypeMap
 *
 * @package Phpro\SoapClient\CodeGenerator\Model
 */
class TypeMap
{

    /**
     * @var array
     */
    private $types = [];

    /**
     * @var string
     */
    private $namespace;

    /**
     * TypeMap constructor.
     *
     * @param string $namespace
     * @param array $types
     */
    public function __construct(string $namespace, array $types)
    {
        $this->namespace = Normalizer::normalizeNamespace($namespace);

        foreach ($types as $type => $properties) {
            $this->types[] = new Type($namespace, $type, $properties);
        }
    }

    /**
     * @param string     $namespace
     * @param SoapClient $client
     *
     * @return TypeMap
     */
    public static function fromSoapClient(string $namespace, SoapClient $client): self
    {
        return new self($namespace, $client->getSoapTypes());
    }

    /**
     * @return string
     */
    public function getNamespace(): string
    {
        return $this->namespace;
    }

    /**
     * @return array|Type[]
     */
    public function getTypes(): array
    {
        return $this->types;
    }
}
