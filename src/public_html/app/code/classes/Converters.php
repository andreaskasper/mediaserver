<?php

class Converters {



    public static function get_converters(): Array {
        return array(
            "thumb_middle" => new \converters\thumbnail_middle(),
            "thumb_25pics" => new \converters\thumbnail_25pics(),
            "mp4_2160p" => new \converters\mp4_2160p(),
            "mp4_1080p" => new \converters\mp4_1080p(),
            "mp4_480p" => new \converters\mp4_480p(),
            "mp4_240p" => new \converters\mp4_240p(),
            "webm_2160p" => new \converters\webm_2160p(),
            "webm_1080p" => new \converters\webm_1080p(),
            "webm_480p" => new \converters\webm_480p(),
            "webm_240p" => new \converters\webm_240p()
        );
    }



}