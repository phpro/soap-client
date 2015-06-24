<?php

namespace Phpro\SoapClient\CodeGenerator\Generator;

/**
 * Class ClassmapGenerator
 *
 * @package Phpro\SoapClient\Generator
 */
class ClassmapGenerator
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
     * @param array $types
     *
     * @return string
     */
    public function generate(array $types)
    {
        $classmap = $this->renderClassmaps($types);
        return $this->renderClassmapCollection($classmap);
    }

    /**
     * @param array $types
     *
     * @return string
     */
    protected function renderClassmaps(array $types)
    {
        $template = $this->getClassmapTemplate();
        $namespace = $this->namespace ? sprintf('%s\\', trim($this->namespace, '\\')) : '\\';
        $rendered = '';
        foreach ($types as $type => $properties) {
            $values = [
                '%php_type%' => $namespace . ucfirst($type),
                '%xsd_type%' => $type
            ];
            $rendered .= $this->renderString($template, $values);
        }

        return $rendered;
    }

    /**
     * @param string $classmap
     *
     * @return string
     */
    protected function renderClassmapCollection($classmap)
    {
        $template = $this->getClassmapCollectionTemplate();
        $values = [
            '%classmap%' => $classmap,
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
    protected function getClassmapTemplate()
    {
        return file_get_contents(__DIR__ . '/templates/classmap.template');
    }

    /**
     * @return string
     */
    protected function getClassmapCollectionTemplate()
    {
        return file_get_contents(__DIR__ . '/templates/classmap-collection.template');
    }
}
