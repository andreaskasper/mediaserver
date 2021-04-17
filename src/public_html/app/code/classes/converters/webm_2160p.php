<?php

namespace converters;

class webm_2160p implements ConverterTemplate {

    public function get_name(): string {
        return "webm 4K max fps (VP9, Vorbis)";
    }

    public function get_pattern() : string {
        return "@^(?P<pre>.*)(\.mp4|\.mov|\.mpeg|\.mkv)$@";
    }

    public function get_suffix() : string {
        return ".2160p.webm";
    }

    public function get_cmd_convert_ffmpeg(\Datei $in, \Datei $out) : string {
        return "";
    }

    public function convert(\Datei $in, \Datei $out) {
        $atts = " -vcodec libvpx-vp9 -acodec libvorbis ";
        if ($in->height > 2160) $atts .= ' -filter:v "scale=-2:2160" ';
        $in->convertffmpeg($out, $atts);
    }

}