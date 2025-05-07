<?php

namespace MatDave\ActivityPub\Utils;

use phpseclib3\Crypt\PublicKeyLoader;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Request;

class Signature
{
    public const SIGNATURE_PATTERN = '/^
        keyId="(?P<keyId>
            (https?:\/\/[\w\-\.]+[\w]+)
            (:[\d]+)?
            ([\w\-\.#\/@]+)
        )",
        (algorithm="(?P<algorithm>[\w\s-]+)",)?
        (headers="\(request-target\) (?P<headers>[\w\s-]+)",)?
        signature="(?P<signature>[\w+\/]+={0,2})"
    /x';/**
 * Allowed keys when splitting signature
 *
 * @var array
 */
    private $allowedKeys = [
        'keyId',
        'algorithm', // optional
        'headers',   // optional
        'signature',
    ];

    /** @var ClientInterface  */
    protected $client;

    /** @var RequestFactoryInterface  */
    protected $requestFactory;

    /**
     * Inject a server instance
     */
    public function __construct(
        ClientInterface $client,
        RequestFactoryInterface $requestFactory
    )
    {
        $this->client = $client;
        $this->requestFactory = $requestFactory;
    }

    /**
     * Verify an incoming message based upon its HTTP signature
     *
     * @param  \Psr\Http\Message\ServerRequestInterface $request
     * @return bool True if signature has been verified. Otherwise false
     */
    public function verify(ServerRequestInterface $request)
    {
        // Read the Signature header,
        $signature = $request->getHeaderLine('Signature');

        if (!$signature) {
            return 'Signature header not found';
        }

        // Split it into its parts (keyId, headers and signature)
        $parts = $this->splitSignature($signature);
        if (!count($parts)) {
            return 'Empty signature';
        }

        extract($parts);

        // Build a server-oriented actor
        // Fetch the public key linked from keyId

        $publicKeyPem = $this->getPublicKeyPem($keyId);

        if (empty($publicKeyPem)) {
            return 'No Public Key';
        }

        // Create a comparison string from the plaintext headers we got
        // in the same order as was given in the signature header,
        if (empty($headers)) {
            return "No headers to compare";
        }
        $data = $this->getPlainText(
            explode(" ", $headers),
            $request
        );

        // Verify that string using the public key and the original
        // signature.
        $rsa = PublicKeyLoader::loadPublicKey($publicKeyPem)
            ->withHash('sha256');

        if($rsa->verify($data, base64_decode($signature, true))) {
            return 'Signature verified';
        } else {
            return 'Signatured failed: ' . $data;
        }
    }

    /**
     * Split HTTP signature into its parts (keyId, headers and signature)
     */
    public function splitSignature(string $signature): array
    {
        if (!preg_match(self::SIGNATURE_PATTERN, $signature, $matches)) {
            return [];
        }

        // Headers are optional
        if (!isset($matches['headers']) || $matches['headers'] == '') {
            $matches['headers'] = 'date';
        }

        return array_filter($matches, function($key) {
            return !is_int($key) && in_array($key, $this->allowedKeys);
        },  ARRAY_FILTER_USE_KEY );
    }

    /**
     * Get plain text that has been originally signed
     *

        ClientInterface::class => function (ContainerInterface $c) {
            return new Client(['timeout' => 10]);
        },

        RequestFactoryInterface::class => function (ContainerInterface $c) {
            $modx = $c->get(modX::class);
            return $modx->services->get(RequestFactoryInterface::class);
        }
     * @param  array $headers HTTP header keys
     * @param  \Psr\Http\Message\ServerRequestInterface $request
     */
    private function getPlainText(array $headers, ServerRequestInterface $request): string
    {
        $strings = [];
        $strings[] = sprintf(
            '(request-target) %s %s',
            strtolower($request->getMethod()),
            $request->getRequestTarget()
        );

        foreach ($headers as $key) {
            if ($request->getHeader($key)) {
                $strings[] = "$key: " . strtolower($request->getHeaderLine($key));
            }
        }

        return implode("\n", $strings);
    }

    private function getPublicKeyPem(string $keyId)
    {
        $url = explode("#", $keyId)[0] ?? null;
        if (empty($url) || empty($keyId)) {
            return null;
        }
        $test = $this->requestFactory->createRequest('GET', $url)
            ->withHeader('Accept', 'application/activity+json');
        try {
            $request = $this->client->sendRequest($test);
            if ($request->getStatusCode() !== 200) {
                return null;
            }
            $actor = $request->getBody()->getContents();
            $actor = json_decode($actor, true);
            if (
                !isset($actor['publicKey'])
            ) {
                return null;
            }
            if (isset($actor['publicKey']['id']) && $actor['publicKey']['id'] === $keyId) {
                    return  $actor['publicKey']['publicKeyPem'] ?? null;
            } else {
                if (is_array($actor['publicKey'])) {
                    foreach ($actor['publicKey'] as $key) {
                        if ($key['id'] === $keyId) {
                            return $key['publicKeyPem'] ?? null;
                        }
                    }
                }
            }

        } catch (\Throwable $e) {
            return null;
        }
        return null;
    }
}