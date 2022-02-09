<?php

namespace DeployHuman\PHPImageHandler\Enum;

enum ImageLib: string
{

    /**
     * The GD library.
     * @link https://www.php.net/manual/en/book.image.php
     */
    case gd = 'gd';

    /**
     * The Imagick library.
     * @link https://www.php.net/manual/en/book.imagick.php
     */
    case imagick = 'imagick';
}
