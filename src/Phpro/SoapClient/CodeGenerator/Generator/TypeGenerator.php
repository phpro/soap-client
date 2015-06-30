<?php

namespace Phpro\SoapClient\CodeGenerator\Generator;

/**
 * Class TypeGenerator
 *
 * @package Phpro\SoapClient\Generator
 */
class TypeGenerator
{
    /**
     * @var string
     */
    private $namespace;

    /**
     * @param $namespace
     */
    public function __construct($namespace)
    {
        $this->namespace = $namespace;
    }

    /**
     * @param $type
     * @param $properties
     *
     * @return string
     */
    public function generate($type, array $properties)
    {
        $properties  = $this->renderProperties($properties);
        return $this->renderType($type, $properties);
    }

    /**
     * @param array $properties
     *
     * @return string
     */
    protected function renderProperties(array $properties)
    {
        $template = $this->getTypePropertyTemplate();
        $rendered = '';
        foreach ($properties as $property => $type) {
            $type = strtr($type, [
                'long' => 'int',
                'dateTime' => '\\DateTime',
                'date' => '\\DateTime',
                'boolean' => 'bool',
            ]);

            $values = [
                '%property%' => $property,
                '%type%' => $type
            ];
            $rendered .= $this->renderString($template, $values);
        }

        return $rendered;
    }

    /**
     * @param string $type
     * @param string $properties
     *
     * @return string
     */
    protected function renderType($type, $properties)
    {
        $template = $this->getTypeTemplate();
        $values = [
            '%name%' => ucfirst($type),
            '%namespace_block%' => $this->namespace ? sprintf("\n\nnamespace %s;", $this->namespace) : '',
            '%properties%' => $properties,
        ];

        return $this->renderString($template, $values);
    }

    /**
     * @param $template
     * @param $values
     *
     * @return string
     */
    protected function renderString($template, array $values)
    {
        return strtr($template, $values);
    }

    /**
     * @return string
     */
    protected function getTypeTemplate()
    {
        return file_get_contents(__DIR__ . '/templates/type.template');
    }

    /**
     * @return string
     */
    protected function getTypePropertyTemplate()
    {
        return file_get_contents(__DIR__ . '/templates/type-property.template');
    }
}
