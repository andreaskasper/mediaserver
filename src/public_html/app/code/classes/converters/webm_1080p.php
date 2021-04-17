<?php

namespace converters;

class webm_1080p implements ConverterTemplate {

    public function get_name(): string {
        return "webm 1080p max fps (VP9, Vorbis)";
    }

    public function get_pattern() : string {
        return "@^(?P<pre>.*)(\.mp4|\.mov|\.mpeg|\.mkv)$@";
    }

    public function get_suffix() : string {
        return ".1080p.webm";
    }

    public function get_cmd_convert_ffmpeg(\Datei $in, \Datei $out) : string {
        return "";
    }

    public function convert(\Datei $in, \Datei $out) {
        $atts = " -vcodec libvpx-vp9 -acodec libvorbis ";
        if ($in->height > 1080) $atts .= ' -filter:v "scale=-2:1080" ';
        $in->convertffmpeg($out, $atts);
    }

}