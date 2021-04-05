<?php

class ImageRenderer {
    
    public static function renderDatei(Datei $datei, $ci, $width, $height, $format) {
        $im = imagecreatefromjpeg($datei->fullpath);
        $widthO = ImagesX($im);
        $heightO = ImagesY($im);

        switch ($ci) {
            case "c":
                $im2 = ImageCreateTrueColor($width, $height);
                $color_white = imagecolorallocatealpha($im2, 255, 255, 255, 127);
                imagealphablending($im2, false);
                imagefilledrectangle($im2, 0, 0, $width, $height, $color_white);
                imagealphablending($im2, true);
                $k = 0;
                $k = max($k, $width/$widthO, $height/$heightO);
                ImageCopyResized($im2, $im, 0-($widthO*$k-$width)/2, 0-($heightO*$k-$height)/2, 0, 0, $widthO*$k, $heightO*$k, $widthO, $heightO);
                imagedestroy($im);
                $im = $im2;
                break;
            case "i":
                $im2 = ImageCreateTrueColor($width, $height);
                $color_white = imagecolorallocatealpha($im2, 255, 255, 255, 127);
                imagealphablending($im2, false);
                imagefilledrectangle($im2, 0, 0, $width, $height, $color_white);
                imagealphablending($im2, true);
                $k = min($width/$widthO, $height/$heightO);
                ImageCopyResized($im2, $im, 0-($widthO*$k-$width)/2, 0-($heightO*$k-$height)/2, 0, 0, $widthO*$k, $heightO*$k, $widthO, $heightO);
                imagedestroy($im);
                $im = $im2;
                break;
            case "z":
                $k = 1;
                $k = min($k, $width/$widthO, $height/$heightO);
                $im2 = ImageCreateTrueColor($widthO*$k, $heightO*$k);
                ImageCopyResized($im2, $im, 0, 0, 0, 0, $widthO*$k, $heightO*$k, $widthO, $heightO);
                imagedestroy($im);
                $im = $im2;
                break;
        }


        switch ($format) {
            case "jpg":
            case "jpeg":
                header("Content-Type: image/jpeg");
                ImageJpeg($im, null, ($_GET["quality"] ?? 90));
                break;
            case "bmp":
                header("Content-Type: image/bmp");
                ImageBmp($im);
                break;
            case "png":
                header("Content-Type: image/png");
                ImagePng($im, null);
                break;
            case "gif":
                header("Content-Type: image/gif");
                ImageGif($im, null);
                break;
            case "webp":
                header("Content-Type: image/webp");
                imagewebp($im, null, ($_GET["quality"] ?? 80));
                break;
        }
    }
}