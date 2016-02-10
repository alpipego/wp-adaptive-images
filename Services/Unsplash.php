<?php

namespace Alpipego\AdaptiveImages\Services;

use Crew\Unsplash\HttpClient;
use QuanDigital\WpLib\Helpers;

class Unsplash extends AbstractService
{
    public function __construct($id)
    {
        HttpClient::init([
            'applicationId' => \get_field('ai_unsplash_api_key', 'option'),
            'secret' => \get_field('ai_unsplash_api_secret', 'option'),
        ]);

        $this->id = $id;
    }

    public function getPhoto()
    {
        try {
            return UnsplashPhoto::find($this->id);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            if (empty($e)) {
                error_log(date('H:i:s', strtotime('now')) . ":\n" . print_r(['Provider Error', $e->getTraceAsString()], true) . "\n", 3, trailingslashit(WP_CONTENT_DIR) . 'debug.log');
            }
        }
    }

    public function getImageDetails()
    {
        $photo = $this->getPhoto();

        return [
            'licenseUrl' => 'https://creativecommons.org/publicdomain/zero/1.0/',
            'userUrl' => $photo->user['links']['html'],
            'userName' => $photo->user['name'],
        ];
    }

    public function getSize($requestedSize)
    {
        $sizes = $this->getPhoto()->urls;
        foreach ($sizes as $size => $url) {
            if (strtolower($size) === strtolower($requestedSize)) {
                return $url;
            }
        }
    }

    public function getImage($width)
    {
        try {
            return UnsplashPhoto::findSize($this->id, $width);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            if (empty($e)) {
                error_log(date('H:i:s', strtotime('now')) . ":\n" . print_r(['Provider Error', $e->getTraceAsString()], true) . "\n", 3, trailingslashit(WP_CONTENT_DIR) . 'debug.log');
            }
        }
    }
}
