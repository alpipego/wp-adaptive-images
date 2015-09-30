<?php

namespace alpipego\adaptiveImages;

class AdaptiveImages
{
    public function __construct()
    {
        \add_action('wp_ajax_ai_load_image', [$this, 'ai_flickr_ajax']);
        \add_action('wp_ajax_nopriv_ai_load_image', [$this, 'ai_flickr_ajax']);

        \add_filter('acf/validate_value/name=ai_url_id', [$this, 'ai_validate_flickr_url_id'], 10, 4);
    }

    function ai_flickr_ajax()
    {
        $flickr = new Flickr($_POST['id']);
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

    function ai_validate_flickr_url_id($valid, $value, $field, $input)
    {
        $admin = new Admin();

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

    
}