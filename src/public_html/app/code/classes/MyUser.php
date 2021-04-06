<?php

class MyUser {

    public static function isloggedin() : bool {
        return isset($_SESSION["user"]["id"]);
    }

    public static function checklogin() : bool {
        $user = $_SERVER['PHP_AUTH_USER'] ?? null;
        $pass = $_SERVER["PHP_AUTH_PW"] ?? null;
        if (empty($user) OR empty($pass)) return false;
        if (!empty($_ENV["ROOT_USER"]) AND !empty($_ENV["ROOT_PASSWORD"]) AND $_ENV["ROOT_USER"] == $user AND $_ENV["ROOT_PASSWORD"] == $pass) {
            $_SESSION["user"]["id"] = 1;
            $_SESSION["user"]["name"] = "Superuser";
            return true;
        }
        return false;
    }



}