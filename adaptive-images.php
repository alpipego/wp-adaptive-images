<?php

namespace alpipego\adaptiveImages;

use QuanDigital\WpLibs\Autload;

/**
 * Plugin Name: Adaptive Images for WordPress
 * Plugin URI: https://github.com/alpipego/wp-adaptive-images
 * Description: A custom take on adaptive images in WordPress
 * Author: alpipego
 * Version: 1.0.0
 * Author URI: http://alpipego.com/
 * License: MIT
 */

new Autoload(__DIR__, __NAMESPACE__);


    function ai_image($id)
    {
        $image = new \adaptiveImages\Image($id);
        return $image->ratio;
    }

    function ai_resize($id, $ratio)
    {
        $resize = new \adaptiveImages\Resize($id);
        return $resize->resizeByRatio($ratio);
    }

    function ai_flickr_test($url)
    {
        $flickr = new \adaptiveImages\Flickr($url);
        return $flickr;
    }

    function ai_flickr($url)
    {
        $flickr = new \adaptiveImages\Flickr($url);
        return $flickr->getImage();
    }

    function ai_admin($value)
    {
        $valid = true;
        $admin = new \adaptiveImages\Admin();
        
        try {
            $admin->idOrUrl($value);
        } catch (Exception $e) {
            $valid = $e->getMessage();
        }
        return $valid;
    }

    function ai_flickr_ajax()
    {
        $flickr = new \adaptiveImages\Flickr($_POST['id']);
        $size = $_POST['containerWidth'] * $_POST['pixelRatio'];
        $availSizesArr = $flickr->getSizes();

        $closest = null;

        foreach ($availSizesArr as $key => $availSizes) {
            $width = (int) $availSizes['width'];
            if ($closest == null || abs($size - $closest) > abs($width - $size)) {
                $closest = $key;
            }
        }

        echo base64_encode($availSizesArr[$closest]['source']);
        wp_die();
    }

    add_action('wp_ajax_ai_load_image', 'ai_flickr_ajax');
    add_action('wp_ajax_nopriv_ai_load_image', 'ai_flickr_ajax');


    add_action( 'wp_enqueue_scripts', function() {
        wp_enqueue_script( 'ai_flickr', plugins_url('adaptiveFlickr.js', __FILE__), array('jquery'), '1.0.0', true );
    });

    add_filter('acf/validate_value/name=flickr_url_id', 'ai_validate_flickr_url_id', 10, 4);

    function ai_validate_flickr_url_id($valid, $value, $field, $input)
    {
        $admin = new \adaptiveImages\Admin();

        // bail early if value is already invalid
        if(!$valid) {
            return $valid;
        }
        
        try {
            $admin->idOrUrl($value);
        } catch (Exception $e) {
            $valid = $e->getMessage();
        }
        
        // return
        return $valid;
    }    
    