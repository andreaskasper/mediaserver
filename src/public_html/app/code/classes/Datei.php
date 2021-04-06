<?php

class Datei {

    private $_file = null;
    private $_ffprobe = null;
    private $_md5 = null;

    public function __construct(string $fullpathtofile) {
        $this->_file = $fullpathtofile;
    }

    public function __get($name) {
        switch ($name) {
            case "prebasename":
                return substr($this->basename, 0, strlen($this->basename)-1-strlen($this->extension));
            case "basename":
                $path_parts = pathinfo($this->_file);
                return $path_parts["basename"];
            case "bucket":
                if (preg_match("@^/originals/(?P<bucket>[^/]+)/@", $this->fullpath, $m)) return $m["bucket"];
                return null;
            case "bucketprekey":
                if (preg_match("@^/originals/(?P<bucket>[^/]+)/(?P<prekey>.+)(\.[a-zA-Z0-9]+)$@", $this->fullpath, $m)) return $m["prekey"];
                return null;
            case "exist":
            case "exists": 
                return file_exists($this->_file);
            case "extension":
                $path_parts = pathinfo($this->_file);
                return $path_parts["extension"];
            case "ffprobe":
                return $this->loadffprobe();
            case "filesize":
                return filesize($this->fullpath);
            case "fullpath": return $this->_file;
            case "md5": 
                if (is_null($this->_md5)) $this->_md5 = md5_file($this->_file);
                return $this->_md5;
            case "mimetype":
                return mime_content_type($this->fullpath);
            case "is_video":
                return ($this->video_duration > 1);
            case "video_duration":
                $ffprobe = $this->ffprobe;
                return ($ffprobe["format"]["duration"] ?? 0);
            case "video_fps":
                $ffprobe = $this->ffprobe;
                foreach ($ffprobe["streams"] as $s) {
                    if (preg_match("@^(?P<fps>[0-9]+)/1$@", $s["r_frame_rate"], $m)) return $m["fps"];
                }
                print_r($ffprobe);
                throw new Exception("Unbekannter fps");
                return null;
            case "width":
                $ffprobe = $this->ffprobe;
                foreach ($ffprobe["streams"] as $s) if (!empty($s["width"])) return $s["width"];
                return null;
            case "height":
                $ffprobe = $this->ffprobe;
                foreach ($ffprobe["streams"] as $s) if (!empty($s["height"])) return $s["height"];
                return null;

        }
        throw new Exception("Unknown Variable ".$name);
        return null;
    }

    public function convertffmpeg(Datei $output, $parameters = null) : bool {
        if (is_null($parameters)) $parameters = "";
        $tempfile = new Datei("/tmp/".md5(microtime(true)).".".$output->extension);
        passthru('nice -n 19 ffmpeg -i "'.$this->fullpath.'" '.$parameters.' "'.$tempfile->fullpath.'"');
        if (!$tempfile->exists) throw new Exception("Konvertierte Datei ".$tempfile->fullname." existiert nicht. Problem bei ffmpeg.");
        rename($tempfile->fullpath, $output->fullpath);
        return $output->exists;
    }

    public function ffmpegthumbnailmiddle(Datei $output) : bool {
        $d = $this->video_duration/2;
        return $this->convertffmpeg($output, " -f mjpeg -vframes 1 -ss ".($d/2)." ");
    }



    private function loadffprobe() {
        if (is_null($this->_ffprobe)) {
            exec('ffprobe -v quiet -print_format json -show_format -show_streams "'.$this->_file.'"',$a);
            $this->_ffprobe = json_decode(implode("",$a), true);
        }
        return $this->_ffprobe;
    }


}