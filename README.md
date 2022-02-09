# PHP Library for editing an image

Quick repo for easy handling of image

Will work both with GD and Imagick Extensions.

Removes all Exif data.

### Composer

To install using [Composer](http://getcomposer.org/)

`composer require deployhuman/PHPImageHandler dev-main`

## Getting Started

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');

$image = new Image('c:\filepath\image.jpg');

$image->resize(100,200,true);
$image-saveToFile('c:\filepath\newimage.png');

?>
```

