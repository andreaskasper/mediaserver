<?php

namespace converters;

class thumbnail_middle implements ConverterTemplate {

    public function get_name(): string {
        return "Thumbnail (middle)";
    }

    public function get_pattern() : string {
        return "@^(?P<pre>.*)(\.[a-zA-Z0-9]+)$@";
    }

    public function get_suffix() : string {
        return ".0.jpg";
    }

    public function get_cmd_convert_ffmpeg(\Datei $in, \Datei $out) : string {
        return "";
    }

    public function convert(\Datei $in, \Datei $out) {
        $in->ffmpegthumbnailmiddle($out);
    }

}