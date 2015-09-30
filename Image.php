<?php

    namespace alpipego\adaptiveImages;

    class Image
    {
        private $imageId;
        private $image;
        public $url;
        public $width;
        public $height;
        public $ratio;

        function __construct($id)
        {
            $this->imageId = $id;
            $this->image = \wp_get_attachment_image_src($id, 'full');
            $this->url = $this->image[0];
            $this->width = $this->image[1];
            $this->height = $this->image[2];
            $this->ratio = $this->getRatio();
        }

        function getRatio()
        {
            return $this->width/$this->height;
        }
    }