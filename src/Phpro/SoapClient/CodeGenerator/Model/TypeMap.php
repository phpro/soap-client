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
     * @param array $soapTypes
     * @param DuplicateType[] $duplicateTypes
     */
    public function __construct(string $namespace, array $soapTypes, array $duplicateTypes = [])
    {
        $this->namespace = Normalizer::normalizeNamespace($namespace);

        $unprocessedSoapTypes = $soapTypes;
        foreach ($duplicateTypes as $duplicateType) {
            $alreadyMatched = false;
            foreach ($unprocessedSoapTypes as $key => $soapType) {
                if (!$soapType['duplicate']) {
                    continue;
                }

                $type = new Type($namespace, $soapType['typeName'], $soapType['properties']);
                if ($duplicateType->matchType($type)) {
                    /**
                     * If duplicate is already matched but in another namespace
                     * we can ignore it and use only class from that another namespace
                     */
                    if ($alreadyMatched) {
                        unset($unprocessedSoapTypes[$key]);
                        continue;
                    }

                    $this->types[] = new Type(
                        $namespace,
                        $soapType['typeName'],
                        $soapType['properties'],
                        $duplicateType
                    );

                    unset($unprocessedSoapTypes[$key]);
                    $alreadyMatched = true;
                }
            }
        }

        foreach ($unprocessedSoapTypes as $key => $soapType) {
            if ($soapType['duplicate']) {
                continue;
            }

            $this->types[] = new Type($namespace, $soapType['typeName'], $soapType['properties']);
            unset($unprocessedSoapTypes[$key]);
        }

        if ($unprocessedSoapTypes) {
            $unprocessedSoapTypeNames = [];
            foreach ($unprocessedSoapTypes as $duplicateType) {
                $unprocessedSoapTypeNames[] = $duplicateType['typeName'];
            }

            throw new \Exception(
                'WSDL contains duplicate types('.implode(', ', $unprocessedSoapTypeNames).') 
                but duplicateType isn`t explicitly defined in configration.'
            );
        }
    }

    /**
     * @param string     $namespace
     * @param SoapClient $client
     *
     * @return TypeMap
     */
    public static function fromSoapClient(string $namespace, SoapClient $client, array $duplicateTypes = []): self
    {
        return new self($namespace, $client->getSoapTypes(), $duplicateTypes);
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
