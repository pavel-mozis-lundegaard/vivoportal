<?php
namespace Vivo\Util;

/**
 * Images class provides methods to works with images as is resample.
 */
class Images
{

    /**
     * Default optons
     * @var array
     */
    protected $defaultOptions = array(
        'size' => null,
        'height' => null,
        'width' => null,
        'bw' => false,
        'crop' => false,
        'bgcolor' => null,
        'radius' => 0,
        'outputType' => 'image/jpeg',
        'quality' => 95,
        'transparency' => null, //0 = full opaque, 127 = full transparent.
        'useTransparency' => false,
    );

    /**
     * Resample image
     * @param string $inputFile Input file path
     * @param string $outputFile Output file path
     * @param array $options Resampling options (@see $defaulOptions)
     * @throws \Exception
     */
    public function resample($inputFile, $outputFile, array $options = array())
    {
        if (!file_exists($inputFile)) {
            throw new \Exception(sprintf('%s: Input file doesn\'t exists.', __METHOD__));
        }
        $options = array_merge($this->defaultOptions, $options);

        //load image
        $image = $this->loadImage($inputFile, $options);

        //setup transparency
        if ($options['transparency'] === null) {
            if (!$options['bgcolor']) {
                $options['transparency'] = 127;
            } else {
                $options['transparency'] = 0;
            }
        }
        if ($options['imageType'] == IMAGETYPE_PNG) {
            $options['useTransparency'] = true;
        }

        //convert to black&white
        if ($options['bw'] == true) {
            imagefilter($image, IMG_FILTER_GRAYSCALE);
        }

        //resize
        if ($options['size'] || $options['height'] || $options['width']) {
            $image = $this->resize($image, $options);
        }

        //round corners
        if ($options['radius']) {
            $image = $this->roundCorners($image, $options);
        }

        $this->saveImage($image, $outputFile, $options);
    }

    /**
     * Save image to file.
     * @param type $image
     * @param type $path
     * @param type $options
     * @throws Exception
     */
    protected function saveImage($image, $path, $options)
    {
        switch ($options['outputType']) {
            case 'image/png':
                imagepng($image, $path);
                break;
            case 'image/gif':
                imagegif($image, $path);
                break;
            case 'image/jpeg':
                imagejpeg($image, $path, $options['quality']);
                break;
            default:
                throw new \Exception(sprintf('%s: Unsupported output type.', __METHOD__));
        }
    }

    /**
     * Function creates a transparent rounded corners to image.
     *
     * @param resource $inputImage Input image
     * @param array $options
     * @return resource Image with rounded and transparent corners
     */
    protected function roundCorners($inputImage, array $options)
    {
        $width = imagesx($inputImage);
        $height = imagesy($inputImage);
        $color = $options['bgcolor'];
        $transparency = $options['transparency'];
        $radius = $options['radius'];

        $outputImage = imagecreatetruecolor($width, $height);
        imagecopy($outputImage, $inputImage, 0, 0, 0, 0, $width, $height);
        imagealphablending($outputImage, false);
        imagesavealpha($outputImage, true);

        $full_color = $this->allocateCornerColor($outputImage, $color, $transparency);

        // loop 4 times, for each corner...
        for ($left = 0; $left <= 1; $left++) {
            for ($top = 0; $top <= 1; $top++) {
                $start_x = $left * ($width - $radius);
                $start_y = $top * ($height - $radius);
                $end_x = $start_x + $radius;
                $end_y = $start_y + $radius;

                $radius_origin_x = $left * ($start_x - 1) + (!$left) * $end_x;
                $radius_origin_y = $top * ($start_y - 1) + (!$top) * $end_y;

                for ($x = $start_x; $x < $end_x; $x++) {
                    for ($y = $start_y; $y < $end_y; $y++) {
                        $dist = sqrt(pow($x - $radius_origin_x, 2) + pow($y - $radius_origin_y, 2));

                        if ($dist > ($radius + 1)) {
                            imagesetpixel($outputImage, $x, $y, $full_color);
                        } else {
                            if ($dist > $radius) {
                                $pct = 1 - ($dist - $radius);
                                $color2 = $this->antialiasPixel($outputImage, $x, $y, $full_color, $pct);
                                imagesetpixel($outputImage, $x, $y, $color2);
                            }
                        }
                    }
                }
            }
        }

        return $outputImage;
    }

