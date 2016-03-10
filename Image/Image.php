<?php

namespace Alpipego\AdaptiveImages\Image;

use Alpipego\AdaptiveImages\Cache\Cache;

class Image
{
    // private $imageId;
    // private $image;
    // public $url;
    // public $width;
    // public $height;
    // public $ratio;

    // function __construct($id)
    // {
    //     $this->imageId = $id;
    //     $this->image = \wp_get_attachment_image_src($id, 'full');
    //     $this->url = $this->image[0];
    //     $this->width = $this->image[1];
    //     $this->height = $this->image[2];
    //     $this->ratio = $this->getRatio();
    // }

    // function getRatio()
    // {
    //     return $this->width/$this->height;
    // }

    public static function get($data)
    {
        $serviceClass = 'Alpipego\\AdaptiveImages\\Services\\' . ucfirst($data['service']);
        $service = new $serviceClass($data['id']);

        $cache = new Cache();
        $request = [
            'id' => $data['id'],
            'service' => $data['service'],
            'width' => $data['width'],
        ];
        $result = $cache->get($request);

        if (!$result) {
            try {
                $result = $service->getImageDetails();
                $result['image'] = $service->getSize($data['width']);
                $cache->set($request, $result);
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

        error_log(date('d.m.Y H:i:s', strtotime('now')) . ":\n" . print_r($result, true) . "\n", 3, trailingslashit(WP_CONTENT_DIR) . 'debug.log');

        return $result;
    }
}
