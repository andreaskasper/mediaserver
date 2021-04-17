<?php

namespace converters;

class mp4_1080p implements ConverterTemplate {

    public function get_name(): string {
        return "mp4 1080p max fps (h.264, aac)";
    }

    public function get_pattern() : string {
        return "@^(?P<pre>.*)(\.mp4|\.mov|\.mpeg|\.mkv)$@";
    }

    public function get_suffix() : string {
        return ".1080p.mp4";
    }

    public function get_cmd_convert_ffmpeg(\Datei $in, \Datei $out) : string {
        return "";
    }

    public function convert(\Datei $in, \Datei $out) {
        $atts = " -c:v libx264 -c:a aac -movflags +faststart ";
        if ($in->height > 1080) $atts .= ' -filter:v "scale=-2:1080" ';
        $in->convertffmpeg($out, $atts);
    }

}