<?php

namespace converters;

interface ConverterTemplate
{
    public function get_name() : string;
    public function get_pattern() : string;
    public function get_cmd_convert_ffmpeg(\Datei $in, \Datei $out) : string;
}