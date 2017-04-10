<?php

namespace Phpro\SoapClient\Middleware;

use Psr\Http\Message\RequestInterface;

/**
 * Class BasicAuthMiddleware
 *
 * @package Phpro\SoapClient\Middleware
 */
class BasicAuthMiddleware extends Middleware
{
    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * NtlmMiddleware constructor.
     *
     * @param string $username
     * @param string $password
     */
    public function __construct(string $username, string $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'basic_auth_middleware';
    }

    /**
     * {@inheritdoc}
     * @throws \InvalidArgumentException
     */
    public function beforeRequest(callable $handler, RequestInterface $request, array $options)
    {
        $request = $request->withHeader(
            'Authorization',
            sprintf('Basic %s', base64_encode(
                sprintf('%s:%s', $this->username, $this->password)
            ))
        );

        return $handler($request, $options);
    }
}
