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

    foreach ($org_video_files as $file) {
        if (!$file->is_video) { echo("[NOTICE] kein Video".PHP_EOL); continue; }
        $md5 = $file->md5;
        echo("[MD5] ".$md5.PHP_EOL);

        $local = new Datei("/converted/".$md5.".0.jpg");
        if (!$local->exists) $file->ffmpegthumbnailmiddle($local);

        $local = new Datei("/converted/".$md5.".1080p.mp4");
        if (!$local->exists) { 
            if ($file->height >= 1080) $file->convertffmpeg($local, ' -filter:v "scale=-2:1080" -c:v libx264 -c:a aac -movflags +faststart ');
            elseif ($file->height > 480)  $file->convertffmpeg($local, " -c:v libx264 -c:a aac -movflags +faststart ");
        }

        $local = new Datei("/converted/".$md5.".1080p.webm");
        if (!$local->exists) { 
            if ($file->height >= 1080) $file->convertffmpeg($local, ' -filter:v "scale=-2:1080" -vcodec libvpx-vp9 -acodec libvorbis ');
            elseif ($file->height > 480)  $file->convertffmpeg($local, " -vcodec libvpx-vp9 -acodec libvorbis ");
        }

        $local = new Datei("/converted/".$md5.".480p.mp4");
        if (!$local->exists) { 
            if ($file->height >= 480) $file->convertffmpeg($local, ' -filter:v "scale=-2:480" -c:v libx264 -c:a aac -movflags +faststart ');
            elseif ($file->height > 240)  $file->convertffmpeg($local, ' -c:v libx264 -c:a aac -movflags +faststart ');
        }

        $local = new Datei("/converted/".$md5.".480p.webm");
        if (!$local->exists) { 
            if ($file->height >= 480) $file->convertffmpeg($local, ' -filter:v "scale=-2:480" -vcodec libvpx-vp9 -acodec libvorbis ');
            elseif ($file->height > 240)  $file->convertffmpeg($local, " -vcodec libvpx-vp9 -acodec libvorbis ");
        }

        $local = new Datei("/converted/".$md5.".240p.mp4");
        if (!$local->exists) { 
            if ($file->height >= 240) $file->convertffmpeg($local, ' -filter:v "scale=-2:240" -c:v libx264 -c:a aac -movflags +faststart ');
            else $file->convertffmpeg($local, " -c:v libx264 -c:a aac -movflags +faststart ");
        }

        $local = new Datei("/converted/".$md5.".240p.webm");
        if (!$local->exists) { 
            if ($file->height >= 240) $file->convertffmpeg($local, ' -filter:v "scale=-2:240" -vcodec libvpx-vp9 -acodec libvorbis ');
            else $file->convertffmpeg($local, " -vcodec libvpx-vp9 -acodec libvorbis ");
        }
        
    }


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