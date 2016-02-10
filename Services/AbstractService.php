<?php

namespace Alpipego\AdaptiveImages\Services;

abstract class AbstractService
{
    abstract function getPhoto();

    abstract function getImageDetails();

    abstract function getSize($size);
}
