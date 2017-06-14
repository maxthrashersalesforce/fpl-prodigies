<?php

function connect() {
    global $url_fpl, $url_standings, $url_players, $server, $user, $pw, $db, $port;

    if (gethostname() == 'scotchbox') {
        $server = "localhost";
        $user = "root";
        $pw = "root";
        $db = 'fpl';
        $port = null;
    } else {
        $server = "127.0.0.1";
        $user = "root";
        $pw = "****";
        $db = 'fpl';
        $port = '3306';
    }

    static $conn;
    $conn = mysqli_connect($server, $user, $pw, $db, $port);

    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    } else {
        return $conn;
    }
    
    $teams_response = file_get_contents($url_fpl . 'teams/');
    $teams = json_decode($teams_response, true);

}

?>