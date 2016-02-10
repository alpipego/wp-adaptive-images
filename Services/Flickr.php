<?php

namespace Alpipego\AdaptiveImages\Services;

use phpFlickr;

class Flickr extends AbstractService
{
    public $id;
    private $f;
    private $apiKey;
    private $apiSecret;
    private $info;

    public function __construct($id)
    {
        $this->apiKey = \get_field('ai_flickr_api_key', 'option');
        $this->apiSecret = \get_field('ai_flickr_api_secret', 'option');

        $this->id = $id;

        $this->connection();
        $this->info = $this->getPhoto();
    }

    protected function connection()
    {
        if (!empty($this->apiKey) && !empty($this->apiSecret)) {
            $this->f = new phpFlickr($this->apiKey, $this->apiSecret);
            $this->f->enableCache('db', 'mysql://'.DB_USER.':'.DB_PASSWORD.'@'.DB_HOST.'/'.DB_NAME, $this->cacheAge());
        } else {
            throw new \Exception('Please provide a flickr API key and secret');
        }
    }

    private function cacheAge()
    {
        if ($env = (defined('WP_STAGE') || defined('WP_ENV'))) {
            switch ($env) {
                case 'local':
                    return 600;
                    break;
                default:
                    return 3600;
                    break;
            }
        } else {
            return 3600;
        }
    }

    function getSizes()
    {
        return $this->f->photos_getSizes($this->id);
    }

    function getSize($specifiedSize)
    {
        $sizes = $this->getSizes();

        foreach ($sizes as $size) {
            if (strtolower($size['label']) === strtolower($specifiedSize)) {
                return $size['source'];
            }
        }
    }

    function getPhoto()
    {
        $info = $this->f->photos_getInfo($this->id);

        if ($info['stat'] == 'ok') {
            return $info['photo'];
        }
    }

    function getLicense()
    {
        $licenses = $this->f->photos_licenses_getInfo();
        $imageLicense = $this->info['license'];
        $allowedLicenses = ['4', '5', '7', '8', '9', '10'];

        if (in_array($imageLicense, $allowedLicenses)) {
            foreach ($licenses as $license) {
                if (in_array($imageLicense, $license)) {
                    return $this->license = $license['url'];
                }
            }
        } else {
            throw new \Exception('All rights reserved. Please choose another image.');

        }
    }

    function getImageDetails()
    {
        return [
            'licenseUrl' => $this->getLicense(),
            'userUrl' => $this->getUser()['url'],
            'userName' => $this->getUser()['name'],
        ];
    }

    function getUrl()
    {
        if (is_array($this->info['urls']['url'])) {
            if (in_array('photopage', $this->info['urls']['url'][0])) {
                return $this->info['urls']['url'][0]['_content'];
            }
        }
    }

    function getUser()
    {
        $userInfo = [];
        $userInfo['url'] = $this->f->urls_getUserProfile($this->info['owner']['nsid']);

        if (empty($this->info['owner']['realname'])) {
            $userInfo['name'] = $this->info['owner']['username'];
        } else {
            $userInfo['name'] = $this->info['owner']['realname'];
        }

        return $userInfo;
    }
}
