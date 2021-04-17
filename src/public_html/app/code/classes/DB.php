<?php

/**
 * Klasse für PDO
 * @author Andreas Kasper <Andreas.Kasper@plabsi.com>
 * @package ASICMS
 * @version 0.1.20200428
 * LastChange: Erster Commit
 * 
 * Änderung am 28.04.2020 Commit
 * Nur noch ab php Version 7 möglich. Einige Optimierungen dafür.
 */
 
class DB {
    private static $_cache = array();
    private $_connection_id = null;
    private $conn = null;
    private $_lastresult = null;
    

    const READWRITE = 3;
    const READ = 1;
    const WRITE = 2;

    const LOW_PRIORITY = 1;
    const IGNORE = 2;
   

    public static function init(int $id, string $connectionstring, $user = null, $password = null, $readwrite = null) {
        if (is_null($readwrite)) $readwrite = DB::READWRITE;
        self::$_cache[$id]["connectionstring"] = $connectionstring;
        self::$_cache[$id]["user"] = $user;
        self::$_cache[$id]["password"] = $password;
        self::$_cache[$id]["readwrite"] = $readwrite;
        self::$_cache[$id]["conn"] = null;
    }

    public function __construct(int $id) {
        $this->_connection_id = $id;
        if (is_null(self::$_cache[$id]["conn"])) {
            self::$_cache[$id]["conn"] = new PDO(self::$_cache[$id]["connectionstring"], self::$_cache[$id]["user"], self::$_cache[$id]["password"]);
            self::$_cache[$id]["conn"]->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        $this->conn = self::$_cache[$id]["conn"];
    }

    public function __get($name) {
        switch(strtolower($name)) {
            case "drivername": return $this->conn->getAttribute(PDO::ATTR_DRIVER_NAME);
            case "lastcmd": return $this->_lastresult->queryString;
        }
        trigger_error("Variable not found ".$name, E_USER_WARNING);
    }

    public function cmd(string $sql, Array $values = array()) {
        //$sth = $this->conn->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $this->_lastresult = $this->conn->prepare($sql);
        $this->_lastresult->execute($values);

        return $this->_lastresult;
    }

    public function exec(string $sql) {
        return $this->conn->exec($sql);
    }

    public function cmdrow(string $sql, Array $values = array()) {
        $sth = $this->cmd($sql, $values);
        $row = $sth->fetch(PDO::FETCH_BOTH);
        return $row;
    }

    public function cmdrows(string $sql, Array $values = array(), $key = null) {
        $sth = $this->cmd($sql, $values);
        $rows = $sth->fetchAll(PDO::FETCH_BOTH);
        return $rows;
    }

    public function cmdvalue(string $sql, Array $values = array()) {
        $sth = $this->cmd($sql, $values);
        $row = $sth->fetch(PDO::FETCH_NUM);
        return $row[0];
    }

    public function Create(string $table = "", array $arr = array(), ) {
        $fSet = array();
        $arr2 = array();
        foreach ($arr as $key => $v) {
            $fSet[] = ' `'.$key.'` = ? ';
            $arr2[] = $v;
        }

		$sql = "INSERT LOW_PRIORITY IGNORE INTO ".$table." SET ".implode(",", $fSet);
		return $this->cmd($sql, $arr2);
    }



}
