<?php
require_once(__DIR__ . '/../common.php');
require_once(__DIR__ . '/../db/db.php');

$url_fpl = "https://fantasy.premierleague.com/drf/";
$url_standings = "leagues-classic-standings/";
$url_teams = $url_fpl . 'teams/';
$url_fixtures = $url_fpl . 'fixtures/';
$url_players = $url_fpl . 'bootstrap-static';
$url_players_detail = $url_fpl . 'element-summary/';

time_elapsed();
// get_players_detail($url_players_detail);
get_players_detail($url_players_detail);
time_elapsed();

function get_players_detail($url) {
    $db = New db();
    $j = 0;

//    $players_playing = $db->select('SELECT p.id
//                        FROM players p
//                          JOIN (SELECT team_h team_code
//                                FROM fixtures
//                                WHERE event = 20 AND started = 1 AND finished_provisional = 0
//                                UNION ALL
//                                SELECT team_a
//                                FROM fixtures
//                                WHERE event = 20 AND started = 1 AND finished_provisional = 0
//                               ) f ON p.team = f.team_code;');

//    foreach ($players_playing as $k => $v) {
    for ($i = 1; $i <= 585; $i++) { // 560
//        $full_players = get_fpl_response($url.$v['id']);

//        if ($player_playing['id'] == 388) {
//               echo '<pre>'; print_r($full_players); echo '</pre>';;
//        }
        $full_players = get_fpl_response($url.$i);
        $count = count($full_players['history']);
        $match = $full_players['history'][$count - 1];

        if ($match['round'] != CURRENT_GW) {
            $insert = 'insert into players_detail (id, round, element, minutes, bps, total_points, fixture, bonus) values ('.$i.CURRENT_GW.'000,'.CURRENT_GW.','.$i.', 0,0,0,0,0) ON DUPLICATE KEY UPDATE element='.$i.';';
            } else {

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
            $insert .= ') ' . $update . '; ';
        }
        $r = $db->query($insert);
        if ($r != 1) {
            error_log($r);
            error_log($insert);
        } else {
            $j++;
        };

        $count = count($full_players['history']);
        $match = $full_players['history'][$count - 2];

        if ($match['round'] == CURRENT_GW) {
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
            $insert .= ') ' . $update . '; ';
        }
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

function get_players_detail_()
{
    $full_players = get_fpl_response(URL_PLAYERS_DETAIL . '1');
    $count = count($full_players['history']);
    $match = $full_players['history'][$count - 1];
    $insert = 'insert into players_detail (';

    $update = 'ON DUPLICATE KEY UPDATE ';
    foreach ($match as $key => $value) {
        if ($key != 'bonus') {
            $insert .= $key . ',';
            $update .= $key . '=VALUES(' . $key . '),';
        }
    }
    $insert = rtrim($insert, ',');
    $update = rtrim($update, ',');
    $insert .= ') values ';

    for ($i = 1; $i <= 580; $i++) { // 580
        $full_players = get_fpl_response(URL_PLAYERS_DETAIL . $i);
        $count = count($full_players['history']);
        $match = $full_players['history'][$count - 1];

        if ($match['round'] == CURRENT_GW) {

        }

        $insert .= '(';
        foreach ($match as $key => $value) {
            if ($key != 'bonus') {
                $value = ($value == '') ? 0 : $value;
                $insert .= "'" . $value . "',";
            }
        }
        $insert = rtrim($insert, ',') . '),';
    }
    $insert = rtrim($insert, ',');
    $db = New db();
    $r = $db->query($insert . $update);
    if ($r != 1) {
        error_log($r);
    }
    echo ('refreshed player detail');
    $db->close();
}