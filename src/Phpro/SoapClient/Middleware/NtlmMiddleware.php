<?php

namespace Phpro\SoapClient\Middleware;

use Http\Promise\Promise;
use Phpro\SoapClient\Exception\RuntimeException;
use Psr\Http\Message\RequestInterface;

/**
 * @deprecated This middleware is deprecated since we moved from guzzle to HTTPlug.
 * @todo Decide if we will remove this class or use a low-level NTLM implementation instead.
 */
class NtlmMiddleware extends Middleware
{
    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    public function __construct(string $username, string $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    public function getName(): string
    {
        return 'ntlm_middleware';
    }

    public function beforeRequest(callable $handler, RequestInterface $request): Promise
    {
        throw new RuntimeException(
            'The NTLM middleware is deprecated since we moved from guzzle to httplug clients.' . PHP_EOL
            . 'You need to configure NTLM on your HTTP client. This can be done with following curl options:' . PHP_EOL
            . PHP_EOL
            . 'CURLOPT_HTTPAUTH = CURLAUTH_NTLM' . PHP_EOL
            . 'CURLOPT_USERPWD = "' . $this->username . ':' . $this->password . '"' . PHP_EOL
        );
    }
}
