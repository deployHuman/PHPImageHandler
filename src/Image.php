<?php

namespace DeployHuman\PHPImageHandler;

use DeployHuman\PHPImageHandler\Enum\ImageLib;
use Imagick;

class Image
{
    protected ImageLib $SelectedLib;
    protected $resource;

    public function __construct(string|Image $image, ImageLib $preferedImagelib = Imagelib::gd)
    {
        $this->checkSystem();
        $this->SelectedLib = $preferedImagelib;
        $this->$this->resource = $this->loadImage($image);
        if ($this->resource === false) {
            throw new \Exception('Image could not be loaded');
        }
    }

    private function checkSystem(): never
    {
        if (version_compare(PHP_VERSION, '8.1.0', '<')) {
            throw new \Exception('PHP 8.1.0 or higher required');
        }
        if (!extension_loaded('gd') && !extension_loaded('imagick')) {
            throw new \Exception('No image library available');
        }
        if (!extension_loaded($this->SelectedLib->value)) {
            throw new \Exception('Selecte image library is not supported');
        }
    }

    private function loadImage(string $data)
    {
        if (is_string($data)) {
            if (file_exists($data)) {
                return $this->loadFromFile($data);
            }
        }
        if (Validation::base64($data)) {
            return $this->loadFromBase64($data);
        }
        return false;
    }

    private function loadFromBase64(string $data)
    {
        $data = base64_decode($data);
        switch ($this->SelectedLib) {
            case Imagelib::gd:
                $image = imagecreatefromstring($data);
                if ($image === false) return false;
                return $image;
                break;

            case Imagelib::imagick:
                $image = new \Imagick();
                $image->readImageBlob($data);
                return $image;

                break;

            default:
                return false;
                break;
        }
    }

    private function loadFromFile(string $file)
    {
        switch ($this->SelectedLib) {
            case Imagelib::gd:
                $info = getimagesize($file);
                $type = $info[2];
                if ($type == IMAGETYPE_JPEG) {
                    return imagecreatefromjpeg($file);
                } else if ($type == IMAGETYPE_GIF) {
                    return imagecreatefromgif($file);
                } else {
                    return imagecreatefrompng($file);
                }
                break;

            case Imagelib::imagick:
                $image = new \Imagick();
                $image->readImage($file);
                return $image;

                break;

            default:
                return false;
                break;
        }
    }


    public function getWidth(): int
    {
        switch ($this->SelectedLib) {
            case Imagelib::gd:
                return imagesx($this->resource);
                break;

            case Imagelib::imagick:
                /**
                 * @var Imagick $localresource
                 */
                $localresource = $this->resource;
                return $localresource->getImageWidth();
                break;

            default:
                return false;
                break;
        }
    }

    public function getHeight(): int
    {
        switch ($this->SelectedLib) {
            case Imagelib::gd:
                return imagesy($this->resource);
                break;

            case Imagelib::imagick:
                /**
                 * @var Imagick $localresource
                 */
                $localresource = $this->resource;
                return $localresource->getImageHeight();
                break;

            default:
                return false;
                break;
        }
    }

    public function getType(): int
    {
        switch ($this->SelectedLib) {
            case Imagelib::gd:
                return image_type_to_extension(image_type_to_mime_type($this->resource), false);
                break;

            case Imagelib::imagick:
                /**
                 * @var Imagick $localresource
                 */
                $localresource = $this->resource;
                return $localresource->getImageFormat();
                break;

            default:
                return false;
                break;
        }
    }

    public function resize(int $maxWidth, int $maxHeight, bool $upscale = false): bool
    {
        switch ($this->SelectedLib) {
            case Imagelib::gd:
                imagecopyresampled($this->resource, $this->resource, 0, 0, 0, 0, $maxWidth, $maxHeight, $this->getWidth(), $this->getHeight());
                return true;
                break;

            case Imagelib::imagick:
                /**
                 * @var Imagick $localresource
                 */
                $localresource = $this->resource;
                $localresource->adaptiveResizeImage($maxWidth, $maxHeight, $upscale);
                $this->resource = $localresource;
                return true;
                break;

            default:
                return false;
                break;
        }
    }

    public function saveToFile(string $filename, int $quality = 100): bool
    {
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        switch ($this->SelectedLib) {
            case Imagelib::gd:
                if ($ext == 'jpg' || $ext == 'jpeg') {
                    return imagejpeg($this->resource, $filename, $quality);
                } else if ($ext == 'gif') {
                    return imagegif($this->resource, $filename);
                } else {
                    return imagepng($this->resource, $filename);
                }
                break;

            case Imagelib::imagick:
                /**
                 * @var Imagick $localresource
                 */
                $localresource = $this->resource;
                $localresource->setImageCompressionQuality($quality);

                if ($ext == 'jpg' || $ext == 'jpeg') {
                    $localresource->setImageFormat('jpeg');
                } else if ($ext == 'gif') {
                    $localresource->setImageFormat('gif');
                } else {
                    $localresource->setImageFormat('png');
                }

                $localresource->writeImage($filename);
                return true;
                break;

            default:
                return false;
                break;
        }
    }
}
