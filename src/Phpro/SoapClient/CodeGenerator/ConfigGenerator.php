<?php

namespace Phpro\SoapClient\CodeGenerator;

use Phpro\SoapClient\CodeGenerator\Config\ClassMapConfig;
use Phpro\SoapClient\CodeGenerator\Config\ClientConfig;
use Phpro\SoapClient\CodeGenerator\Config\Config;
use Phpro\SoapClient\CodeGenerator\Config\Destination;
use Phpro\SoapClient\CodeGenerator\Config\TypeNamespaceMap;
use Phpro\SoapClient\CodeGenerator\Context\ConfigContext;
use Phpro\SoapClient\CodeGenerator\TypeNamespaceMap\Strategy\PrefixBasedTypeNamespaceStrategy;
use Phpro\SoapClient\CodeGenerator\Util\Normalizer;
use Laminas\Code\Generator\FileGenerator;
use Phpro\SoapClient\Soap\DefaultEngineFactory;
use Phpro\SoapClient\Soap\EngineOptions;

/**
 * @template-implements GeneratorInterface<ConfigContext>
 */
class ConfigGenerator implements GeneratorInterface
{
    const BODY = <<<BODY
return Config::create()

BODY;

    const RULESET_RESPONSE = <<<RULESET
->addRule(
    new Rules\IsResultRule(
        \$engine->getMetadata(),
        new Rules\MultiRule([
            new Rules\AssembleRule(new Assembler\ResultAssembler()),
        ])
    )
)
RULESET;

    const ENGINE_BOILERPLATE = <<<EOENGINE
->setEngine(\$engine = DefaultEngineFactory::create(
        EngineOptions::defaults('%s')
    ))
EOENGINE;

    /**
     * Generate code for TypeNamespaceMap configuration
     *
     * @param array<string, string> $detectedXmlNamespaces
     */
    private function generateTypeNamespaceMapCode(
        Destination $fallback,
        array $detectedXmlNamespaces,
        string $indentation
    ): string {
        $createCall = sprintf(
            'TypeNamespaceMap::create(new Destination(%s, %s))',
            var_export($fallback->path, true),
            var_export($fallback->namespace, true)
        );

        $lines = [];
        $lines[] = GeneratorInterface::EOL . $indentation . $indentation . $createCall;
        foreach ($detectedXmlNamespaces as $xmlns => $prefix) {
            $segment = Normalizer::normalizeNamespaceSegment($prefix);
            $suggestedPath = $fallback->path . ($segment !== null ? '/' . $segment : '');
            $suggestedNamespace = $fallback->namespace . ($segment !== null ? '\\' . $segment : '');

            $lines[] = sprintf(
                '%s%s// ->withMapping(%s, new Destination(%s, %s))',
                $indentation,
                $indentation,
                var_export($xmlns, true),
                var_export($suggestedPath, true),
                var_export($suggestedNamespace, true)
            );
        }

        $lines[] = sprintf(
            '%s%s// ->withStrategy(new PrefixBasedTypeNamespaceStrategy())',
            $indentation,
            $indentation
        );

        return implode(GeneratorInterface::EOL, $lines) . GeneratorInterface::EOL . $indentation;
    }

    /**
     * Generate code for ClientConfig configuration
     */
    private function generateClientConfigCode(ClientConfig $config): string
    {
        return sprintf(
            'new ClientConfig(%s, new Destination(%s, %s))',
            var_export($config->name, true),
            var_export($config->destination->path, true),
            var_export($config->destination->namespace, true)
        );
    }

    /**
     * Generate code for ClassMapConfig configuration
     */
    private function generateClassMapConfigCode(ClassMapConfig $config): string
    {
        return sprintf(
            'new ClassMapConfig(%s, new Destination(%s, %s))',
            var_export($config->name, true),
            var_export($config->destination->path, true),
            var_export($config->destination->namespace, true)
        );
    }

    /**
     * @param FileGenerator $file
     * @param string $ruleset
     * @return string
     */
    private function parseIndentedRuleSet(FileGenerator $file, string $ruleset): string
    {
        return $file->getIndentation().preg_replace('/\n/', sprintf("\n%s", $file->getIndentation()), $ruleset)
            .GeneratorInterface::EOL;
    }

    private function parseEngine(FileGenerator $fileGenerator, string $wsdl): string
    {
        return $fileGenerator->getIndentation().sprintf(self::ENGINE_BOILERPLATE, $wsdl).GeneratorInterface::EOL;
    }

