<?php
require_once(__DIR__ . '/../common.php');
require_once(__DIR__ . '/../db/db.php');

$url_fpl = "https://fantasy.premierleague.com/drf/";
$url_standings = "leagues-classic-standings/";
$url_teams = $url_fpl . 'teams/';
$url_fixtures = $url_fpl . 'fixtures/';
$url_players = $url_fpl . 'bootstrap-static';
$url_players_detail = $url_fpl . 'element-summary/';

get_players_detail($url_players_detail);

function get_players_detail($url) {
    $db = New db();
    $j = 0;

    for ($i = 1; $i <= 570; $i++) { // 560
        $full_players = get_fpl_response($url.$i);
        $count = count($full_players['history']);
        $match = $full_players['history'][$count - 1];

        $insert = 'insert into players_detail (';
        foreach ($match as $key => $value) {
            $insert .= $key . ',';
        }
        $insert = rtrim($insert, ',');
        $insert .= ') values (';
        $update = 'ON DUPLICATE KEY UPDATE ';
        foreach ($match as $key => $value) {
            $value = ($value == '') ? 0 : $value;
            $insert .= "'" . $value . "',";
            $update .= $key.'='."'" . $value. "',";
        }
        $insert = rtrim($insert, ',');
        $update = rtrim($update, ',');
        $insert .= ') '.$update.'; ';
        $r = $db->query($insert);
        if ($r != 1) {
            error_log($r);
            error_log($insert);
        } else {
            $j++;
        };
    }
    echo ($j .' refreshed player detail');
    $db->close();
}

function get_fpl_response($url) {
    $response = file_get_contents($url);
    $json = json_decode($response, true);

    if (!$json) {
        return $json;
    } else {
        return $json;
    }
}