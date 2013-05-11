<?php

namespace Vivo\Util;

/**
 * Images class provides methods to works with images as is resample.
 *
 * @todo: PNG quality if > 7...
 */
class Images {

    public function resample($inputFile, $options) {
        if (!file_exists($inputFile)) {
            throw new \Exception('File not exist');
        }

        $image = $this->loadImage($inputFile);

        //convert to black&white
        if ($options['bw'] == true) {
            imagefilter($image, IMG_FILTER_GRAYSCALE);
        }

        //resize
        if ($options['size'] > 0 || $options['height'] || $options['width']) {
            $image = $this->resize($image, $options);
        }

        //round corners

        if (!$options['bgcolor'] && $png) {
            $transparency = 127;
        } else {
            $transparency = 0;
        }

        if ($options['radius'] > 0) {
            $image = $this->roundCorners($image, $options['radius'], $options['bgcolor'], $transparency);
        }

        $this->saveImage($image, $path);

        return $outputFile;
    }

    protected function saveImage($image, $path) {
        //TODO
    }

    /**
     * Function creates a transparent rounded corners to image.
     *
     * @param resource $inputImage Input image
     * @param int $radius Radius of rounded corners
     * @param string $color Color to make transparent
     * @param int $transparency Transparency. 0 = full opaque, 127 = full transparent.
     * @return resource Image with rounded and transparent corners
     */
    public function roundCorners($inputImage, $radius = 20, $color = false, $transparency = 127) {
        $width = imagesx($inputImage);
        $height = imagesy($inputImage);

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
    public function allocateCornerColor($image, $color, $transparency) {
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

        if ($transparency > 127)
            $transparency = 127;

        if ($transparency <= 0)
            return imagecolorallocate($image, $r, $g, $b);
        else
            return imagecolorallocatealpha($image, $r, $g, $b, $transparency);
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
    protected function antialiasPixel($image, $x = 0, $y = 0, $color = false, $weight = 0.5) {
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
    protected function resize($inputImage, $options) {

        $width   = $options['width'];
        $height  = $options['height'];
        $size    = $options['size'];
        $bgcolor = $options['bgcolor'];
        $cropX    = $options['crop'];

        $isPNG  = true; //TODO

        $h1 = imagesy($inputImage);
        $w1 = imagesx($inputImage);

        if ($size) {
            $c = $size / ($w1 > $h1 ? $w1 : $h1);
            $w2 = floor($w1 * $c);
            $h2 = floor($h1 * $c);
            $i2 = imagecreatetruecolor($w2, $h2);

            imagecopyresampled($i2, $inputImage, 0, 0, 0, 0, $w2, $h2, $w1, $h1);

            if ($isPNG) {
                imagecolortransparent($i2, imagecolorallocatealpha($i2, 0, 0, 0, 127));
                imagealphablending($i2, false);
                imagesavealpha($i2, true);

//                if (!$bgcolor) {
//                    $transparency = 127;
//                }
            }
//            if ($radius > 0) {
//                $i2 = self::roundCorners($i2, $radius, $bgcolor, $transparency);
//            }
        } elseif ($width && $height && $cropX && $w1 / $h1 != $width / $height) {
            $i2 = imagecreatetruecolor($width, $height);

            // find the right scale to use
            $scale = min((float) ($w1 / $width), (float) ($h1 / $height));
            // coords to crop
            $cropX = (float) ($w1 - ($scale * $width));
            $cropY = (float) ($h1 - ($scale * $height));
            // cropped image size
            $cropW = (float) ($w1 - $cropX);
            $cropH = (float) ($h1 - $cropY);
            $i3 = imagecreatetruecolor($cropW, $cropH);

            if ($isPNG) {
                imagecolortransparent($i2, imagecolorallocatealpha($i2, 0, 0, 0, 127));
                imagealphablending($i2, false);
                imagesavealpha($i2, true);

                imagealphablending($i3, false);
                $color = imagecolortransparent($i3, imagecolorallocatealpha($i3, 0, 0, 0, 127));
                imagefill($i3, 0, 0, $color);
                imagesavealpha($i3, true);

//                if (!$bgcolor) {
//                    $transparency = 127;
//                }
            }

            // crop the middle part of the image to fit proportions
            imagecopy($i3, $inputImage, 0, 0, (int) ($cropX / 2), (int) ($cropY / 2), $cropW, $cropH);
            imagecopyresampled($i2, $i3, 0, 0, 0, 0, $width, $height, $cropW, $cropH);
            imagedestroy($i3);

//            if ($radius > 0) {
//                $i2 = self::roundCorners($i2, $radius, $bgcolor, $transparency);
//            }
        } elseif ($width || $height) {
            if (!is_numeric($width)) {//preserves aspect ratio
                $width = $height / imagesy($inputImage) * imagesx($inputImage);
            }
            if (!is_numeric($height)) {//preserves aspect ratio
                $height = $width / imagesx($inputImage) * imagesy($inputImage);
            }

            $i2 = imagecreatetruecolor($width, $height);

            if ($bgcolor) {
                if ($isPNG) {
                    imagealphablending($i2, false);
                    $color = imagecolortransparent($i2, imagecolorallocate($i2, hexdec(substr($bgcolor, 0, 2)), hexdec(substr($bgcolor, 2, 2)), hexdec(substr($bgcolor, 4, 2))));
                    imagefill($i2, 0, 0, $color);
                    imagesavealpha($i2, true);
                } else {
                    imagefill($i2, 0, 0, imagecolorallocate($i2, hexdec(substr($bgcolor, 0, 2)), hexdec(substr($bgcolor, 2, 2)), hexdec(substr($bgcolor, 4, 2))));
                }
            } elseif ($isPNG) {
                imagealphablending($i2, false);
                $color = imagecolortransparent($i2, imagecolorallocatealpha($i2, 0, 0, 0, 127));
                imagefill($i2, 0, 0, $color);
                imagesavealpha($i2, true);

//                $transparency = 127;
            }

            $cw = $width / $w1;
            $ch = $height / $h1;
            $w2 = floor($w1 * ($cw > $ch ? $ch : $cw));
            $h2 = floor($h1 * ($cw > $ch ? $ch : $cw));

            imagecopyresampled($i2, $inputImage, floor($width / 2 - $w2 / 2), floor($height / 2 - $h2 / 2), 0, 0, $w2, $h2, $w1, $h1);

//            if ($radius > 0) {
//                if ($bgcolor)
//                    $i2 = self::roundCorners($i2, $radius, $bgcolor, $transparency);
//                else
//                    $i2 = self::roundCorners($i2, $radius, false, $transparency);
//            }
        }
    }

    protected function loadImage($fileName) {
        list($w, $h, $imageType) = getimagesize($fileName);

        switch ($imageType) {
            case IMAGETYPE_GIF:
                $imageResource = imagecreatefromgif($fileName);
                break;
            case IMAGETYPE_JPG:
            case IMAGETYPE_JPEG:
                $imageResource = imagecreatefromjpeg($fileName);
                break;
            case IMAGETYPE_PNG:
                $imageResource = imagecreatefrompng($fileName);
                break;
            default:
                throw new \Exception('Unsupported file type ' . $image_type);
                break;
        }
        return $imageResource;
    }

}

