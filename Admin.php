<?php
    
namespace alpipego\adaptiveImages;

use QuanDigital\WpLib\Helpers;

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
        if (preg_match("%https?://w{3}\.?flickr\.com/photos/.[^/]+/(\d+)/%", $url, $matches)) {
            return (int) $matches[1];
        } else {
            throw new \Exception('Not a valid flickr.com url');  
        }
    }

    function idOrUrl($idOrUrl)
    {
        if (is_numeric($idOrUrl)) {
            return (int) $idOrUrl;
        } else {
            try {
                return $this->parseUrl($idOrUrl);
            } catch (\Exception $e) {
                throw $e;
            }
        }
    }

    function ajaxIdOrUrl() 
    {
        try {
            echo $this->idOrUrl($_GET['idOrUrl']);
        } catch (\Exception $e) {
            echo json_encode([
                'error' => [
                    'msg' => $e->getMessage(),
                ],
            ]);
        }

        wp_die();
    }

    function getFlickrImage()
    {
        $flickr = new Flickr($_GET['id']);

        try {
            echo json_encode($flickr->getImage());
        } catch (\Exception $e) {
            echo json_encode([
                'error' => [
                    'msg' => $e->getMessage(),
                ],
            ]);
        }
        
        wp_die();
    }

    function getUnsplashImage()
    {
        
    }
}
