<?php

namespace converters;

class thumbnail_25pics implements ConverterTemplate {

    public function get_name(): string {
        return "Thumbnail (25pics)";
    }

    public function get_pattern() : string {
        return "@^(?P<pre>.*)(\.[a-zA-Z0-9]+)$@";
    }

    public function get_suffix() : string {
        return ".25img.jpg";
    }

    public function get_cmd_convert_ffmpeg(\Datei $in, \Datei $out) : string {
        return "";
    }

    public function convert(\Datei $in, \Datei $out) {
        $duration = $in->video_duration;
        $folder = "/tmp/".md5(microtime(true))."/";
        mkdir($folder, 0777);
        passthru('nice -n 19 ffmpeg -i "'.$in->fullpath.'" -vf fps=1/'.($duration/25).' '.$folder.'%d.png');        
        $im = ImageCreateTrueColor(1920, 1080);
        for ($i = 1; $i <= 25; $i++) {
            $im2 = ImageCreateFromPng($folder.$i.".png");
            ImageCopyResized($im, $im2, ((($i-1) % 5)*1920/5), floor(($i-1)/5)*(1080/5), 0, 0, 1920/5, 1080/5, ImagesX($im2), ImagesY($im2));
            ImageDestroy($im2);
        }
        ImageJpeg($im, $out->fullpath, 100);
        ImageDestroy($im);
        exec('rm -f "'.$folder.'"');
    }

}