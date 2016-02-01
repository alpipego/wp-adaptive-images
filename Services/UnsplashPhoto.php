<?php

namespace Alpipego\AdaptiveImages\Services;

use Crew\Unsplash\Photo;

class UnsplashPhoto extends Photo
{
    public static function findSize($id, $width)
    {
        $photo = json_decode(self::get("photos/{$id}", ['query' => ['w' => $width]])->getBody(), true);

        return $photo['urls']['custom'];
    }
}
