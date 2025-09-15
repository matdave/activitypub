<?php

namespace MatDave\ActivityPub\Api\Controllers\ActivityStream;

use MatDave\ActivityPub\{Api\Configuration,
    Api\Controllers\Restful,
    Api\Exceptions\RestfulException,
    Model\Actor as ActorAlias,
    Model\Follower,
    Utils\Signatures\MessageVerifier};
use MODX\Revolution\modX;
use Psr\{Http\Client\ClientInterface,
    Http\Message\RequestFactoryInterface,
    Http\Message\ResponseInterface,
    Http\Message\ServerRequestInterface};

class Inbox extends Restful
{
    /**
     * Reference:
     * - ActivityPub Delivery method https://www.w3.org/TR/activitypub/#delivery
     * - Linked Delivery Notification payload https://www.w3.org/TR/ldn/#payload
     */

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
     */
    public function post(ServerRequestInterface $request): ResponseInterface
    {
        $data = $request->getParsedBody();
        $type = $data['type'] ?? null;
        if (empty($type) && !empty($data['@type'])) {
            $type = $data['@type'];
        }
        $actor = $data['actor'] ?? null;
        $object = $data['object'] ?? null;
        if (empty($object) && !empty($data['@id'])) {
            $object = $data['@id'];
        }
        if (empty($object)) {
            $params = $this->getParams($request);
            if (!empty($params['alias'])) {
                $object = $params['alias'];
            } else {
                throw RestfulException::badRequest(['error' => 'Object not found']);
            }
        }
        $objectType = $this->determineObjectType($object);
        if (empty($objectType)) {
            throw RestfulException::badRequest(['error' => 'Object type not found']);
        }
        $object = $this->handleObjectType($objectType, $object);
        if (empty($object)) {
            throw RestfulException::badRequest(['error' => 'Object not found']);
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
                    $this->modx->log(1, json_encode($data));
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
            $actor = $this->modx->getObject(ActorAlias::class, $condition);
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
        $nodeActor = $this->modx->getObject(
            ActorAlias::class,
            ['username' => $object]
        );
        if (!$nodeActor) {
            throw RestfulException::notFound();
        }
        $follower = $this->modx->getObject(
            Follower::class,
            ['actor' => $nodeActor->id, 'user' => $actor]
        );
        if (!$follower) {
            $follower = $this->modx->newObject(Follower::class);
            $follower->set('actor', $nodeActor->id);
            $follower->set('user', $actor);
            $approved = $nodeActor->get('manuallyApprovesFollowers') === false;
            $follower->set('approved', $approved);
            $follower->save();
        }
    }

    private function handleUndo(string $actor, string $object)
    {

    }

    private function determineObjectType(string $object): ?string
    {
        if (empty($object)) {
            return null;
        }
        // check if starts with http
        if (str_starts_with($object, 'http')) {
            return 'link';
        }
        return 'alias';
    }

    private function handleObjectType(string $objectType, string $object): ?string
    {
        if ($objectType === 'link') {
            $segments = explode('/', trim($object, '/'));
            $object = array_pop($segments);
        }
        return $object;
    }
}