    /**
     * Allocates the color the image corners to be filled with.
     * @param resource $image Input image
     * @param mixed $color String HEX color to use or bool false to create a random one
     * @param int $transparency Transparency level 0..127
     * @return int A color identifier or false if the allocation failed
     */
    protected function allocateCornerColor($image, $color, $transparency)
    {
        $r = $g = $b = '';
        if (!$color) {
            $found = false;
            while ($found == false) {
                $r = rand(0, 255);
                $g = rand(0, 255);
                $b = rand(0, 255);

                if (imagecolorexact($image, $r, $g, $b) != -1) {
                    $found = true;
                }
            }
        } else {
            $r = hexdec(substr($color, 0, 2));
            $g = hexdec(substr($color, 2, 2));
            $b = hexdec(substr($color, 4, 2));
        }

        if ($transparency > 127) {
            $transparency = 127;
        }

        if ($transparency <= 0) {
            return imagecolorallocate($image, $r, $g, $b);
        } else {
            return imagecolorallocatealpha($image, $r, $g, $b, $transparency);
        }
    }

    /**
     * Does antialiasing for rounded corners to be smoother.
     * @param resource $image Input image
     * @param int $x X position of the pixel to be antialiased
     * @param int $y Y position of the pixel to be antialiased
     * @param mixed $color String HEX color to use or bool false to create a random one.
     * @param float $weight Index of transparency (0.0..1.0) for antialiasing
     * @return int A color identifier or false if the allocation failed
     */
    protected function antialiasPixel($image, $x = 0, $y = 0, $color = false, $weight = 0.5)
    {
        $c = imagecolorsforindex($image, $color);
        $r1 = $c['red'];
        $g1 = $c['green'];
        $b1 = $c['blue'];
        $t1 = $c['alpha'];
        $color2 = imagecolorat($image, $x, $y);
        $c = imagecolorsforindex($image, $color2);
        $r2 = $c['red'];
        $g2 = $c['green'];
        $b2 = $c['blue'];
        $t2 = $c['alpha'];

        $cweight = $weight + ($t1 / 127) * (1 - $weight) - ($t2 / 127) * (1 - $weight);

        $r = round($r2 * $cweight + $r1 * (1 - $cweight));
        $g = round($g2 * $cweight + $g1 * (1 - $cweight));
        $b = round($b2 * $cweight + $b1 * (1 - $cweight));
        $t = round($t2 * $weight + $t1 * (1 - $weight));

        return imagecolorallocatealpha($image, $r, $g, $b, $t);
    }

