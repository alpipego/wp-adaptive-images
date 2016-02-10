<?php

namespace Alpipego\AdaptiveImages\Admin;

use QuanDigital\WpLib\Boilerplate;
use Alpipego\AdaptiveImages\Admin\Admin;
use Alpipego\AdaptiveImages\Image\AdaptiveImages;

class Plugin extends Boilerplate
{
    function __construct($file)
    {
        parent::__construct($file);

        \add_action('wp_enqueue_scripts', function() use ($file) {
            \wp_enqueue_script('ai', plugins_url('js/adaptiveImages.js', $file), ['jquery'], '1.0.0', true);
        });

        \add_action('admin_enqueue_scripts', function($hook) use ($file) {
            \wp_register_script('ai', plugins_url('js/adaptiveImages.js', $file), ['jquery'], '1.0.0', true);
            \wp_register_script('aiFunctions', plugins_url('js/_aiFunctions.js', $file), ['jquery'], '1.0.0', true);

            if ($hook === 'post.php') {
                \wp_enqueue_script('aiAdmin', plugins_url('js/aiAdmin.js', $file), ['jquery', 'aiFunctions'], '1.0.0', true);
            }
        });

        \add_action('wp_ajax_ai_validate_url', [new Admin(), 'ajaxValidateUrl']);
        \add_action('wp_ajax_ai_get_image', [new Admin(), 'ajaxGetImage']);

        \add_action('wp_ajax_ai_load_image', [new AdaptiveImages(), 'getBestImage']);
        \add_action('wp_ajax_nopriv_ai_load_image', [new AdaptiveImages(), 'getBestImage']);

        \add_action('_admin_menu', [$this, 'setOptionsPage']);
    }

    function setOptionsPage()
    {
        \acf_add_options_page([
            'page_title'    => 'Adaptive Images',
            'menu_title'    => 'Adaptive Images',
            'menu_slug'     => 'plugin-adaptive-images',
            'capability'    => 'manage_options',
            'redirect'      => false,
        ]);
    }
}
