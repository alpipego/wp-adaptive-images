<?php

namespace Alpipego\AdaptiveImages\Image;

use QuanDigital\WpLib\Helpers;

use Alpipego\AdaptiveImages\Services\Flickr;
use Alpipego\AdaptiveImages\Services\Unsplash;
use Alpipego\AdaptiveImages\Cache\Cache;

class AdaptiveImages
{

    public static function getBestImage()
    {
        session_start(); // starting the session
        session_write_close(); // close the session file and release the lock

        $result = ['error' => [ 'msg' => 'Something is wrong']];
        if (!empty($_GET['service']) && !empty($_GET['id'])) {
            $cache = new Cache();
            $result = $cache->get($_GET);

            if (!$result) {
                $class = 'getBest'.$_GET['service'];
                $result = self::$class($_GET);
                $cache->set($_GET, $result);
            }
        }

        echo json_encode($result);
        wp_die();
    }

    public static function getBestFlickr($data)
    {
        $flickr = new Flickr($data['id']);
        $availSizesArr = $flickr->getSizes();

        foreach ($availSizesArr as $key => $availSizes) {
            if ($availSizes['label'] === 'Original') {
                $src = $availSizes['source'];
                break;
            }
        }

        return 'https://images1-focus-opensocial.googleusercontent.com/gadgets/proxy?url=' . urlencode($src) . '&container=focus&resize_w=' . $data['width'] . '&refresh=' . 30 * 24 * 60 * 60;
    }

    public static function getBestUnsplash($data)
    {
        $unsplash = new Unsplash($data['id']);
        return $unsplash->getImage($data['width']);
    }
}
