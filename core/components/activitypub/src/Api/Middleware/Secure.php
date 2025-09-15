<?php

namespace MatDave\ActivityPub\Api\Middleware;

use MatDave\ActivityPub\Api\{Configuration, Exceptions\RestfulException};
use Psr\{Http\Message\ResponseInterface, Http\Message\ServerRequestInterface, Http\Server\RequestHandlerInterface};
use Slim\Psr7\Response;

class Secure
{
    /**
     * @var \MatDave\ActivityPub\Api\Configuration
     */
    private $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }


    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return Response
     *
     * @throws RestfulException
     */
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->validate($request);

        $request = $request->withAttribute('checkParamLimits', false);
        return $handler->handle($request);
    }

    /**
     * Validate a request for a Restful route.
     *
     * @param ServerRequestInterface $request
     *
     * @throws RestfulException
     */
    private function validate(ServerRequestInterface $request)
    {
        $buildAuth = $this->configuration->get('buildAuth');
        if (empty($buildAuth['username']) || empty($buildAuth['password'])) {
            throw RestfulException::forbidden();
        }

        $auth = 'Basic ' . base64_encode("{$buildAuth['username']}:{$buildAuth['password']}");
        $header = $request->getHeaderLine('Authorization');

        if (empty($header)) {
            throw RestfulException::unauthorized();
        }

        if ($auth !== $header) {
            throw RestfulException::forbidden();
        }
    }
}
