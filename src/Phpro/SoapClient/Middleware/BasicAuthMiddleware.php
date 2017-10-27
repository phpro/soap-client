<?php

namespace Phpro\SoapClient\Middleware;

use Http\Promise\Promise;
use Psr\Http\Message\RequestInterface;

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

    public function __construct(string $username, string $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    public function getName(): string
    {
        return 'basic_auth_middleware';
    }

    public function beforeRequest(callable $handler, RequestInterface $request): Promise
    {
        $request = $request->withHeader(
            'Authorization',
            sprintf('Basic %s', base64_encode(
                sprintf('%s:%s', $this->username, $this->password)
            ))
        );

        return $handler($request);
    }
}