    /**
     * @param FileGenerator $file
     * @param ConfigContext $context
     * @return string
     */
    public function generate(FileGenerator $file, $context): string
    {
        $body = self::BODY;
        $file->setUse('Phpro\\SoapClient\\CodeGenerator\\Assembler');
        $file->setUse('Phpro\\SoapClient\\CodeGenerator\\Rules');
        $file->setUse(ClassMapConfig::class);
        $file->setUse(ClientConfig::class);
        $file->setUse(Config::class);
        $file->setUse(Destination::class);
        $file->setUse(TypeNamespaceMap::class);
        $file->setUse(EngineOptions::class);
        $file->setUse(DefaultEngineFactory::class);

        $body .= $this->parseEngine($file, $context->getWsdl());

        // Generate TypeNamespaceMap setter
        if ($typeDestination = $context->getTypeDestination()) {
            $file->setUse(PrefixBasedTypeNamespaceStrategy::class);
            $detectedXmlNamespaces = $context->getDetectedXmlNamespaces();
            $body .= sprintf(
                "%s->setTypeNamespaceMap(%s)".GeneratorInterface::EOL,
                $file->getIndentation(),
                $this->generateTypeNamespaceMapCode($typeDestination, $detectedXmlNamespaces, $file->getIndentation())
            );
        }

        // Generate Client setter
        if ($clientConfig = $context->getClientConfig()) {
            $body .= sprintf(
                "%s->setClient(%s)".GeneratorInterface::EOL,
                $file->getIndentation(),
                $this->generateClientConfigCode($clientConfig)
            );
        }

        // Generate ClassMap setter
        if ($classMapConfig = $context->getClassMapConfig()) {
            $body .= sprintf(
                "%s->setClassMap(%s)".GeneratorInterface::EOL,
                $file->getIndentation(),
                $this->generateClassMapConfigCode($classMapConfig)
            );
        }

        $body .= $this->parseIndentedRuleSet($file, $this->generateGetterSetterRuleSet($context));
        $body .= $this->parseIndentedRuleSet($file, $this->generateRequestRuleSet($context));
        $body .= $this->parseIndentedRuleSet($file, self::RULESET_RESPONSE);
        $body .= $this->parseIndentedRuleSet($file, $this->generateInheritanceRules());

        $file->setBody($body.';'.GeneratorInterface::EOL);

        return $file->generate();
    }

    private function generateGetterSetterRuleSet(ConfigContext $context): string
    {
        if ($context->isGenerateDocblocks()) {
            return <<<RULESET
->addRule(new Rules\AssembleRule(new Assembler\GetterAssembler(new Assembler\GetterAssemblerOptions())))
->addRule(new Rules\AssembleRule(new Assembler\ImmutableSetterAssembler(
    new Assembler\ImmutableSetterAssemblerOptions()
)))
RULESET;
        }

        return <<<RULESET
->addRule(new Rules\AssembleRule(new Assembler\GetterAssembler(
    (new Assembler\GetterAssemblerOptions())->withDocBlocks(false)
)))
->addRule(new Rules\AssembleRule(new Assembler\ImmutableSetterAssembler(
    (new Assembler\ImmutableSetterAssemblerOptions())->withDocBlocks(false)
)))
RULESET;
    }

    private function generateRequestRuleSet(ConfigContext $context): string
    {
        if ($context->isGenerateDocblocks()) {
            return <<<REQUEST
->addRule(
    new Rules\IsRequestRule(
        \$engine->getMetadata(),
        new Rules\MultiRule([
            new Rules\AssembleRule(new Assembler\RequestAssembler()),
            new Rules\AssembleRule(new Assembler\ConstructorAssembler(new Assembler\ConstructorAssemblerOptions())),
        ])
    )
)
REQUEST;
        }

        return <<<REQUEST
->addRule(
    new Rules\IsRequestRule(
        \$engine->getMetadata(),
        new Rules\MultiRule([
            new Rules\AssembleRule(new Assembler\RequestAssembler()),
            new Rules\AssembleRule(new Assembler\ConstructorAssembler(
                (new Assembler\ConstructorAssemblerOptions())->withDocBlocks(false)
            )),
        ])
    )
)
REQUEST;
    }

    private function generateInheritanceRules(): string
    {
        return <<<INHERITANCE
->addRule(
    new Rules\IsExtendingTypeRule(
        \$engine->getMetadata(),
        new Rules\AssembleRule(new Assembler\ExtendingTypeAssembler())
    )
)
->addRule(
    new Rules\IsAbstractTypeRule(
        \$engine->getMetadata(),
        new Rules\AssembleRule(new Assembler\AbstractClassAssembler())
    )
)
INHERITANCE;
    }
}
