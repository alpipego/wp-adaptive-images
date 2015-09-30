<?php
    
    namespace Alpipego\AdaptiveImages;

    use phpFlickr;

    class Flickr
    {
        public $id;
        public $license;
        public $url;
        private $f;
        private $apiKey;
        private $apiSecret;
        private $info;

        public function __construct($idOrUrl)
        {
            $this->apiKey = \get_field('ai_flickr_api_key', 'option');
            $this->apiSecret = \get_field('ai_flickr_api_secret', 'option'); 

            $this->id = (new Admin())->idOrUrl($idOrUrl);

            if ($this->id && !($this->id instanceof \Exception)) {
                $this->connection();
                $this->info = $this->getPhotoInfo();
            } else {
                throw new \Exception('Please provide a valid url or image id');
            }
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

        protected function getPhotoInfo()
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
            $allowedLicenses = ['4', '5', '8', '9', '10'];

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

        function getImage()
        {
            return [
                'license' => $this->getLicense(),
                'photopage' => $this->getUrl(),
                'user' => $this->getUser(),
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
            if (empty($this->info['owner']['realname'])) {
                return $this->info['owner']['username'];
            }

            return $this->info['owner']['realname'];
        }
    }
