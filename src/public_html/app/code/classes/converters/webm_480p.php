<?php

namespace converters;

class webm_480p implements ConverterTemplate {

    public function get_name(): string {
        return "webm 480p max fps (VP9, Vorbis)";
    }

    public function get_pattern() : string {
        return "@^(?P<pre>.*)(\.mp4|\.mov|\.mpeg|\.mkv)$@";
    }

    public function get_suffix() : string {
        return ".480p.webm";
    }

    public function get_cmd_convert_ffmpeg(\Datei $in, \Datei $out) : string {
        return "";
    }

    public function convert(\Datei $in, \Datei $out) {
        $atts = " -vcodec libvpx-vp9 -acodec libvorbis ";
        if ($in->height > 480) $atts .= ' -filter:v "scale=-2:480" ';
        if ($in->video_fps > 51) $atts .= " -r 30 ";
        elseif ($in->video_fps > 41) $atts .= " -r 25 ";
        $in->convertffmpeg($out, $atts);
    }

}