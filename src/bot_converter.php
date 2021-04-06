#!/usr/bin/env php
<?php

$_ENV["basepath"] = __DIR__;

/*
 * Mit dieser Funktion werden Klassen anhand ihres Namens automatisch geladen. Das Ergebnis spiegelt den Erfolg der Ausführung
 * @param string $class_name Name der Klasse, die geladen werden muss
 * @return boolean
 */
spl_autoload_register(function($class_name) {
	$prio = array();
	if (substr($class_name,0,4) == "API_") {
		require_once(__DIR__."/app/api/0.1/classes/".substr($class_name,4,999).".php");
		return true;
	}
	
	//$prio[] = __DIR__."/app/code/helper/default/".$class_name.".php";
	$prio[] = __DIR__."/public_html/app/code/classes/".str_replace(chr(92), "/", $class_name).".php";
	//print_r($prio);

	foreach ($prio as $file) {
		if (file_exists($file)) {
			require($file);
			return true;
		}
	}
	if (isset($_GET["debug"])) throw new Exception("Klasse ".$class_name." kann nicht gefunden werden!");
	return false;
});

if (!file_exists("/config/config.json")) { 
    file_put_contents("/config/config.json","{}"); 
    chmod("/config/config.json", 0777);
}

while(true) {
    $json = @json_decode(@file_get_contents("/config/config.json"),true);
    print_r($json);

    $org_video_files = array();
    $dirs = array("/");
    for ($i = 0; $i < count($dirs); $i++) {
        $indir= "/originals".$dirs[$i];
        $files = scandir($indir);
        foreach ($files as $file) {
            if (substr($file,0,1) == ".") continue;
            if (is_dir($indir.$file)) $dirs[] = $dirs[$i].$file."/";
            else {
                $org_video_files[] = new Datei("/originals".$dirs[$i].$file);
            }
        }
    }

    $filesjson = array("bucket" => array());

    foreach ($org_video_files as $file) {
        if (!$file->is_video) { echo("[NOTICE] kein Video".PHP_EOL); continue; }
        $md5 = $file->md5;
        echo("[MD5] ".$md5.PHP_EOL);
        preg_match ("@^/originals/(?P<bucket>[^/]+)/(?P<path2>.*)\.[a-zA-Z0-9]+$@", $file->fullpath, $m);
        $bucket = $m["bucket"];
        $restpath = $m["path2"];
        $filesjson["bucket"][$bucket][$restpath]["md5"] = $md5;
        $filesjson["bucket"][$bucket][$restpath]["conv"] = array();
        //$filesjson 

        if (($json["convert"]["default"]["jpg_thumbnail0"] ?? "") == 1) {
            $local = new Datei("/converted/".$md5.".0.jpg");
            if (!$local->exists) $file->ffmpegthumbnailmiddle($local);
            $filesjson["bucket"][$bucket][$restpath]["conv"][] = "d.thumbmiddle";
        }

        if (($json["convert"]["default"]["jpg_thumbnail1"] ?? "") == 1) {
            $local = new Datei("/converted/".$md5.".1.jpg");
            if (!$local->exists) {
                $duration = $file->video_duration;
                $folder = "/tmp/".md5(microtime(true))."/";
                mkdir($folder, 0777);
                passthru('nice -n 19 ffmpeg -i "'.$file->fullpath.'" -vf fps=1/'.($duration/25).' '.$folder.'%d.png');        
                $im = ImageCreateTrueColor(1920, 1080);
                for ($i = 1; $i <= 25; $i++) {
                    $im2 = ImageCreateFromPng($folder.$i.".png");
                    ImageCopyResized($im, $im2, ((($i-1) % 5)*1920/5), floor(($i-1)/5)*(1080/5), 0, 0, 1920/5, 1080/5, ImagesX($im2), ImagesY($im2));
                    ImageDestroy($im2);
                }
                ImageJpeg($im, $local->fullpath, 100);
                ImageDestroy($im);
                exec('rm -f "'.$folder.'"');
            }
            $filesjson["bucket"][$bucket][$restpath]["conv"][] = "d.thumb25p";
        }

        

        if (($json["convert"]["default"]["mp4_1080p"] ?? "") == 1) {
            $local = new Datei("/converted/".$md5.".1080p.mp4");
            if (!$local->exists) { 
                $atts = " -c:v libx264 -c:a aac -movflags +faststart ";
                if ($file->height > 1080) $atts .= ' -filter:v "scale=-2:1080" ';
                $file->convertffmpeg($local, $atts);
            }
            $filesjson["bucket"][$bucket][$restpath]["conv"][] = "d.mp41080p";
        }

        if (($json["convert"]["default"]["webm_1080p"] ?? "") == 1) {
            $local = new Datei("/converted/".$md5.".1080p.webm");
            if (!$local->exists) { 
                $atts = ' -vcodec libvpx-vp9 -acodec libvorbis ';
                if ($file->height > 1080) $atts .= ' -filter:v "scale=-2:1080" ';
                $file->convertffmpeg($local, $atts);
            }
            $filesjson["bucket"][$bucket][$restpath]["conv"][] = "d.webm1080p";
        }

        if (($json["convert"]["default"]["mp4_480p"] ?? "") == 1) {
            $local = new Datei("/converted/".$md5.".480p.mp4");
            if (!$local->exists) { 
                $atts = " -c:v libx264 -c:a aac -movflags +faststart ";
                if ($file->video_fps > 30) $atts .= " -r 30 ";
                elseif ($file->video_fps > 25) $atts .= " -r 25 ";
                if ($file->height > 480) $atts .= ' -filter:v "scale=-2:480" ';
                $file->convertffmpeg($local, $atts);
            }
            $filesjson["bucket"][$bucket][$restpath]["conv"][] = "d.mp4480p";
        }

        if (($json["convert"]["default"]["webm_480p"] ?? "") == 1) {
            $local = new Datei("/converted/".$md5.".480p.webm");
            if (!$local->exists) {
                $atts = ' -vcodec libvpx-vp9 -acodec libvorbis ';
                if ($file->video_fps > 30) $atts .= " -r 30 ";
                elseif ($file->video_fps > 25) $atts .= " -r 25 ";
                if ($file->height > 480) $atts .= ' -filter:v "scale=-2:480" ';
                $file->convertffmpeg($local, $atts);
            }
            $filesjson["bucket"][$bucket][$restpath]["conv"][] = "d.webm480p";
        }

        if (($json["convert"]["default"]["mp4_240p"] ?? "") == 1) {
            $local = new Datei("/converted/".$md5.".240p.mp4");
            if (!$local->exists) { 
                $atts = " -c:v libx264 -c:a aac -movflags +faststart ";
                if ($file->video_fps > 30) $atts .= " -r 30 ";
                elseif ($file->video_fps > 25) $atts .= " -r 25 ";
                if ($file->height > 240) $atts .= ' -filter:v "scale=-2:240" ';
                $file->convertffmpeg($local, $atts);
            }
            $filesjson["bucket"][$bucket][$restpath]["conv"][] = "d.mp4240p";
        }

        if (($json["convert"]["default"]["webm_240p"] ?? "") == 1) {
            $local = new Datei("/converted/".$md5.".240p.webm");
            if (!$local->exists) { 
                $atts = ' -vcodec libvpx-vp9 -acodec libvorbis ';
                if ($file->video_fps > 30) $atts .= " -r 30 ";
                elseif ($file->video_fps > 25) $atts .= " -r 25 ";
                if ($file->height > 240) $atts .= ' -filter:v "scale=-2:240" ';
                $file->convertffmpeg($local, $atts);
            }
            $filesjson["bucket"][$bucket][$restpath]["conv"][] = "d.webm240p";
        }
        
    }

    file_put_contents("/config/files.json", json_encode($filesjson)); 
    chmod("/config/files.json", 0777);


    echo("[WAIT] warte 60sec ".date("Y-m-d H:i:s"));
    sleep(60);
}


function getallfiles($rootdir) {
    $out = array();
    $dirs = array("/");
    for ($i = 0; $i < count($dirs); $i++) {
        $indir= "/originals".$dirs[$i];
        $files = scandir($indir);
        foreach ($files as $file) {
            if (substr($file,0,1) == ".") continue;
            if (is_dir($indir.$file)) $dirs = $dirs[$i].$file."/";
            else {

            }
        }
    }
    return $out;
}