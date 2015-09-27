<?php
    
    namespace adaptiveImages;

    class Resize
    {
        private $image;

        function __construct($id)
        {
            $this->image = new Image($id);
        }

        private function process($url, $width)
        {
            return \aq_resize($url, $width);
        }

        public function resizeByRatio($ratio)
        {
            if (is_string($ratio)) {
                $ratioArr = explode(':', $ratio);
                $ratio = $ratioArr[0]/$ratioArr[1];
            }

            return aq_resize($this->image->url, $this->image->width, round($ratio * $this->image->width), true);
        }
    }
