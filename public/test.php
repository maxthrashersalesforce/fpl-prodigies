<?php

require_once(__DIR__ . '/db/db.php');
require_once(__DIR__ . '/classes/entry.php');
require_once(__DIR__ . '/common.php');


$db = New db();
$j = 0;

$players_playing = $db->select('SELECT p.id
                        FROM players p
                          JOIN (SELECT team_h team_code
                                FROM fixtures
                                WHERE event = 20 AND started = 1 AND finished_provisional = 0
                                UNION ALL
                                SELECT team_a
                                FROM fixtures
                                WHERE event = 20 AND started = 1 AND finished_provisional = 0
                               ) f ON p.team = f.team_code;');

// echo '<pre>'; print_r($players_playing); echo '</pre>';

foreach ($players_playing as $k => $v) {
    $full_players = get_fpl_response(URL_PLAYERS_DETAIL.$v['id']);

    $count = count($full_players['history']);
    $match = $full_players['history'][$count - 1];

    $insert = 'insert into players_detail (';
    foreach ($match as $key => $value) {
        if ($key != 'bonus') {
            $insert .= $key . ',';
        }
    }
    $insert = rtrim($insert, ',');
    $insert .= ') values (';
    $update = 'ON DUPLICATE KEY UPDATE ';
    foreach ($match as $key => $value) {
        if ($key != 'bonus') {
            $value = ($value == '') ? 0 : $value;
            $insert .= "'" . $value . "',";
            $update .= $key . '=' . "'" . $value . "',";
        }
    }
    $insert = rtrim($insert, ',');
    $update = rtrim($update, ',');
    $insert .= ') '.$update.'; ';
//    echo ($insert .'<br>');
    $r = $db->query($insert);
    if ($r != 1) {
        error_log($r);
        error_log($insert);
    } else {
        $j++;
    };
    if ($v['id'] == 322) {
        echo $insert;
    }
}
