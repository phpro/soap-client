<?php

declare(strict_types=1);

namespace Phpro\SoapClient\Soap\Driver\ExtSoap;

use Phpro\SoapClient\Soap\ClassMap\ClassMap;
use Phpro\SoapClient\Soap\ClassMap\ClassMapCollection;
use Phpro\SoapClient\Soap\TypeConverter\TypeConverterCollection;
use Phpro\SoapClient\Soap\TypeConverter\TypeConverterInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExtSoapOptionsResolverFactory
{
    public static function createForWsdl($wsdl): OptionsResolver
    {
        $resolver = self::create();
        if (!$wsdl) {
            $resolver = clone $resolver;
            $resolver->setRequired(['uri', 'location']);
        }

        return $resolver;
    }

    public static function create(): OptionsResolver
    {
        static $resolver;
        if ($resolver) {
            return $resolver;
        }

        $resolver = new OptionsResolver();

        // Override HTTP info
        $resolver->setDefined(['uri', 'location']);
        $resolver->setAllowedTypes('uri', ['string']);
        $resolver->setAllowedTypes('location', ['string']);

        // Specify SOAP version
        $resolver->setDefault('soap_version', SOAP_1_1);
        $resolver->setAllowedTypes('soap_version', 'int');
        $resolver->setAllowedValues('soap_version', [SOAP_1_1, SOAP_1_2]);

        // HTTP AUthentication
        $resolver->setDefined(['login', 'password', 'authentication']);
        $resolver->setAllowedTypes('login', ['string']);
        $resolver->setAllowedTypes('password', ['string']);
        $resolver->setAllowedTypes('authentication', ['int']);
        $resolver->setAllowedValues('authentication', [SOAP_AUTHENTICATION_BASIC, SOAP_AUTHENTICATION_DIGEST]);

        // HTTP Proxy
        $resolver->setDefined(['proxy_host', 'proxy_port', 'proxy_login', 'proxy_password']);
        $resolver->setAllowedTypes('proxy_host', ['string']);
        $resolver->setAllowedTypes('proxy_port', ['string', 'int']);
        $resolver->setAllowedTypes('proxy_login', ['string']);
        $resolver->setAllowedTypes('proxy_password', ['string']);

        // SSL Certificates
        $resolver->setDefined(['local_cert', 'passphrase']);
        $resolver->setAllowedTypes('local_cert', ['string']);
        $resolver->setAllowedTypes('passphrase', ['string', 'int']);

        // Compression
        $resolver->setDefined(['compression']);
        $resolver->setAllowedTypes('compression', ['int']);
        $resolver->setAllowedValues('compression', function ($value): bool {
            // Levels 0-9 Specify GZIP compression
            // @see: https://bugs.php.net/bug.php?id=36283
            return $value >= 0
                   && $value <= (
                       SOAP_COMPRESSION_ACCEPT
                       | SOAP_COMPRESSION_DEFLATE
                       | SOAP_COMPRESSION_GZIP
                       | 1 | 2 | 3 | 4 | 5 | 6 | 7 | 8 | 9
                );
        });

        // Encoding
        $resolver->setDefined(['encoding']);
        $resolver->setAllowedTypes('encoding', ['string']);

        // Trace
        $resolver->setDefault('trace', true);
        $resolver->setAllowedTypes('trace', ['bool']);

        // Classmaps
        $resolver->setDefault('classmap', new ClassMapCollection());
        $resolver->setAllowedTypes('classmap', [ClassMapCollection::class, 'array']);
        $resolver->setNormalizer('classmap', function (Options $options, $value): array {
            // Classic array configuration:
            if (!$value instanceof ClassMapCollection) {
                return $value;
            }

            return array_map(
                function (ClassMap $classMap) {
                    return $classMap->getPhpClassName();
                },
                iterator_to_array($value)
            );
        });

        // Exceptions
        $resolver->setDefault('exceptions', true);
        $resolver->setAllowedTypes('exceptions', ['bool']);

        // Timeouts
        $resolver->setDefined(['connection_timeout', 'default_socket_timeout']);
        $resolver->setAllowedTypes('connection_timeout', ['int']);
        $resolver->setAllowedTypes('default_socket_timeout', ['int']);

        // Typemaps
        $resolver->setDefault('typemap', new TypeConverterCollection());
        $resolver->setAllowedTypes('typemap', [TypeConverterCollection::class, 'array']);

        $resolver->setDefined(['typemap']);
        $resolver->setAllowedTypes('typemap', ['array', TypeConverterCollection::class]);
        $resolver->setNormalizer('typemap', function (Options $options, $value): array {
            // Classic array configuration:
            if (!$value instanceof TypeConverterCollection) {
                return $value;
            }

            return array_values(array_map(
                function (TypeConverterInterface $converter) {
                    return [
                        'type_name' => $converter->getTypeName(),
                        'type_ns' => $converter->getTypeNamespace(),
                        'from_xml' => function ($input) use ($converter) {
                            return $converter->convertXmlToPhp($input);
                        },
                        'to_xml' => function ($input) use ($converter) {
                            return $converter->convertPhpToXml($input);
                        },
                    ];
                },
                iterator_to_array($value)
            ));
        });

        // WSDL Caching
        $resolver->setDefault('cache_wsdl', WSDL_CACHE_NONE);
        $resolver->setAllowedTypes('cache_wsdl', ['int']);
        $resolver->setAllowedValues('cache_wsdl', [
            WSDL_CACHE_NONE,
            WSDL_CACHE_DISK,
            WSDL_CACHE_MEMORY,
            WSDL_CACHE_BOTH
        ]);

        // User agent
        $resolver->setDefined(['user_agent']);
        $resolver->setAllowedTypes('user_agent', ['string']);

        // Stream context
        $resolver->setDefined(['stream_context']);
        $resolver->setAllowedTypes('stream_context', ['resource']);

        // Features
        $resolver->setDefined(['features']);
        $resolver->setAllowedTypes('features', ['int']);
        $resolver->setAllowedValues('features', function ($value): bool {
            return $value >= 0
               && $value <= (SOAP_SINGLE_ELEMENT_ARRAYS | SOAP_USE_XSI_ARRAY_TYPE | SOAP_WAIT_ONE_WAY_CALLS);
        });

        // Keep alive
        $resolver->setDefined(['keep_alive']);
        $resolver->setAllowedTypes('keep_alive', ['bool']);

        // SSL Method
        $resolver->setDefined(['ssl_method']);
        $resolver->setAllowedTypes('ssl_method', ['int']);
        $resolver->setAllowedValues('ssl_method', [
            SOAP_SSL_METHOD_TLS,
            SOAP_SSL_METHOD_SSLv2,
            SOAP_SSL_METHOD_SSLv3,
            SOAP_SSL_METHOD_SSLv23
        ]);

        return $resolver;
    }
}
