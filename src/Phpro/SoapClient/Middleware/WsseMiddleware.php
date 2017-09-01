<?php

namespace Phpro\SoapClient\Middleware;

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
     * @var string
     */
    private $privateKeyFile;

    /**
     * @var string
     */
    private $publicKeyFile;

    /**
     * @var string
     */
    private $serverCertificateFile = '';

    /**
     * @var int
     */
    private $timestamp = 3600;

    /**
     * @var bool
     */
    private $signAllHeaders = false;

    /**
     * @var string
     */
    private $digitalSignMethod = XMLSecurityKey::RSA_SHA1;

    /**
     * @var string
     */
    private $userTokenName = '';

    /**
     * @var string
     */
    private $userTokenPassword = '';

    /**
     * @var bool
     */
    private $userTokenDigest = false;

    /**
     * @var bool
     */
    private $encrypt = false;

    /**
     * @var bool
     */
    private $hasUserToken = false;

    /**
     * @var bool
     */
    private $serverCertificateHasSubjectKeyIdentifier = true;

    /**
     * WsseMiddleware constructor.
     *
     * @param string $privateKeyFile
     * @param string $publicKeyFile
     */
    public function __construct(string $privateKeyFile, string $publicKeyFile)
    {
        $this->privateKeyFile = $privateKeyFile;
        $this->publicKeyFile = $publicKeyFile;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'wsse_middleware';
    }

    /**
     * @param int $timestamp
     *
     * @return $this
     */
    public function withTimestamp(int $timestamp = 3600)
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    /**
     * @return $this
     */
    public function withAllHeadersSigned()
    {
        $this->signAllHeaders = true;

        return $this;
    }

    /**
     * @param string $digitalSignMethod
     *
     * @return $this
     */
    public function withDigitalSignMethod(string $digitalSignMethod)
    {
        $this->digitalSignMethod = $digitalSignMethod;

        return $this;
    }

    /**
     * @param string      $username
     * @param string|null $password
     * @param bool        $digest
     *
     * @return $this
     */
    public function withUserToken(string $username, string $password = null, $digest = false)
    {
        $this->hasUserToken = true;
        $this->userTokenName = $username;
        $this->userTokenPassword = $password;
        $this->userTokenDigest = $digest;

        return $this;
    }

    /**
     * @param $serverCertificateFile
     *
     * @return $this
     */
    public function withEncryption($serverCertificateFile)
    {
        $this->encrypt = true;
        $this->serverCertificateFile = $serverCertificateFile;

        return $this;
    }

    /**
     * @param bool $hasSubjectKeyIdentifier
     *
     * @return $this
     */
    public function withServerCertificateHasSubjectKeyIdentifier(bool $hasSubjectKeyIdentifier)
    {
        $this->serverCertificateHasSubjectKeyIdentifier = $hasSubjectKeyIdentifier;

        return $this;
    }

    /**
     * @param callable         $handler
     * @param RequestInterface $request
     * @param array            $options
     *
     * @return mixed
     */
    public function beforeRequest(callable $handler, RequestInterface $request, array $options)
    {

        $xml = SoapXml::fromStream($request->getBody());

        $wsse = new WSSESoap($xml->getXmlDocument());

        // Prepare the WSSE soap class:
        $wsse->signAllHeaders = $this->signAllHeaders;
        $wsse->addTimestamp($this->timestamp);

        // Add a user token if this is configured.
        if ($this->hasUserToken) {
            $wsse->addUserToken($this->userTokenName, $this->userTokenPassword, $this->userTokenDigest);
        }

        // Create new XMLSec Key using the dsigType and type is private key
        $key = new XMLSecurityKey($this->digitalSignMethod, ['type' => 'private']);
        $key->loadKey($this->privateKeyFile, true);
        $wsse->signSoapDoc($key);

        //  Add certificate (BinarySecurityToken) to the message and attach pointer to Signature:
        $token = $wsse->addBinaryToken(file_get_contents($this->publicKeyFile));
        $wsse->attachTokentoSig($token);

        // Add end-to-end encryption if configured:
        if ($this->encrypt) {
            $key = new XMLSecurityKey(XMLSecurityKey::AES256_CBC);
            $key->generateSessionKey();
            $siteKey = new XMLSecurityKey(XMLSecurityKey::RSA_OAEP_MGF1P, ['type' => 'public']);
            $siteKey->loadKey($this->serverCertificateFile, true, true);
            $wsse->encryptSoapDoc($siteKey, $key, [
                'KeyInfo' => [
                    'X509SubjectKeyIdentifier' => $this->serverCertificateHasSubjectKeyIdentifier,
                ]
            ]);
        }

        $request = $request->withBody($xml->toStream());

        return $handler($request, $options);
    }

    /**
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     */
    public function afterResponse(ResponseInterface $response)
    {
        if (!$this->encrypt) {
            return $response;
        }

        $xml = SoapXml::fromStream($response->getBody());
        $wsse = new WSSESoap($xml->getXmlDocument());
        $wsse->decryptSoapDoc(
            $xml->getXmlDocument(),
            [
                'keys' => [
                    'private' => [
                        'key'    => $this->privateKeyFile,
                        'isFile' => true,
                        'isCert' => false,
                    ]
                ]
            ]
        );

        return $response->withBody($xml->toStream());
    }
}
