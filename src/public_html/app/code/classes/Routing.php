<?php

class Routing {

    public static function start() {

        $_SERVER["REQUEST_URI2"] = substr($_SERVER["REQUEST_URI"],strlen($_SERVER["SCRIPT_NAME"])-10);
		$p = strpos($_SERVER["REQUEST_URI2"],"?");
		if (!$p) $_SERVER["REQUEST_URIpure"] = $_SERVER["REQUEST_URI2"]; else $_SERVER["REQUEST_URIpure"] = substr($_SERVER["REQUEST_URI2"],0, $p);

		if (preg_match ("@^\/api\/(?P<namespace>[A-Za-z0-9]+)(\.|\/)(?P<method>[A-Za-z0-9]+)(\.|\/)(?P<format>[a-z]+)@", $_SERVER["REQUEST_URIpure"], $m)) {
			\API::run($m["namespace"], $m["method"], $m["format"], $_REQUEST);
			exit(1);
        }

        switch ($_SERVER["REQUEST_URIpure"]) {
            case "/sitemap.xml":
                PageEngine::html("xml_sitemap"); exit;
        }

        $langs = array( "deDE", "enUS");

        /*ab jetzt geht es in die Multilingualität*/
        if (preg_match("@^/(?P<lang>[a-z]{2}[A-Z]{2})(?P<url>/.*)$@", $_SERVER["REQUEST_URIpure"], $m)) {
            if (!in_array($m["lang"], $langs)) { \PageEngine::html("goto", array("url" => "/enUS".$_SERVER["REQUEST_URI"])); exit(1); }
            $_ENV["lang"] = $m["lang"];
            $_ENV["lang2"] = substr($m["lang"],0,2);
		} else {
			\PageEngine::html("goto", array("url" => "/enUS".$_SERVER["REQUEST_URI"])); exit(1);
        }
        
        switch ($m["url"]) {
            case "":
            case "/":
            case "/index.html":
                PageEngine::html("page_index");
                exit(1);
            case "/admin":
                header("Location: /".$_ENV["lang"]."/admin/");
                exit(1);
            case "/bucket":
                header("Location: /".$_ENV["lang"]."/bucket/");
                exit(1);
        }

        if (substr($m["url"],0,7) == "/admin/") self::start_admin(substr($m["url"], 6, 9999));
        if (substr($m["url"],0,8) == "/bucket/") self::start_bucket(substr($m["url"], 7, 9999));
                
           /* if (preg_match("@^/regstatus/(?P<id>[0-9]+)(?P<salt>[A-Z0-9]{4})@", $m2["path"], $m3)) {
                PageEngine::html("page_regstatus", array("event" => $event, "reg" => $m3["id"], "salt" => $m3["salt"]));
                exit(1);
            }*/
        //}
        
        header("Not Found", true, 404);
        header("Location: /"); exit(1);
    }

    public static function start_admin(string $path) {
        switch ($path) {
            case "/":
                PageEngine::html("admin/page_index");
                exit();
            case "/bucket/":
                PageEngine::html("admin/page_buckets");
                exit();
            case "/settings":
                PageEngine::html("admin/page_settings");
                exit();
            case "/users/":
                PageEngine::html("admin/page_users");
                exit();
            default:
        }

        if (preg_match("@^/bucket/(?P<bucket>[A-Za-z0-9]+)(?P<path>/.*)$@", $path, $m)) {
            PageEngine::html("admin/page_folder", array("bucket" => $m["bucket"], "path" => $m["path"]));
            exit;
        }

        die("Adminpfad: ".$path);


        header("Not Found", true, 404);
        header("Location: /"); exit(1);
    }

    public static function start_bucket(string $path) {
        echo($path);
    }
    
}