    /**
     * Resize image
     * @param type $inputImage
     * @param type $options
     */
    protected function resize($inputImage, $options)
    {
        $width = $options['width'];
        $height = $options['height'];
        $size = $options['size'];
        $bgcolor = $options['bgcolor'];
        $crop = $options['crop'];
        $useTransparency = $options['useTransparency'];

        $h1 = imagesy($inputImage);
        $w1 = imagesx($inputImage);

        if ($size) {
            $c = $size / ($w1 > $h1 ? $w1 : $h1);
            $w2 = floor($w1 * $c);
            $h2 = floor($h1 * $c);
            $outputImage = imagecreatetruecolor($w2, $h2);

            imagecopyresampled($outputImage, $inputImage, 0, 0, 0, 0, $w2, $h2, $w1, $h1);

            if ($useTransparency) {
                imagecolortransparent($outputImage, imagecolorallocatealpha($outputImage, 0, 0, 0, 127));
                imagealphablending($outputImage, false);
                imagesavealpha($outputImage, true);
            }
        } elseif ($width && $height && $crop && $w1 / $h1 != $width / $height) {
            $outputImage = imagecreatetruecolor($width, $height);

            // find the right scale to use
            $scale = min((float) ($w1 / $width), (float) ($h1 / $height));
            // coords to crop
            $cropX = (float) ($w1 - ($scale * $width));
            $cropY = (float) ($h1 - ($scale * $height));
            // cropped image size
            $cropW = (float) ($w1 - $cropX);
            $cropH = (float) ($h1 - $cropY);
            $i3 = imagecreatetruecolor($cropW, $cropH);

            if ($useTransparency) {
                imagecolortransparent($outputImage, imagecolorallocatealpha($outputImage, 0, 0, 0, 127));
                imagealphablending($outputImage, false);
                imagesavealpha($outputImage, true);

                imagealphablending($i3, false);
                $color = imagecolortransparent($i3, imagecolorallocatealpha($i3, 0, 0, 0, 127));
                imagefill($i3, 0, 0, $color);
                imagesavealpha($i3, true);
            }

            // crop the middle part of the image to fit proportions
            imagecopy($i3, $inputImage, 0, 0, (int) ($cropX / 2), (int) ($cropY / 2), $cropW, $cropH);
            imagecopyresampled($outputImage, $i3, 0, 0, 0, 0, $width, $height, $cropW, $cropH);
            imagedestroy($i3);
        } elseif ($width || $height) {
            if (!is_numeric($width)) {//preserves aspect ratio
                $width = $height / imagesy($inputImage) * imagesx($inputImage);
            }
            if (!is_numeric($height)) {//preserves aspect ratio
                $height = $width / imagesx($inputImage) * imagesy($inputImage);
            }

            $outputImage = imagecreatetruecolor($width, $height);

            if ($bgcolor) {
                if ($useTransparency) {
                    imagealphablending($outputImage, false);
                    $color = imagecolortransparent($outputImage,
                            imagecolorallocate($outputImage, hexdec(substr($bgcolor, 0, 2)),
                                    hexdec(substr($bgcolor, 2, 2)), hexdec(substr($bgcolor, 4, 2))));
                    imagefill($outputImage, 0, 0, $color);
                    imagesavealpha($outputImage, true);
                } else {
                    imagefill($outputImage, 0, 0,
                            imagecolorallocate($outputImage, hexdec(substr($bgcolor, 0, 2)),
                                    hexdec(substr($bgcolor, 2, 2)), hexdec(substr($bgcolor, 4, 2))));
                }
            } elseif ($useTransparency) {
                imagealphablending($outputImage, false);
                $color = imagecolortransparent($outputImage, imagecolorallocatealpha($outputImage, 0, 0, 0, 127));
                imagefill($outputImage, 0, 0, $color);
                imagesavealpha($outputImage, true);
            }

            $cw = $width / $w1;
            $ch = $height / $h1;
            $w2 = floor($w1 * ($cw > $ch ? $ch : $cw));
            $h2 = floor($h1 * ($cw > $ch ? $ch : $cw));

            imagecopyresampled($outputImage, $inputImage, floor($width / 2 - $w2 / 2), floor($height / 2 - $h2 / 2), 0,
                    0, $w2, $h2, $w1, $h1);
        }
        return $outputImage;
    }

    protected function loadImage($fileName, &$options)
    {
        list($w, $h, $imageType) = getimagesize($fileName);
        $options['imageType'] = $imageType;
        switch ($imageType) {
            case IMAGETYPE_GIF:
                $imageResource = imagecreatefromgif($fileName);
                break;
            case IMAGETYPE_JPEG:
                $imageResource = imagecreatefromjpeg($fileName);
                break;
            case IMAGETYPE_PNG:
                $imageResource = imagecreatefrompng($fileName);
                break;
            default:
                throw new \Exception('Unsupported file type. ');
                break;
        }
        return $imageResource;
    }

}

