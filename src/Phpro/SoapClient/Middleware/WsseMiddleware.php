<?php

namespace Phpro\SoapClient\Middleware;

use Http\Promise\Promise;
use Phpro\SoapClient\Xml\SoapXml;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RobRichards\WsePhp\WSSESoap;
use RobRichards\XMLSecLibs\XMLSecurityKey;

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

    public function __construct(string $privateKeyFile, string $publicKeyFile)
    {
        $this->privateKeyFile = $privateKeyFile;
        $this->publicKeyFile = $publicKeyFile;
    }

    public function getName(): string
    {
        return 'wsse_middleware';
    }

    public function withTimestamp(int $timestamp = 3600): self
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    public function withAllHeadersSigned(): self
    {
        $this->signAllHeaders = true;

        return $this;
    }

    public function withDigitalSignMethod(string $digitalSignMethod): self
    {
        $this->digitalSignMethod = $digitalSignMethod;

        return $this;
    }

    public function withUserToken(string $username, string $password = null, $digest = false): self
    {
        $this->hasUserToken = true;
        $this->userTokenName = $username;
        $this->userTokenPassword = $password;
        $this->userTokenDigest = $digest;

        return $this;
    }

    public function withEncryption(string $serverCertificateFile): self
    {
        $this->encrypt = true;
        $this->serverCertificateFile = $serverCertificateFile;

        return $this;
    }

    public function withServerCertificateHasSubjectKeyIdentifier(bool $hasSubjectKeyIdentifier): self
    {
        $this->serverCertificateHasSubjectKeyIdentifier = $hasSubjectKeyIdentifier;

        return $this;
    }

    public function beforeRequest(callable $handler, RequestInterface $request): Promise
    {
        $xml = SoapXml::fromString((string)$request->getBody());
        $wsse = new WSSESoap($xml->getXmlDocument());

        // Prepare the WSSE soap class:
        $wsse->signAllHeaders = $this->signAllHeaders;
        $wsse->addTimestamp($this->timestamp);

        // Add a user token if this is configured.
        if ($this->hasUserToken) {
            $wsse->addUserToken($this->userTokenName, $this->userTokenPassword, $this->userTokenDigest);
        }

        //  Add certificate (BinarySecurityToken) to the message
        $token = $wsse->addBinaryToken(file_get_contents($this->publicKeyFile));

        // Create new XMLSec Key using the dsigType and type is private key
        $key = new XMLSecurityKey($this->digitalSignMethod, ['type' => 'private']);
        $key->loadKey($this->privateKeyFile, true);
        $wsse->signSoapDoc($key);

        //  Attach token pointer to Signature:
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

        return $handler($request);
    }

    public function afterResponse(ResponseInterface $response): ResponseInterface
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
