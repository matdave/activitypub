<?php
namespace MatDave\ActivityPub\Model;

use MODX\Revolution\modResource;
use phpseclib3\Crypt\PublicKeyLoader;
use phpseclib3\Crypt\RSA;
use xPDO\xPDO;

/**
 * Class Actor
 *
 * @property string $type
 * @property integer $user
 * @property boolean $manuallyApprovesFollowers
 * @property string $username
 * @property string $fullname
 * @property string $profile
 * @property string $icon
 *
 * @property \Activity[] $Activities
 * @property \Follower[] $Followers
 *
 * @package MatDave\ActivityPub\Model
 */
class Actor extends \xPDO\Om\xPDOSimpleObject
{
    public function get($k, $format = null, $formatTemplate= null) {
        $get = parent::get($k,$format, $formatTemplate);
        if ($k === 'icon') {
            $this->prefixUrl($get);
        }
        if ($k === 'profile') {
            $this->parseValue($get);
            $this->prefixUrl($get);
        }
        return $get;
    }

    public function getIconMime() {
        $icon = $this->icon;
        $parts = explode(".", $icon);
        $extension = array_pop($parts);
        switch (strtolower($extension)) {
            case 'jpg':
            case 'jpeg':
                return 'image/jpeg';
            case 'gif':
                return 'image/gif';
            case 'svg':
                return 'image/svg+xml';
            case 'webp':
                return 'image/webp';
            default:
                return 'image/png';
        }
    }

    private function parseValue(&$value) {
        if (preg_match('/\[\[/', $value)) {
            // parse the profile url
            $resource = $this->xpdo->getObject(modResource::class, $this->xpdo->getOption('site_start'));
            if (!empty($resource)) {
                $this->xpdo->setOption('link_tag_scheme', 'full');
                $this->xpdo->resource = $resource;
                $this->xpdo->resourceIdentifier = $resource->get('id');
                $this->xpdo->elementCache = [];
                $this->xpdo->parser->processElementTags('', $value, false, false, '[[', ']]', [], 10);
                $this->xpdo->parser->processElementTags('', $value, true, false, '[[', ']]', [], 10);
                $this->xpdo->parser->processElementTags('', $value, true, true, '[[', ']]', [], 10);
            } else {
                $value = '';
            }
        }
    }

    private function prefixUrl(&$url) {
        if (!preg_match('/^http(s|)?:\/\//', $url)) {
            $url = rtrim($this->xpdo->getOption('site_url'), '/')  . '/' . ltrim($url, '/');
        }
    }

    public function getPublicKey()
    {
        $privateKey = $this->get('privatekey');
        if (empty($privateKey)) {
            $this->generatePrivateKey();
        }
        $publicKey = $this->get('publickey');
        return $publicKey;
    }

    private function generatePrivateKey(): void
    {
        $key = RSA::createKey();
        $key->withHash('sha256');
        $this->set('privatekey', $key);
        $this->set('publickey', $key->getPublicKey());
        $this->save();
    }
}
