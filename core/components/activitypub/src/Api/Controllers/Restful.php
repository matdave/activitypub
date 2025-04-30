<?php

namespace MatDave\ActivityPub\Api\Controllers;

use MODX\Revolution\modX;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;
use MatDave\ActivityPub\Api\Configuration;
use MatDave\ActivityPub\Api\Exceptions\RestfulException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use MatDave\ActivityPub\Api\Transformers\Transformer;
use MatDave\ActivityPub\Api\Transformers\xPDOObjectTransformer;
use MatDave\ActivityPub\Api\TypeCast\Caster;

abstract class Restful implements RequestHandlerInterface
{
    /** @var \DI\FactoryInterface */
    private $factoryInterface;

    /** @var int */
    private int $expires = 3600;

    protected static $transformer = xPDOObjectTransformer::class;

    /** @var modX */
    protected $modx;

    /** @var Configuration */
    protected $config;

    protected $cacheResponse = true;

    public function __construct(modX $modx, \DI\FactoryInterface $factoryInterface, Configuration $config)
    {
        $this->factoryInterface = $factoryInterface;
        $this->modx = $modx;
        $this->config = $config;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $action = static::class;
        $method = strtolower($request->getMethod());
        if (method_exists($this, $method)) {
            return $this->{$method}($request);
        }

        throw RestfulException::notImplemented();
    }

    /**
     * Merge url params and query params
     *
     * @param  ServerRequestInterface  $request
     * @param  array  $defaultParams
     * @param  array  $paramsCast
     *
     * @return array
     * @throws \MatDave\ActivityPub\Api\Exceptions\RestfulException
     */
    protected function getParams(ServerRequestInterface $request, array $defaultParams = [], array $paramsCast = [], array $paramLimits = []): array
    {
        $urlParams = $request->getAttribute('params', '');

        $parsedParams = [];

        if ($urlParams !== null) {
            $params = explode('/', $urlParams);
            foreach ($params as $param) {
                $exploded = explode(':', $param);
                if (isset($exploded[1])) {
                    $parsedParams[$exploded[0]] = $exploded[1];
                }
            }
        }

        $allParams = array_merge($defaultParams, $request->getQueryParams(), $parsedParams);

        try {
            Caster::castArray($allParams, $paramsCast);
        } catch (\Exception $e) {
            throw RestfulException::internalServerError(['message' => $e->getMessage()]);
        }

        $checkParamLimits = $request->getAttribute('checkParamLimits', true);
        if ($checkParamLimits && !empty($paramLimits)) {
            foreach ($paramLimits as $key => $limits) {
                if (!isset($allParams[$key])) {
                    continue;
                }

                foreach ($limits as $name => $value) {
                    switch ($name) {
                        case 'min':
                            if ($allParams[$key] < $value) {
                                throw RestfulException::badRequest(['query' => $key]);
                            }
                            break;
                        case 'max':
                            if ($allParams[$key] > $value) {
                                throw RestfulException::badRequest(['query' => $key]);
                            }
                            break;
                    }
                }
            }
        }

        return $allParams;
    }

    /**
     * @param ServerRequestInterface $request
     * @param array|\Iterator $collection
     * @param Transformer|null $transformer
     * @param array $meta
     * @param array $params
     * @return ResponseInterface
     */
    protected function respondWithCollection(ServerRequestInterface $request, $collection, $transformer = null, array $transformerParams = [], array $meta = [], array $params = []): ResponseInterface
    {
        $transformer = $this->factoryInterface->make($transformer ?: static::$transformer);
        $data = $transformer->transformCollection($collection, $transformerParams);

        $total = $meta['total'] ?? count($data);
        $returned = count($data);
        $page = (isset($params['page'])) ? (int)$params['page'] : 1;
        $limit = (isset($params['limit'])) ? (int)$params['limit'] : 0;

        $hasMore = false;
        if ($limit !== 0) {
            $hasMore = (($page - 1) * $limit + $returned) < $total;
        }

        return $this->respond($request, [
            'total' => (int)$total,
            'hasMore' => $hasMore,
            'returned' => (int)$returned,
            'params' => $params,
            'data' => $data
        ]);
    }

    protected function respondWithItem(
        ServerRequestInterface $request,
        $item,
        $transformer = null,
        array $transformerParams = []
    ): ResponseInterface {
        $transformer = $this->factoryInterface->make($transformer ?: static::$transformer);
        $data = $transformer->transformItem($item, $transformerParams);

        return $this->respond($request, $data);
    }

    protected function respond(ServerRequestInterface $request, array $data): ResponseInterface
    {
        $response = new Response();
        $response->getBody()->write(json_encode($data));
        if ($this->cacheResponse &&
            $request->getMethod() === 'GET'
        ) {
            $response = $response->withHeader('Cache-Control', 'public, max-age='. $this->expires)
            ->withHeader('Expires', gmdate('D, d M Y H:i:s \G\M\T', time() + $this->expires))
            ->withHeader('Pragma', 'cache');
        } else {
            $response = $response->withHeader('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->withHeader('Expires', gmdate('D, d M Y H:i:s \G\M\T', time() - $this->expires))
            ->withHeader('Pragma', 'no-cache');
        }
        return $response
            ->withHeader('Content-Type', 'application/json;charset=utf-8');
    }

    protected function idOrAlias(ServerRequestInterface $request): array
    {
        $alias = $request->getAttribute('alias');
        $condition = ['id' => $request->getAttribute('id')];

        if ($alias !== null) {
            $condition = ['alias' => $alias];
        }
        return $condition;
    }

    protected function cacheResponse(bool $cacheResponse = true): void
    {
        $this->cacheResponse = $cacheResponse;
    }
}
