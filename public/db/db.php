<?php

class db {

    protected static $conn;

    public function connect() {
       // global $url_fpl, $url_standings, $url_players, $server, $user, $pw, $db, $port;
        $configs = require_once('cred.php');
        $server = $configs['server'];
        $user = $configs['user'];
        $pw = $configs['pw'];
        $db = $configs['db'];
        $port = $configs['port'];

        if(!isset(self::$conn)) {
            self::$conn = new mysqli($server, $user, $pw, $db, $port);
        }

        if(self::$conn === false) {
            echo 'error on connection.';
            return false;
        }
        return self::$conn;

    }

    public function query($query) {
        $conn = $this -> connect();
        $r = $conn -> query($query);
        return $r;
    }

    public function select($query) {
        $rows = array();
        $r = $this -> query($query);
        if($r === false) {
            return false;
        }
        while ($row = $r -> fetch_assoc()) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function error() {
        $conn = $this->connect();
        return $conn->error;
    }
}

?>