<?php

namespace converters;

class mp4_2160p implements ConverterTemplate {

    public function get_name(): string {
        return "mp4 4K max fps (h.264, aac)";
    }

    public function get_pattern() : string {
        return "@^(?P<pre>.*)(\.mp4|\.mov|\.mpeg|\.mkv)$@";
    }

    public function get_suffix() : string {
        return ".2160p.mp4";
    }

    public function get_cmd_convert_ffmpeg(\Datei $in, \Datei $out) : string {
        return "";
    }

    public function convert(\Datei $in, \Datei $out) {
        $atts = " -c:v libx264 -c:a aac -movflags +faststart ";
        if ($in->height > 2160) $atts .= ' -filter:v "scale=-2:2160" ';
        $in->convertffmpeg($out, $atts);
    }

}