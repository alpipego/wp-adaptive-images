<?php
    
    namespace alpipego\adaptiveImages;

    use phpFlickr;

    class Flickr
    {
        public $id;
        public $license;
        public $url;
        private $f;
        private $api_key;
        private $api_secret;
        private $info;

        function __construct($idOrUrl)
        {
            $this->connection();

            $this->id = $this->idOrUrl($idOrUrl);

            // $this->imageId = (int) $this->parseUrl();
            $this->api_key = \get_option('ai_flickr_api');
            $this->api_secret = \get_option('ai_flickr_api_secret');

            $this->info = $this->getPhotoInfo();
        }

        function connection()
        {
            $this->f = new phpFlickr($this->api_key, $this->api_secret);
            $this->f->enableCache('db', 'mysql://'.DB_USER.':'.DB_PASSWORD.'@'.DB_HOST.'/'.DB_NAME, 600);
        }

        function getSizes()
        {
            return $this->f->photos_getSizes($this->id);
        }

        function getPhotoInfo()
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
                return 'not allowed';
            } 
            /*else {
                throw new \Exception('You can\'t use this license on this page. Please choose another image.', 1);
                
            }*/
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
            if (in_array('photopage', $this->info['urls']['url'][0])) {
                return $this->info['urls']['url'][0]['_content'];
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
