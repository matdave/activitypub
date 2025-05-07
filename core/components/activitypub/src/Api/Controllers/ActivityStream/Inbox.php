<?php

namespace MatDave\ActivityPub\Api\Controllers\ActivityStream;

use ActivityPhp\Server;
use JsonException;
use MatDave\ActivityPub\Api\Configuration;
use MatDave\ActivityPub\Api\Controllers\Restful;
use MatDave\ActivityPub\Api\Exceptions\RestfulException;
use MatDave\ActivityPub\Utils\Signature;
use MODX\Revolution\modX;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Inbox extends Restful
{

    /** @var \Psr\Http\Client\ClientInterface */
    protected $client;
    /** @var RequestFactoryInterface  */
    protected $requestFactory;
    public function __construct(
        modX $modx,
        \DI\FactoryInterface $factoryInterface,
        Configuration $config,
        ClientInterface $client,
        RequestFactoryInterface $requestFactory,
    )
    {
        $this->client = $client;
        $this->requestFactory = $requestFactory;
        return parent::__construct($modx, $factoryInterface, $config);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     * @throws RestfulException
     * @throws JsonException
     */
    public function post(ServerRequestInterface $request): ResponseInterface
    {
        $data = $request->getParsedBody();
        $type = $data['type'] ?? null;
        $actor = $data['actor'] ?? null;
        $object = $data['object'] ?? null;
        if (empty($object)) {
            $params = $this->getParams($request);
            if (!empty($params['alias'])) {
                $object = $params['alias'];
            } else {
                throw RestfulException::badRequest(['error' => 'Object not found']);
            }
        }
        $response = '';
        if ($type && $actor) {
            $httpSignature = new Signature($this->client, $this->requestFactory);
            $response = $httpSignature->verify($request);
            $this->modx->log(1, $request->getHeaderLine('Signature'));
            $this->modx->log(1, $httpSignature->verify($request));
        }
        return $this->respondWithItem($request, ['resp' => $response]);
    }
}