<?php

namespace MatDave\ActivityPub\Api\Controllers\ActivityStream;

use ActivityPhp\Server;
use JsonException;
use MatDave\ActivityPub\Api\Configuration;
use MatDave\ActivityPub\Api\Controllers\Restful;
use MatDave\ActivityPub\Api\Exceptions\RestfulException;
use MatDave\ActivityPub\Utils\Signatures\MessageVerifier;
use MODX\Revolution\modX;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use function Aws\default_user_agent;

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
        $verified = false;
        if ($type && $actor) {
            $verifier = new MessageVerifier();
            $keyId = $verifier->getKeyId($request);
            if (empty($keyId)) {
                throw RestfulException::badRequest(['error' => 'Key not found']);
            }
            $publicKeyPem = $verifier->getPublicKeyPem($this->requestFactory, $this->client, $keyId);
            if (empty($publicKeyPem)) {
                throw RestfulException::badRequest(['error' => 'Public key not found']);
            }
            $verified = $verifier->verify($request, $publicKeyPem);
        }

        if ($verified) {
            switch ($type) {
                case 'Follow':
                    $this->handleFollow($actor, $object);
                    break;
                case 'Undo':
                    $this->handleUndo($actor, $object);
                    break;
                default:
                    $this->modx->log(1, "unhandled type: " . $type . " - " . $object);
                    $verified = false;
            }
        }
        return $this->respondWithItem($request, ['success' => $verified]);
    }

    private function handleFollow(string $actor, string $object)
    {
        $nodeActor = $this->modx->getObject(Actor::class, []);
    }

    private function handleUndo(string $actor, string $object)
    {

    }
}