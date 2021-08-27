<?php
declare(strict_types=1);

namespace Phpro\SoapClient\Caller;

use Phpro\SoapClient\Exception\SoapException;
use Phpro\SoapClient\Soap\Engine\EngineInterface;
use Phpro\SoapClient\Type\MixedResult;
use Phpro\SoapClient\Type\MultiArgumentRequestInterface;
use Phpro\SoapClient\Type\RequestInterface;
use Phpro\SoapClient\Type\ResultInterface;
use Phpro\SoapClient\Type\ResultProviderInterface;

final class EngineCaller implements Caller
{
    private EngineInterface $engine;

    public function __construct(EngineInterface $engine)
    {
        $this->engine = $engine;
    }

    public function __invoke(string $method, RequestInterface $request): ResultInterface
    {
        try {
            $arguments = ($request instanceof MultiArgumentRequestInterface) ? $request->getArguments() : [$request];
            $result = $this->engine->request($method, $arguments);

            if ($result instanceof ResultProviderInterface) {
                $result = $result->getResult();
            }

            if (!$result instanceof ResultInterface) {
                $result = new MixedResult($result);
            }
        } catch (\Exception $exception) {
            throw SoapException::fromThrowable($exception);
        }

        return $result;
    }
}
