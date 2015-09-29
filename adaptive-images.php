<?php

namespace alpipego\adaptiveImages;

use QuanDigital\WpLib\Autoload;

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

new Plugin(__FILE__);