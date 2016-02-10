<?php

namespace Alpipego\AdaptiveImages;

use Alpipego\AdaptiveImages\Cache\Cache;

define('WP_USE_THEMES', false);
require_once($_SERVER['DOCUMENT_ROOT'] . '/wp/wp-load.php');

// include __DIR__ . '/Unsplash.php';
// include __DIR__ . '/Cache.php';

// $unsplash = new Unsplash('YnaPN0dl4A');

// echo '<code><pre>';
//     var_dump($unsplash->getPhoto());
//     var_dump($unsplash->getImage(400));
// echo '</pre></code>';

$cache = new Cache();
$request = [
        'action' => 'ai_load_image',
        'id' => 'eqsEZNCm4-c',
        'service' => 'unsplash',
        'width' => 412
    ];
$result = ['result' => 'this is result'];

// $cacheHit = $cache->get($request);
// if ($cacheHit) {
//     echo '<code><pre>';
//         var_dump($cacheHit);
//     echo '</pre></code>';
// } else {
//     $res = $cache->set($request, $result);
// }

// $cache->purge();

echo '<code><pre>';
    var_dump($cache->getAll());
echo '</pre></code>';

// $cache->add(serialize([
//     'id' => 'eqsEZNCm4-c',
//     'service' => 'unsplash',
//     'width' => 412
// ]));
