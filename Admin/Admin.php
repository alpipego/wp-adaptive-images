<?php

namespace Alpipego\AdaptiveImages\Admin;

use QuanDigital\WpLib\Helpers;
use Alpipego\AdaptiveImages\Cache\Cache;


class Admin
{
    function __construct()
    {
        \add_action('intermediate_image_sizes_advanced', [$this, 'removeDefaultSizes'], 10, 1);
    }

    function removeDefaultSizes($defaultSizes)
    {
        $sizes = $this->getImageSizes();

        foreach ($sizes as $size) {
            unset($defaultSizes[$size]);
        }

        return $defaultSizes;
    }

    function getImageSizes()
    {
        return \get_intermediate_image_sizes();
    }

    function parseUrl($url)
    {
        $url = \trailingslashit($url);

        if (preg_match('%https?://w{3}\.?flickr\.com/photos/.[^/]+/(\d+)/?%', $url, $matches)) {
            return [
                'service' => 'flickr',
                'id' => (int) $matches[1],
            ];
        } elseif (preg_match('%https?://unsplash\.com/photos/(.+?)(?=/)%', $url, $matches)) {
            return [
                'service' => 'unsplash',
                'id' => $matches[1],
            ];
        } else {
            throw new \Exception('Not a valid url');
        }
    }

    function ajaxValidateUrl()
    {
        try {
            echo json_encode($this->parseUrl($_GET['url']));
        } catch (\Exception $e) {
            if ($e->getMessage() !== '') {
                $msg = $e->getMessage();
            } else {
                $msg = 'Some undefined error occurred';
            }
            echo json_encode([
                'error' => [
                    'msg' => $msg
                ],
            ]);
        }

        wp_die();
    }

    function ajaxGetImage()
    {
        $serviceClass = 'Alpipego\\AdaptiveImages\\Services\\' . ucfirst($_GET['image']['service']);
        $service = new $serviceClass($_GET['image']['id']);

        $cache = new Cache();
        $request = [
            'id' => $_GET['image']['id'],
            'service' => $_GET['image']['service'],
            'width' => 'admin',
        ];
        $result = $cache->get($_GET);

        if (!$result) {
            try {
                $result = $service->getImageDetails();
                $result['image'] = $service->getSize('small');
                $cache->set($_GET, $result);
            } catch (\Exception $e) {
                $msg = $e->getMessage();
                if (empty($msg)) {
                    $msg = 'Some undefined error occurred';
                }
                $result = [
                    'error' => [
                        'msg' => $msg
                    ],
                ];
            }
        }

        echo json_encode($result);
        wp_die();
    }

}
