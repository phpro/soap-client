<?php

namespace Phpro\SoapClient\MiddleWare;

use Phpro\SoapClient\Xml\SoapXml;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RobRichards\WsePhp\WSSESoap;
use RobRichards\XMLSecLibs\XMLSecurityKey;

/**
 * Class WsseMiddleware
 *
 * @package Phpro\SoapClient\Middleware
 */
class WsseMiddleware extends Middleware
{
    /**
     * @var callable
     */
    private $prepareWsseRequest;

    /**
     * @var callable
     */
    private $prepareWsseResponse;

    /**
     * WsseMiddleware constructor.
     *
     * @param callable $prepareWsseRequest
     * @param callable $prepareWsseResponse
     */
    public function __construct(callable $prepareWsseRequest, callable $prepareWsseResponse)
    {
        $this->prepareWsseRequest = $prepareWsseRequest;
        $this->prepareWsseResponse = $prepareWsseResponse;
    }

    /**
     * {@inheritdoc}
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function beforeRequest(callable $handler, RequestInterface $request, array $options)
    {
        $xml = SoapXml::fromStream($request->getBody()->getContents());

        $wsse = new WSSESoap($xml->getXmlDocument());
        $this->prepareWsseRequest($wsse, $xml);

        $request = $request->withBody($xml->toStream());

        return $handler($request, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function afterResponse(ResponseInterface $response)
    {
        $xml = SoapXml::fromStream($response->getBody()->getContents());

        $wsse = new WSSESoap($xml->getXmlDocument());
        $this->prepareWsseResponse($wsse, $xml);

        return $response->withBody($xml->toStream());
    }

    /**
     * @return WsseMiddleware
     */
    public static function wsa(
        string $publicKey,
        string $privateKey,
        string $dsigMethod = XMLSecurityKey::RSA_SHA1
    ): WsseMiddleware
    {
        return new self(
            function(WSSESoap $wsse) use ($publicKey, $privateKey, $dsigMethod) {
                // Make sure the WSA headers get signed and add a timestamp:
                $wsse->signAllHeaders = true;
                $wsse->addTimestamp();

                // Sign the SOAP document with your private key:
                $key = new XMLSecurityKey($dsigMethod, ['type' => 'private']);
                $key->loadKey($privateKey, true);
                $wsse->signSoapDoc($key);

                // Add the public key to the request:
                $token = $wsse->addBinaryToken(file_get_contents($publicKey));
                $wsse->attachTokentoSig($token);
            },
            function() {}
        );
    }

    /**
     * TODO: certificates
     *
     * @param string $username
     * @param string $password
     * @param bool   $digest
     *
     * @return WsseMiddleware
     */
    public static function userSigned(string $username, string $password, bool $digest = false)
    {
        return new self(
            function (WSSESoap $wsse) use ($username, $password, $digest) {
                /* Sign all headers to include signing the WS-Addressing headers */
                $wsse->signAllHeaders = true;
                $wsse->addTimestamp();
                $wsse->addUserToken($username, $password, $digest);

                /* create new XMLSec Key using RSA SHA-1 and type is private key */
                $key = new XMLSecurityKey(XMLSecurityKey::RSA_SHA1, array('type' => 'private'));
                $key->loadKey(PRIVATE_KEY, true);
                $wsse->signSoapDoc($key);

                /* Add certificate (BinarySecurityToken) to the message and attach pointer to Signature */
                $token = $wsse->addBinaryToken(file_get_contents(CERT_FILE));
                $wsse->attachTokentoSig($token);
            },
            function() {}
        );
        
    }

    /**
     * @return WsseMiddleware
     * @throws \Exception
     */
    public static function encrypt(
        string $publicKey,
        string $privateKey,
        string $serviceCertificate,
        string $dsigMethod = XMLSecurityKey::RSA_SHA1
    )
    {
        return new self(
            function(WSSESoap $wsse) use ($publicKey, $privateKey, $serviceCertificate, $dsigMethod) {
                $wsse->addTimestamp();

                $key = new XMLSecurityKey($dsigMethod, ['type' => 'private']);
                $key->loadKey(PRIVATE_KEY, true);

                /* Sign the message - also signs appropiate WS-Security items */
                $wsse->signSoapDoc($key, [
                    'insertBefore' => false
                ]);

                /* Add certificate (BinarySecurityToken) to the message */
                $token = $wsse->addBinaryToken(file_get_contents($publicKey));
                $wsse->attachTokentoSig($token);

                /* Attach pointer to Signature */
                $key = new XMLSecurityKey(XMLSecurityKey::AES256_CBC);
                $key->generateSessionKey();
                $siteKey = new XMLSecurityKey(XMLSecurityKey::RSA_OAEP_MGF1P, ['type' => 'public']);
                $siteKey->loadKey($serviceCertificate, true, true);
                $wsse->encryptSoapDoc($siteKey, $key,  [
                    'KeyInfo' => [
                        'X509SubjectKeyIdentifier' => true
                    ]
                ]);
            },
            function(WSSESoap $wsse, SoapXml $xml) use($privateKey) {
                $wsse->decryptSoapDoc($xml->getXmlDocument(), [
                    'keys' => [
                        'private' => [
                            'key' => $privateKey,
                            'isFile' => true,
                            'isCert' => false
                        ]
                    ]
                ]);
            }
        );
    }
}
