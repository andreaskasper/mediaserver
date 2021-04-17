<?php

namespace converters;

class mp4_240p implements ConverterTemplate {

    public function get_name(): string {
        return "mp4 240p max fps (h.264, aac)";
    }

    public function get_pattern() : string {
        return "@^(?P<pre>.*)(\.mp4|\.mov|\.mpeg|\.mkv)$@";
    }

    public function get_suffix() : string {
        return ".480p.mp4";
    }

    public function get_cmd_convert_ffmpeg(\Datei $in, \Datei $out) : string {
        return "";
    }

    public function convert(\Datei $in, \Datei $out) {
        $atts = " -c:v libx264 -c:a aac -movflags +faststart ";
        if ($in->height > 240) $atts .= ' -filter:v "scale=-2:240" ';
        if ($in->video_fps > 51) $atts .= " -r 30 ";
        elseif ($in->video_fps > 41) $atts .= " -r 25 ";
        $in->convertffmpeg($out, $atts);
    }

}