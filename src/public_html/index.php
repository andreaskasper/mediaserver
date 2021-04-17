<?php
/*
 * Hier startet die gesamte Webseite
 *
 * inspiriert von http://www.bestofsalsa.com/Home/Discover
 *
 */


header("Cache-Control: no-cache, no-store, must-revalidate");
 
define("asi_allowed_entrypoint", true);

if (true) {
	ini_set("error_reporting", E_ALL | E_STRICT);
//	xdebug_enable();
} else {
//	xdebug_disable();
	//ini_set("display_errors","off");
	set_exception_handler(function($ex) {
		$msg = "https://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"].PHP_EOL;
		$msg .= var_export($ex ,true);
		//mail("test@example.com", "Exception bei MediaServer", $msg);
		/*$db = new \SQL(0);
		$w = array();
		$w["url"] = ;
		$w["message"] = $ex->getMessage();
		$w["file"] = $ex->getFile();
		$w["zeile"] = $ex->getLine();
		$w["stack_json"] = json_encode(xdebug_get_function_stack(), JSON_PRETTY_PRINT);
		$db->Create("log_error", $w);*/
		header($_SERVER["SERVER_PROTOCOL"]." 500 Globaler Serverfehler"); 
		die("Ooops, this is a error I don't thought it would happen. Please contact me at scoring@andi.dance at tell me about that error");
		exit;
	});
}

//if(function_exists('xdebug_disable')) { xdebug_disable(); }

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
	$prio[] = __DIR__."/app/code/classes/".str_replace(chr(92), "/", $class_name).".php";
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

DB::init(0, "sqlite:/configs/db.sqlite3");

//require_once(__DIR__."/app/code/vendor/autoload.php");


date_default_timezone_set("Europe/Berlin");

//file_put_contents(__DIR__."/requests.log", var_export($_SERVER,true).PHP_EOL, FILE_APPEND);

//print_r($_ENV);

//DB::init(0, "mysql:host=db;dbname=test", "test", "test");


try {
	Routing::start();
} catch (Exception $ex) {
	Xdebug::show_exception($ex);
}

function html($txt) {
	return htmlentities($txt, 3, "UTF-8");
}

function htmlattr($txt) {
	return str_replace(array('"'),array(''),html($txt));
}

function htmlhref($txt) {
	$txt = str_replace(array(" ","ä","ö","ü","ß","Ä","Ö","Ü"),array("_","ae","oe","ue","ss","Ae","Oe","Ue"),$txt);
	$txt = preg_replace("@[^a-zA-Z0-9\_\-]@iU","",$txt);
	return $txt;
}