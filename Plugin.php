<?php

namespace alpipego\adaptiveImages;

use QuanDigital\WpLib\Boilerplate;

class Plugin extends Boilerplate
{
    function __construct($file)
    {
        parent::__construct($file);

        \add_action('wp_enqueue_scripts', function() use ($file) {
            \wp_enqueue_script('ai_flickr', plugins_url('js/adaptiveFlickr.js', $file), array('jquery'), '1.0.0', true);
        });
    }
}