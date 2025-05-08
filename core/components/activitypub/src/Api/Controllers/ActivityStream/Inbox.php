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
    
    public function get(ServerRequestInterface $request): ResponseInterface
    {
        $defaultParams = [
            'page' => 0,
        ];
        $paramsCast = [
            'page' => 'int'
        ];
        $params = $this->getParams($request, $defaultParams, $paramsCast, []);
        $basePath = rtrim($this->modx->config['site_url'], '/');
        $apiPath = $basePath . $this->config->get('base_path_manage', '/activitypub');

        if ($request->getAttribute('alias')) {
            $condition = ['username' => $request->getAttribute('alias')];
            $actor = $this->modx->getObject(\MatDave\ActivityPub\Model\Actor::class, $condition);
            if (!$actor) {
                throw RestfulException::notFound();
            }
            $owner = $apiPath . '/users/' . $actor->get('username');
        } else {
            $owner = $apiPath;
        }
        $total = 0;
        if ($params['page'] > 0) {
            $response = [
                "@context" => "https://www.w3.org/ns/activitystreams",
                "id" => $owner . "/inbox?page=1",
                "type" => "OrderedCollectionPage",
                "totalItems" => $total,
                "partOf" => $owner . "/inbox",
                "orderedItems" => []
            ];
        } else {
            $response = [
                "@context" => "https://www.w3.org/ns/activitystreams",
                "id" => $owner . "/inbox",
                "type" => "OrderedCollection",
                "totalItems" => $total,
                "first" => $owner . "/inbox?page=1"
            ];
        }
        return $this->respondWithItem($request, $response);
    }

    private function handleFollow(string $actor, string $object)
    {
        $nodeActor = $this->modx->getObject(Actor::class, []);
    }

    private function handleUndo(string $actor, string $object)
    {

    }
}