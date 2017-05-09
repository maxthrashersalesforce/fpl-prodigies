<?php

class db {

    protected static $conn;

    public function connect() {
       // global $url_fpl, $url_standings, $url_players, $server, $user, $pw, $db, $port;

        if (gethostname() == 'scotchbox') {
            $server = "localhost";
            $user = "root";
            $pw = "root";
            $db = 'fpl';
            $port = null;
        } else {
            $server = "127.0.0.1";
            $user = "root";
            $pw = "*";
            $db = 'fpl';
            $port = '3306';
        }

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
        //echo $query . '<br>';
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
}

?>