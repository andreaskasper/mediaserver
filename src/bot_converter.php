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

/*DB::init(0, "sqlite:/configs/db.sqlite3");

$db = new DB(0);
$db->cmd('CREATE TABLE IF NOT EXISTS files (
    original_file TEXT PRIMARY KEY,
    bucket TEXT,
    pre TEXT,
    md5 TEXT
    )');*/

if (!file_exists("/config/config.json")) { 
    file_put_contents("/config/config.json","{}"); 
    chmod("/config/config.json", 0777);
}

while(true) {
    $json_config = @json_decode(@file_get_contents("/config/config.json"),true);
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


        $converters = Converters::get_converters();
        foreach ($converters as $conv_id => $convi) {
            $datei_converted = new Datei("/converted/".$md5.$convi->get_suffix());
            if (($json_config["convert"]["default"][$conv_id] ?? "") == 1) {
                if (!$datei_converted->exists) {
                    $a = $convi->convert($file, $datei_converted);
                }
            }
            if ($datei_converted->exists) $filesjson["bucket"][$bucket][$restpath]["conv"][] = $conv_id;
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