<?php
// error_reporting(0);
require_once(__DIR__ . '/../db/db.php');
require_once(__DIR__ . '/../classes/entry.php');
require_once(__DIR__ . '/../common.php');
require_once(__DIR__ . '/../weath/weather.php');
// require_once(__DIR__ . '/../justoffside.php');

$url_fpl = "https://fantasy.premierleague.com/drf/";
$url_standings = "leagues-classic-standings/";
$url_standings_h2h = "leagues-h2h-standings/";
$url_teams = $url_fpl . 'teams/';
$url_fixtures = $url_fpl . 'fixtures/';
$url_players = $url_fpl . 'bootstrap-static';
$success = 0;
$arr = array();
$mode = isset($_POST['mode']) ? $_POST['mode'] : '';
$s_entry = isset($_SESSION['entry']) ? $_SESSION['entry'] : null;
$s_league = isset($_SESSION['league']) ? $_SESSION['league'] : null;

switch ($mode) {
    case 'teamsheets':
        $team_id = $_POST['team_id'] ?: 10;
        $screen_height = $_POST['screen_height'];
        $body = get_teamsheets($team_id, $screen_height);
        $options = get_teams_select($team_id);

        if ($body and $options) {
            $success = 1;
        }

        $arr = array(
            'SUCCESS' => $success
            ,'BODY' => $body
            ,'OPTIONS' => $options
        );
        break;
    case 'fixtures':
        $shown_gameweeks = $_POST['gameweeks'] ?: 3;

        $tbl = fixtures_table($shown_gameweeks);
        if ($tbl) {
            $success = 1;
        }

        $arr = array(
            'SUCCESS' => $success
            ,'TABLE' => $tbl
            ,'gw' => $shown_gameweeks
        );
        break;
    case 'players':
        $positions = $_POST['positions'];
        $body = players_table($positions);
        $arr = array(
            'SUCCESS' => $success
            ,'BODY' => $body
        );
        break;
    case 'selections':
        $league = $_POST['league'] ?: ($s_league ?: 313); // 3281;
        if ($league == '00000') {
            $body = 'Enter your League ID above to get selections!';
        } else {
            if ($league != 'veterans') {
                $_SESSION['league'] = $league;
            }
            $body = get_league_picks($league);
        }
        $success = 1;
        $arr = array(
            'SUCCESS' => $success
            ,'BODY' => $body
            ,'LEAGUE' => $league
        );
        break;
    case 'test':
        $league = $_POST['league'] ?: ($s_league ?: 46823); // 3281;
        $pages = $_POST['pages'] ?: 1;
        if ($league == '00000') {
            $body = 'Enter your League ID above to get your live table!';
        } else {
//            time_elapsed();
            $_SESSION['league'] = $league;
            $body = get_league_picks_($league, $pages);
//            $db = New db();
//            $db->query("insert into transactions (page, league) values ('live', ".$league.")");
//            time_elapsed();
        }
        $success = 1;
        $arr = array(
            'SUCCESS' => $success
            ,'BODY' => $body
            ,'LEAGUE' => $league
        );
        break;
    case 'test_':
        $league = $_POST['league'] ?: ($s_league ?: 46823); // 3281;
        if ($league == '00000') {
            $body = 'Enter your League ID above to get your live table!';
        } else {
            time_elapsed();
            $_SESSION['league'] = $league;
            $body = first_of_week($league);
            time_elapsed();
        }
        $success = 1;
        $arr = array(
            'SUCCESS' => $success
            ,'BODY' => $body
            ,'LEAGUE' => $league
        );
        break;
    case 'team':
        $team = $_POST['team'] ?: ($s_entry ?: 81182);
        if ($team == '00000') {
            $body = 'Enter your Team ID above to get selections!';
        } else {
            $_SESSION['entry'] = $team;
            $body = get_team($team);
        }
        $success = 1;
        $arr = array(
            'SUCCESS' => $success
            ,'BODY' => $body
            ,'TEAM' => $team
        );
        break;
    case 'weather':
        $player = $_POST['player'] ?: 255;
        $body = get_weather($player);
        $success = 1;
        $arr = array(
            'SUCCESS' => $success
            ,'BODY' => $body
        );
        break;
    case 'transfers':
        $entry = $_POST['entry'] ?: 0;
        $entry_obj = new entry();
        $entry_obj->get_transfers($entry);
        $success = 1;
        $arr = array(
            'SUCCESS' => $success
            ,'BODY' => $entry_obj->transfers
        );
        break;
    case 'justoffside':
        $res = justoffside(433);
        $success = 1;
        $arr = array(
            'SUCCESS' => $success
            ,'JO' => $res['JO']
            ,'LEAGUE' => $res['LEAGUE']
        );
        break;
    case 'fmlfpl':
        $res = justoffside(366);
        $success = 1;
        $arr = array(
            'SUCCESS' => $success
            ,'JO' => $res['JO']
            ,'LEAGUE' => $res['LEAGUE']
        );
        break;
    case 'overall':

        $body = get_league_picks_('313', 1, true);
        $success = 1;
        $arr = array(
            'SUCCESS' => $success
            ,'BODY' => $body
            ,'LEAGUE' => $league
        );
        break;

}

echo (sizeof($arr) > 0 ) ? json_encode($arr) : null;

function get_weather($player) {
    $w = new weather();
    return $w -> get_player($player);

}

function get_team($team_id) {
    $entry = new entry();
    $entry -> get($team_id);
    $t = $entry->picks;

    $p = db_get_players_for_my_team();

//    return $p;
    $current = array();
    foreach($t as $player) {
        $current[] = $p[$player['element'] - 1];
    }


    $t = array_order($current, 'element_type', SORT_ASC);



    $teams = array();
    for ($i = CURRENT_GW + 1; $i <= CURRENT_GW + 1 + 3 and $i <= 38; $i++) {
        $gk1 = name($t, $i, 0);
        $gk2 = name($t, $i, 1);
        $def1 = name($t, $i, 2);
        $def2 = name($t, $i, 3);
        $def3 = name($t, $i, 4);
        $def4 = name($t, $i, 5);
        $def5 = name($t, $i, 6);
        $mid1 = name($t, $i, 7);
        $mid2 = name($t, $i, 8);
        $mid3 = name($t, $i, 9);
        $mid4 = name($t, $i, 10);
        $mid5 = name($t, $i, 11);
        $fwd1 = name($t, $i, 12);
        $fwd2 = name($t, $i, 13);
        $fwd3 = name($t, $i, 14);

        $teams[] = '<div class="wrapper">
            <div class="hdr"><h4><b>Gameweek ' . $i . '</b></h4></div>    
            <div class="my-player-wrapper gk1">' . $gk1 . '</div>
            <div class="my-player-wrapper gk2">' . $gk2 . '</div>
            <div class="my-player-wrapper def1">' . $def1 . '</div>
            <div class="my-player-wrapper def2">' . $def2 . '</div>
            <div class="my-player-wrapper def3">' . $def3 . '</div>
            <div class="my-player-wrapper def4">' . $def4 . '</div>
            <div class="my-player-wrapper def5">' . $def5 . '</div>
            <div class="my-player-wrapper mid1">' . $mid1 . '</div>
            <div class="my-player-wrapper mid2">' . $mid2 . '</div>
            <div class="my-player-wrapper mid3">' . $mid3 . '</div>
            <div class="my-player-wrapper mid4">' . $mid4 . '</div>
            <div class="my-player-wrapper mid5">' . $mid5 . '</div>
            <div class="my-player-wrapper fwd1">' . $fwd1 . '</div>
            <div class="my-player-wrapper fwd2">' . $fwd2 . '</div>
            <div class="my-player-wrapper fwd3">' . $fwd3 . '</div>
        </div>';
    }
    return $teams;
}

function name($t, $i, $slot) {
    $r = '<div style="text-align: center">';
    $r .= '<img class="player-image-my" src="http://platform-static-files.s3.amazonaws.com/premierleague/photos/players/110x140/p'.$t[$slot]['code'].'.png"/>';
    $r .= '<br>';
    $r .= '<span class="my-player"><b>'.$t[$slot]['web_name'].'</b>';
    $r .= '<br>';
    $r .= str_replace(' (','<br>(', $t[$slot]['gw'.$i]).'</span>';
    $r .= '</div>';
    return $r;
}

function get_league_picks($league_id) {
    global $url_fpl, $url_standings, $url_standings_h2h;

    if ($league_id != 'veterans') {
        $page = 1;
        $json_response = file_get_contents($url_fpl . $url_standings . $league_id . "?phase=1&le-page=1&ls-page=" . $page);
        $array = json_decode($json_response, true);
        if (CURRENT_GW == 1) {
//            $json_standings = $array['new_entries']['results'];
            $json_standings = $array['standings']['results'];
        } else {
            $json_standings = $array['standings']['results'];
        }
    } else {

        $json_standings[0]['entry_name'] = 'Mark McGettigan'; $json_standings[0]['id'] = '24494'; $json_standings[0]['entry'] = '24494';
        $json_standings[1]['entry_name'] = 'Marlen Rattiner'; $json_standings[1]['id'] = '85929'; $json_standings[1]['entry'] = '85929';
        $json_standings[2]['entry_name'] = 'Grant Barclay'; $json_standings[2]['id'] = '36462'; $json_standings[2]['entry'] = '36462';
        $json_standings[3]['entry_name'] = 'Jay Egersdorff'; $json_standings[3]['id'] = '175574'; $json_standings[3]['entry'] = '175574';
        $json_standings[4]['entry_name'] = 'Joe Lepper'; $json_standings[4]['id'] = '208'; $json_standings[4]['entry'] = '208';
        $json_standings[5]['entry_name'] = 'Chaz Phillips'; $json_standings[5]['id'] = '69'; $json_standings[5]['entry'] = '69';
        $json_standings[6]['entry_name'] = 'Tom Fenley'; $json_standings[6]['id'] = '38273'; $json_standings[6]['entry'] = '38273';
        $json_standings[7]['entry_name'] = 'Ville Rönkä'; $json_standings[7]['id'] = '767113'; $json_standings[7]['entry'] = '767113';
        $json_standings[8]['entry_name'] = 'Lester Cheng'; $json_standings[8]['id'] = '142045'; $json_standings[8]['entry'] = '142045';
        $json_standings[9]['entry_name'] = 'Torres Magician'; $json_standings[9]['id'] = '44937'; $json_standings[9]['entry'] = '44937';
        $json_standings[10]['entry_name'] = 'Mark Sutherns'; $json_standings[10]['id'] = '370'; $json_standings[10]['entry'] = '370';
        $json_standings[11]['entry_name'] = 'Utkarsh Dalmia'; $json_standings[11]['id'] = '619'; $json_standings[11]['entry'] = '619';
        $json_standings[12]['entry_name'] = 'Sam Pater'; $json_standings[12]['id'] = '1583001'; $json_standings[12]['entry'] = '1583001';
        $json_standings[13]['entry_name'] = 'Matthew Jones'; $json_standings[13]['id'] = '97282'; $json_standings[13]['entry'] = '97282';
        $json_standings[14]['entry_name'] = 'Lee Cowen'; $json_standings[14]['id'] = '52537'; $json_standings[14]['entry'] = '52537';
        $json_standings[15]['entry_name'] = 'Pascal Evans'; $json_standings[15]['id'] = '425515'; $json_standings[15]['entry'] = '425515';
        $json_standings[16]['entry_name'] = 'Simon March'; $json_standings[16]['id'] = '46178'; $json_standings[16]['entry'] = '46178';
        $json_standings[17]['entry_name'] = 'Nick Triggerlips'; $json_standings[17]['id'] = '1191'; $json_standings[17]['entry'] = '1191';
        $json_standings[18]['entry_name'] = 'Milan Mihajlovic'; $json_standings[18]['id'] = '388177'; $json_standings[18]['entry'] = '388177';
        $json_standings[19]['entry_name'] = 'Ulrik Nylund'; $json_standings[19]['id'] = '725389'; $json_standings[19]['entry'] = '725389';
        $json_standings[20]['entry_name'] = 'Ben Crellin'; $json_standings[20]['id'] = '2238'; $json_standings[20]['entry'] = '2238';
        $json_standings[21]['entry_name'] = 'Chris McGurn'; $json_standings[21]['id'] = '137002'; $json_standings[21]['entry'] = '137002';
        $json_standings[22]['entry_name'] = 'Barry Manager'; $json_standings[22]['id'] = '965'; $json_standings[22]['entry'] = '965';
        $json_standings[23]['entry_name'] = 'Sir Moult'; $json_standings[23]['id'] = '1446309'; $json_standings[23]['entry'] = '1446309';
        $json_standings[24]['entry_name'] = 'Geoff Dance'; $json_standings[24]['id'] = '832'; $json_standings[24]['entry'] = '832';
        $json_standings[25]['entry_name'] = 'B.J. McNair'; $json_standings[25]['id'] = '905'; $json_standings[25]['entry'] = '905';
        $json_standings[26]['entry_name'] = 'Uwais Ahmed'; $json_standings[26]['id'] = '485465'; $json_standings[26]['entry'] = '485465';
        $json_standings[27]['entry_name'] = 'Spencer Li'; $json_standings[27]['id'] = '68'; $json_standings[27]['entry'] = '68';
        $json_standings[28]['entry_name'] = 'Matthew Martyniak'; $json_standings[28]['id'] = '749922'; $json_standings[28]['entry'] = '749922';
        $json_standings[29]['entry_name'] = 'AbuBakar Siddiq'; $json_standings[29]['id'] = '218'; $json_standings[29]['entry'] = '218';
        $json_standings[30]['entry_name'] = 'Luke Williams'; $json_standings[30]['id'] = '263'; $json_standings[30]['entry'] = '263';
        $json_standings[31]['entry_name'] = 'Jon Reeson'; $json_standings[31]['id'] = '9552'; $json_standings[31]['entry'] = '9552';
        $json_standings[32]['entry_name'] = 'Graeme Sumner'; $json_standings[32]['id'] = '345'; $json_standings[32]['entry'] = '345';
        $json_standings[33]['entry_name'] = 'Peter Kouwenberg'; $json_standings[33]['id'] = '36298'; $json_standings[33]['entry'] = '36298';
        $json_standings[34]['entry_name'] = 'Jack Kennedy'; $json_standings[34]['id'] = '51357'; $json_standings[34]['entry'] = '51357';
        $json_standings[35]['entry_name'] = 'Kelvin Travers'; $json_standings[35]['id'] = '37'; $json_standings[35]['entry'] = '37';
        $json_standings[36]['entry_name'] = 'Paul Marshman'; $json_standings[36]['id'] = '11421'; $json_standings[36]['entry'] = '11421';
        $json_standings[37]['entry_name'] = 'Ogie Nolan'; $json_standings[37]['id'] = '112597'; $json_standings[37]['entry'] = '112597';
        $json_standings[38]['entry_name'] = 'John Frisina'; $json_standings[38]['id'] = '1164821'; $json_standings[38]['entry'] = '1164821';
        $json_standings[39]['entry_name'] = 'Dimitri Nicolaou'; $json_standings[39]['id'] = '1887758'; $json_standings[39]['entry'] = '1887758';
        $json_standings[40]['entry_name'] = 'Tommy Wilson'; $json_standings[40]['id'] = '1264153'; $json_standings[40]['entry'] = '1264153';
        $json_standings[41]['entry_name'] = 'Ben Crabtree'; $json_standings[41]['id'] = '28412'; $json_standings[41]['entry'] = '28412';
        $json_standings[42]['entry_name'] = 'Cormac O\'Shaughnessy'; $json_standings[42]['id'] = '888793'; $json_standings[42]['entry'] = '888793';

//        $json_standings[1]['entry_name'] = 'Ville Ronka';
//        $json_standings[1]['id'] = '767113';
//        $json_standings[1]['entry'] = '767113';
//        $json_standings[0]['entry_name'] = 'Mark Suthern';
//        $json_standings[0]['id'] = '370';
//        $json_standings[0]['entry'] = '370';
//        $json_standings[2]['entry_name'] = 'Jay Egersdorff';
//        $json_standings[2]['id'] = '175574';
//        $json_standings[2]['entry'] = '175574';
//        $json_standings[3]['entry_name'] = 'Peter Kouwenberg';
//        $json_standings[3]['id'] = '36298';
//        $json_standings[3]['entry'] = '36298';
    }

    if (count($json_standings) > 0) {
        return get_league_picks_api($json_standings);
    } else {
        $json_response = file_get_contents($url_fpl . $url_standings_h2h . $league_id);
        $array = json_decode($json_response, true);
        $json_standings = $array['standings']['results'];
        if (count($json_standings) > 0) {
            return get_league_picks_api($json_standings);
        } else {
            return 'League ID not found.';
        }
    }

}

function get_league_picks_($league_id, $last_page, $get_overall = false) {

    $return = '';
    if (TIME_TIL_LIVE != 0) {
        return 'The FPL website is updating... coming to you live in ' . TIME_TIL_LIVE;
    } else {
        global $url_fpl, $url_standings, $url_standings_h2h;
//        if ($get_overall) {
//            $last_page = 20;
//        } else {
//            $last_page = 2;
//        }

        $json_standings = [];
        $fpl_league_update_status = 0;

        // check classic first
        $json_response = file_get_contents($url_fpl . $url_standings . $league_id . "?phase=1&le-page=1&ls-page=" . $page);
        $array = json_decode($json_response, true);
        $json_standings_per_page = $array['standings']['results'];
        if (count($json_standings_per_page) > 0) {
            $league_type = 'classic';
            $classic_league = true;
            $fpl_league_update_status = $array['update_status'];
            $json_standings = $json_standings_per_page;
        } else {
            $json_response_h2h = file_get_contents($url_fpl . $url_standings_h2h . $league_id);
            $array_h2h = json_decode($json_response_h2h, true);
            $json_standings_per_page = $array_h2h['standings']['results'];
            if (count($json_standings_per_page) > 0) {
                $league_type = 'h2h';
                $url_standings = $url_standings_h2h; // change endpoint
                $fpl_league_update_status = $array['update_status'];
                $json_standings = $json_standings_per_page;
            } else {
                $league_type = 'Unknown';
                $return .= 'League ID not found.';
            }
        }

        if ($league_type != 'Unknown') {
            for ($page = 2; $page <= $last_page; $page++) {
                $json_response = file_get_contents($url_fpl . $url_standings . $league_id . "?phase=1&le-page=1&ls-page=" . $page);
                $array = json_decode($json_response, true);
                $json_standings_per_page = $array['standings']['results'];
                $fpl_league_update_status = $array['update_status'];
                $json_standings = array_merge($json_standings, $json_standings_per_page);
            }

            $return .= get_league_picks_api_($json_standings, $fpl_league_update_status, $classic_league, $get_overall);
        }

        return $return;





        if (count($json_standings) > 0) {
            $return .= get_league_picks_api_($json_standings, $fpl_league_update_status, true, $get_overall);
        } else {
            $json_response_h2h = file_get_contents($url_fpl . $url_standings_h2h . $league_id);
            $array_h2h = json_decode($json_response_h2h, true);
            $json_standings = $array_h2h['standings']['results'];
            if (count($json_standings) > 0) {
                $return .= get_league_picks_api_($json_standings, $fpl_league_update_status, false, $get_overall);
            } else {
                $json_players = $array['new_entries']['results'];
                if (count($json_players) > 0 ) {
                    $table = '<table id="live_table" class="table table-striped table-condensed table-sm"><thead><tr><th>Players Joined</th></tr></thead><tbody>';
                    foreach ($json_players as $player) {
                        $table .= '<tr><td>' . $player['entry_name'] . '</td></tr>';
                    }
                    $table .= '</tbody></table>';
                    $return = $table;
                } else {
                    $return .= 'League ID not found.';
                }
            }
        }


//        for ($page = 1; $page <= $last_page; $page++) {
//            $json_response = file_get_contents($url_fpl . $url_standings . $league_id . "?phase=1&le-page=1&ls-page=" . $page);
//            $array = json_decode($json_response, true);
//            $json_standings = $array['standings']['results'];
//            $fpl_league_update_status = $array['update_status'];
//
//            if (count($json_standings) > 0) {
//                $return .= get_league_picks_api_($json_standings, $fpl_league_update_status, true, $get_overall);
//            } else {
//                $json_response_h2h = file_get_contents($url_fpl . $url_standings_h2h . $league_id);
//                $array_h2h = json_decode($json_response_h2h, true);
//                $json_standings = $array_h2h['standings']['results'];
//                if (count($json_standings) > 0) {
//                    $return .= get_league_picks_api_($json_standings, $fpl_league_update_status, false, $get_overall);
//                } else {
//                    $json_players = $array['new_entries']['results'];
//                    if (count($json_players) > 0 ) {
//                        $table = '<table id="live_table" class="table table-striped table-condensed table-sm"><thead><tr><th>Players Joined</th></tr></thead><tbody>';
//                        foreach ($json_players as $player) {
//
//                            $table .= '<tr><td>' . $player['entry_name'] . '</td></tr>';
//                        }
//                        $table .= '</tbody></table>';
//                        $return = $table;
//                    } else {
//                        $return .= 'League ID not found.';
//                    }
//                }
//            }
//        }
//        return $return;
    }
}

function first_of_week($league_id) {
    $db = New db;
    $has_been_checked = ($db -> select("SELECT count(1) cnt FROM league_members WHERE round = ".CURRENT_GW." AND league_id = $league_id;"))[0]['cnt'];
    if ($has_been_checked == 0) {
        $page = 1;
        $json_response = file_get_contents(URL_STANDINGS . $league_id . "?phase=1&le-page=1&ls-page=" . $page);
        $array = json_decode($json_response, true);
        $json_standings = $array['standings']['results'];
        if (count($json_standings) > 0) {
            get_start_of_week_data($json_standings);
            return get_league_picks_api_($json_standings, false, true);
        } else {
            $json_response = file_get_contents(URL_STANDINGS_H2H. $league_id);
            $array = json_decode($json_response, true);
            $json_standings = $array['standings']['results'];
            if (count($json_standings) > 0) {
                return get_start_of_week_data($json_standings);
            } else {
                return 'Error retrieving league ID!';
            }
        }
    } else {
        error_log('routed');
        $json_standings = $db -> select("SELECT * FROM league_members WHERE round = ".CURRENT_GW." AND league_id = $league_id;");
        return get_league_picks_api_($json_standings, false, true);
    }
}

function get_start_of_week_data($json_standings) {
    $sql = "insert into league_members (round, entry, league_id, entry_name, event_total, player_name, movement, rank, last_rank, rank_sort, total) values ";
    foreach ($json_standings as $entry) {
        $sql .= "(".CURRENT_GW.",".$entry['entry'].",".$entry['league'].",'".str_replace("'", "''", $entry['entry_name'])."',".$entry['event_total'].",'".str_replace("'", "''", $entry['player_name'])."','"
            .$entry['movement']."',".$entry['rank'].",".$entry['last_rank'].",".$entry['rank_sort'].",".$entry['total']."),";
//        $entry_obj = new entry();
//        if ($entry_obj->get($entry['entry'], true)) {
//            $picks = $entry_obj->picks;
//        }
    }
    $sql = rtrim($sql, ',');
    $db = New db;
    $db -> query($sql);
    return true;
}

function get_league_picks_api_($json_standings, $fpl_league_update_status, $classic_league, $get_overall = false) {
    $i = 0;
    $db_players = db_get_players_detail();
    $fixtures = db_get_current_fixtures();
    $sql_standings_insert = 'insert into overall_standings (entry_id, live_total) values ';
    foreach ($json_standings as $users => $user) {
        $gameweek_points = 0;
        $user_bench_points = 0;
        $p_played = 0;
        $p_yet_to_play = 0;
        $p_playing = 0;
        $captain = 0;
        $vice_captain = 0;
        $triple_captain = 0;
        $projected_points = 0;
        $vice_captain_points = 0;

        $captain_name = '';
        $vice_captain_name = '';

        $gk_did_not_play = 0;
        $def_did_not_play = 0;
        $mid_did_not_play = 0;
        $fwd_did_not_play = 0;
        $benched_starters = 0;
        $use_vice_captain = 0;

        $a_played = array();
        $a_playing = array();
        $a_to_play = array();
        $a_bench = array();
        $a_starters = array();

        $entry_obj = new entry();
        if ($entry_obj->get($user['entry'])) {
            # get transfer cost (only in classic leagues)
            if ($classic_league) {
                if ($entry_obj->calc_cost) {
                    $previous_total = $user['total'] - $user['event_total'];
                    $live_total = $previous_total - $entry_obj->transfer_cost;
                } else {
                    $previous_total = $user['total'] - $user['event_total'] + $entry_obj->transfer_cost;
                    $live_total = $previous_total - $entry_obj->transfer_cost;
                }
            } else {
                if ($entry_obj->calc_cost) {
                    $previous_total = $user['points_for'] - $user['points_total'];
                    $live_total = $previous_total - $entry_obj->transfer_cost;
                } else {
                    $previous_total = $user['points_for'] - $user['points_total'] + $entry_obj->transfer_cost;
                    $live_total = $previous_total - $entry_obj->transfer_cost;
                }
            }

            // loop through picks for each team
            foreach ($entry_obj->picks as $pick) {
                // get player and fixture objects

                // if player has a double gameweek
                $pick_element = array_search($pick['element'], array_column($db_players, 'id'));
                $player_obj = $db_players[$pick_element];
                $player_obj_first_game = $player_obj;
                $player_obj_dgw = $db_players[$pick_element + 1];

                if ($player_obj['id'] == $player_obj_dgw['id']) {
                    $matches_to_process_for_player = 1;
                } else {
                    $matches_to_process_for_player = 1;
                }

                for ($dgw_counter = 1; $dgw_counter <= $matches_to_process_for_player; $dgw_counter++) {
                    if ($dgw_counter == 2) {
                        $player_obj = $player_obj_dgw;
                    }
                    $fixture = $player_obj['fixture'] - 1;
                    // if fixture DNE (blank gw)
                    if ($fixture == -1) {
                        if ($pick['multiplier'] == 2) {
                            $captain = $pick['element'];
                            $captain_name = $player_obj['web_name'];
                        } else if ($pick['multiplier'] == 3) {
                            $triple_captain = $pick['element'];
                            $captain_name = $player_obj['web_name'];
                        } else if ($pick['is_vice_captain'] == 'true') {
                            $vice_captain = $pick['element'];
                            $vice_captain_name = $player_obj['web_name'];
                        }
                        if ($pick['position'] <= 11 || $entry_obj->chip == 'bboost') {
                            if ($player_obj['id'] == $captain || $player_obj['id'] == $triple_captain) {
                                $a_played[] = '<b>' . $player_obj['web_name'] . ' (B)</b>';
                                $a_starters[] = '<b>' . $player_obj['web_name'] . ' (B)</b>';
                            } else {
                                $a_played[] = $player_obj['web_name'] . ' (B)';
                                $a_starters[] = $player_obj['web_name'] . ' (B)';
                            }
                            if ($player_obj['element_type'] == 1) {
                                $gk_did_not_play = 1;
                            } else {
                                $benched_starters++; // why is this here? nvm this is all for a fixture not existing
                            }
                            $p_played++;
                        } else {
                            $a_bench[] = $player_obj['web_name'] . ' (B)';
                        }
                    } else {
                        $fixture_obj = $fixtures[$fixture];
//                        if(!empty($fixtures[$player_obj_dgw['fixture'] - 1])) {
                            $fixture_obj_dgw = $fixtures[$player_obj_dgw['fixture'] - 1];
//                        } else {
//                            $fixture_obj_dgw = array();
//                        }

                        if ($pick['multiplier'] == 2) {
                            $captain = $pick['element'];
                        } else if ($pick['multiplier'] == 3) {
                            $triple_captain = $pick['element'];
                        } else if ($pick['is_vice_captain'] == 'true') {
                            $vice_captain = $pick['element'];
                            $vice_captain_name = $player_obj['web_name'];
                        }

                        // if a starter
                        if ($pick['position'] <= 11 || $entry_obj->chip == 'bboost') {

                            if ($dgw_counter == 1) {
                                if ($player_obj['element_type'] == 2) {
                                    $entry_obj->def_count++;
                                } else if ($player_obj['element_type'] == 3) {
                                    $entry_obj->mid_count++;
                                } else if ($player_obj['element_type'] == 4) {
                                    $entry_obj->fwd_count++;
                                }
                            }

                            // if leagues haven't been updated and fixture != finished
                            if ($fpl_league_update_status == 0 and !$fixture_obj['finished']) {
//                            if (!$fixture_obj['finished']) {
                                $points = ($player_obj['total_points'] + $player_obj['bonus']) * $pick['multiplier'];
                                $bonus_to_display = ($player_obj['bonus'] > 0) ? (' + ' . ($player_obj['bonus'] * $pick['multiplier'])) : '';
                                $points_to_display = ($player_obj['total_points'] * $pick['multiplier']) . $bonus_to_display;
//                                $points_to_display = $points;
                                // if leagues haven't been updated and fixture == finished
                            } else {
                                $points = $player_obj['total_points'] * $pick['multiplier'];
                                $points_to_display = $points;
                            }

                            // create the list of players
                            // if fixture == finished
                            if ($fixture_obj['finished_provisional']) {
                                $p_played++;
                                if ($player_obj['id'] == $captain || $player_obj['id'] == $triple_captain) {
                                    $captain_name = $player_obj['web_name'];
                                    if ($player_obj['minutes'] == 0) {
                                        $a_played[] = '<b>' . $player_obj['web_name'] . ' (B)</b>';
                                        if ($player_obj['element_type'] != 1) { $benched_starters++; }
                                        if ($matches_to_process_for_player == 1) {
                                            $use_vice_captain = 1;
                                        } else if ($dgw_counter > 1 and $player_obj_first_game['minutes'] == 0) {
                                            $use_vice_captain = 1;
                                        }
                                    } else {
                                        $a_played[] = '<b>' . $player_obj['web_name'] . ' (' . $points_to_display . ')</b>';
                                    }
                                } else if ($player_obj['minutes'] == 0) {
                                    $a_played[] = $player_obj['web_name'] . ' (B)';

                                    if ($matches_to_process_for_player == 1 or ($matches_to_process_for_player == 2 and $dgw_counter == 1 and $player_obj_dgw['minutes'] == 0 and $fixture_obj_dgw['started'])) {
                                        if ($player_obj['element_type'] == 1) {
                                            $gk_did_not_play = 1;
                                        } else if ($player_obj['element_type'] == 2) {
                                            $def_did_not_play++;
                                            $entry_obj->def_count--;
                                        } else if ($player_obj['element_type'] == 3) {
                                            $mid_did_not_play++;
                                            $entry_obj->mid_count--;
                                        } else if ($player_obj['element_type'] == 4) {
                                            $fwd_did_not_play++;
                                            $entry_obj->fwd_count--;
                                        }
                                        if ($player_obj['element_type'] != 1) {
                                            $benched_starters++;
                                        }
                                    }
                                } else {
                                    $a_played[] = $player_obj['web_name'] . ' (' . $points_to_display . ')';
                                    if ($player_obj['id'] == $vice_captain) {
                                        $vice_captain_points = $points_to_display;
                                    }
                                }
                                // if fixture == started
                            } else if ($fixture_obj['started']) {
                                $p_playing++;
                                if ($player_obj['id'] == $captain || $player_obj['id'] == $triple_captain) {
                                    $captain_name = $player_obj['web_name'];
                                    if ($player_obj['minutes'] == 0) {
                                        $a_playing[] = '<b>' . $player_obj['web_name'] . ' (B)</b>';
                                        if ($player_obj['element_type'] != 1) { $benched_starters++; }
                                        if ($matches_to_process_for_player == 1) {
                                            $use_vice_captain = 1;
                                        } else if ($dgw_counter > 1 and $player_obj_first_game['minutes'] == 0) {
                                            $use_vice_captain = 1;
                                        }
                                    } else {
                                        $a_playing[] = '<b>' . $player_obj['web_name'] . ' (' . $points_to_display . ')</b>';
                                    }
                                } else if ($player_obj['minutes'] == 0) {
                                    $a_playing[] = $player_obj['web_name'] . ' (B)';
                                    if ($matches_to_process_for_player == 1 or ($dgw_counter > 1 and $player_obj_first_game['minutes'] == 0)) {
                                        if ($player_obj['element_type'] != 1) {
                                            $benched_starters++;
                                        }
                                        if ($player_obj['element_type'] == 1) {
                                            $gk_did_not_play = 1;
                                        } else if ($player_obj['element_type'] == 2) {
                                            $def_did_not_play++;
                                            $entry_obj->def_count--;
                                        } else if ($player_obj['element_type'] == 3) {
                                            $mid_did_not_play++;
                                            $entry_obj->mid_count--;
                                        } else if ($player_obj['element_type'] == 4) {
                                            $fwd_did_not_play++;
                                            $entry_obj->fwd_count--;
                                        }
                                    }
                                } else {
                                    $a_playing[] = $player_obj['web_name'] . ' (' . $points_to_display . ')';
                                    if ($player_obj['id'] == $vice_captain) {
                                        $vice_captain_points = $points_to_display;
                                    }
                                }
                                // if fixture != started
                            } else {
                                $p_yet_to_play++;
                                if ($player_obj['id'] == $captain || $player_obj['id'] == $triple_captain) {
                                    $captain_name = $player_obj['web_name'];
                                    if ($player_obj['id'] == $captain) {
                                        $player_projected_points = $player_obj['form'] * 2;
                                    } else {
                                        $player_projected_points = $player_obj['form'] * 3;
                                    }
                                    $a_to_play[] = '<b>' . $player_obj['web_name'] . ' (' . $player_projected_points . ')</b>';
                                } else {
                                    $player_projected_points = $player_obj['form'];
                                    $a_to_play[] = $player_obj['web_name'] . ' (' . $player_projected_points . ')</b>';
                                }
                                $projected_points .= $player_projected_points;
                            }

                            // if fixture is finished and zero minutes played, mark benched; else show points
                            if ($player_obj['minutes'] == 0 and !$fixture_obj['started']) {
                                $points_to_display = '';
                            } else if ($player_obj['minutes'] == 0) {
                                $points_to_display = ' (B)';
                            } else {
                                $points_to_display = ' (' . $points_to_display . ')';
                            }

                            // bold captains
                            if ($player_obj['id'] == $captain || $player_obj['id'] == $triple_captain) {
                                $a_starters[] = '<b>' . $player_obj['web_name'] . $points_to_display . '</b>';
                            } else {
                                $a_starters[] = $player_obj['web_name'] . $points_to_display;
                            }

                            // add points to gameweek points
                            $gameweek_points += $points;
                        }
                        else {
                            if ($fpl_league_update_status == 0 and !$fixture_obj['finished']) {
                                $points = ($player_obj['total_points'] + $player_obj['bonus']) * $pick['multiplier'];
                                $bonus_to_display = ($player_obj['bonus'] > 0) ? (' + ' . ($player_obj['bonus'] * $pick['multiplier'])) : '';
                                $points_to_display = ($player_obj['total_points'] * $pick['multiplier']) . $bonus_to_display;
                            } else if ($fpl_league_update_status == 0 and $fixture_obj['finished']) {
                                $points = $player_obj['total_points'] * $pick['multiplier'];
                                $points_to_display = ($player_obj['total_points'] * $pick['multiplier']);
                            } else {
                                $points_to_display = ($player_obj['total_points'] * $pick['multiplier']);
                                $points = $player_obj['total_points'] * $pick['multiplier'];
                            }

                            if (($gk_did_not_play == 1 and $pick['position'] == 12) and ($player_obj['minutes'] != 0 or $fixture_obj['started'])) {
                                $gameweek_points += $points;
                                $a_starters[] = '<i>' . $player_obj['web_name'] . ' (' . $points_to_display . ')</i>';
                                $a_bench[] = '<i>' . $player_obj['web_name'] . ' (' . $points_to_display . ')</i>';
                            } else if ($pick['position'] == 12) {
                                $user_bench_points += $points;
                                $a_bench[] = $player_obj['web_name'] . ' (' . $points_to_display . ')';
                            }

                            if ($benched_starters >= 1) {
                                if ($pick['position'] == 13) {
                                    if ((($entry_obj->def_count >= 3 and $entry_obj->fwd_count >= 1) or $benched_starters >= 2) and ($player_obj['minutes'] != 0 or !$fixture_obj['started'])) {
                                        $gameweek_points += $points;
                                        $a_starters[] = '<i>' . $player_obj['web_name'] . ' (' . $points_to_display . ')</i>';
                                        $a_bench[] = '<i>' . $player_obj['web_name'] . ' (' . $points_to_display . ')</i>';
                                        if ($player_obj['element_type'] == 2) {
                                            $entry_obj->def_count++;
                                        } else if ($player_obj['element_type'] == 3) {
                                            $entry_obj->mid_count++;
                                        } else if ($player_obj['element_type'] == 4) {
                                            $entry_obj->fwd_count++;
                                        }
                                    } else {
                                        $benched_starters++; // add to bench because the first sub is technically now a benched starter
                                        $user_bench_points += $points;
                                        $a_bench[] = $player_obj['web_name'] . ' (' . $points_to_display . ')';
                                    }
                                }

                                if (($pick['position'] == 14 and $benched_starters >= 2) and ($player_obj['minutes'] != 0 or $fixture_obj['started'])) {
                                    if ($entry_obj->def_count >= 3 and $entry_obj->fwd_count >= 1) {
                                        $gameweek_points += $points;
                                        $a_starters[] = '<i>' . $player_obj['web_name'] . ' (' . $points_to_display . ')</i>';
                                        $a_bench[] = '<i>' . $player_obj['web_name'] . ' (' . $points_to_display . ')</i>';
                                        if ($player_obj['element_type'] == 2) {
                                            $entry_obj->def_count++;
                                        } else if ($player_obj['element_type'] == 3) {
                                            $entry_obj->mid_count++;
                                        } else if ($player_obj['element_type'] == 4) {
                                            $entry_obj->fwd_count++;
                                        }
                                    } else if ($entry_obj->def_count >= 2) {
                                        $gameweek_points += $points;
                                        $a_starters[] = '<i>' . $player_obj['web_name'] . ' (' . $points_to_display . ')</i>';
                                        $a_bench[] = '<i>' . $player_obj['web_name'] . ' (' . $points_to_display . ')</i>';
                                        $entry_obj->def_count++;
                                    } else if ($entry_obj->fwd_count >= 0) {
                                         // if ($player_obj[]) may want some logic here saying if minutes = 0 and fixture = started, skip this guy too
                                        $gameweek_points += $points;
                                        $a_starters[] = '<i>' . $player_obj['web_name'] . ' (' . $points_to_display . ')</i>';
                                        $a_bench[] = '<i>' . $player_obj['web_name'] . ' (' . $points_to_display . ')</i>';
                                        $entry_obj->fwd_count++;
                                    } else {
                                        $benched_starters++;
                                        $user_bench_points += $points;
                                        $a_bench[] = $player_obj['web_name'] . ' (' . $points_to_display . ')';
                                    }
                                } else if ($pick['position'] == 14) {
                                    $user_bench_points += $points;
                                    $a_bench[] = $player_obj['web_name'] . ' (' . $points_to_display . ')';
                                }

                                if (($pick['position'] == 15 and $benched_starters >= 3) and ($player_obj['minutes'] != 0 or $fixture_obj['started'])) {
                                    if ($entry_obj->def_count >= 3 and $entry_obj->fwd_count >= 1) {
                                        $gameweek_points += $points;
                                        $a_starters[] = '<i>' . $player_obj['web_name'] . ' (' . $points_to_display . ')</i>';
                                        $a_bench[] = '<i>' . $player_obj['web_name'] . ' (' . $points_to_display . ')</i>';
                                        if ($player_obj['element_type'] == 2) {
                                            $entry_obj->def_count++;
                                        } else if ($player_obj['element_type'] == 3) {
                                            $entry_obj->mid_count++;
                                        } else if ($player_obj['element_type'] == 4) {
                                            $entry_obj->fwd_count++;
                                        }
                                    } else if ($entry_obj->def_count >= 2) {
                                        $gameweek_points += $points;
                                        $a_starters[] = '<i>' . $player_obj['web_name'] . ' (' . $points_to_display . ')</i>';
                                        $a_bench[] = '<i>' . $player_obj['web_name'] . ' (' . $points_to_display . ')</i>';
                                        $entry_obj->def_count++;
                                    } else if ($entry_obj->fwd_count >= 0) {
                                        $gameweek_points += $points;
                                        $a_starters[] = '<i>' . $player_obj['web_name'] . ' (' . $points_to_display . ')</i>';
                                        $a_bench[] = '<i>' . $player_obj['web_name'] . ' (' . $points_to_display . ')</i>';
                                        $entry_obj->fwd_count++;
                                    } else {
                                        $user_bench_points += $points;
                                        $a_bench[] = $player_obj['web_name'] . ' (' . $points_to_display . ')';
                                    }
                                } else if ($pick['position'] == 15) {
                                    $user_bench_points += $points;
                                    $a_bench[] = $player_obj['web_name'] . ' (' . $points_to_display . ')';
                                }

                            } else if ($pick['position'] != 12) {
                                $user_bench_points += $points;
                                $a_bench[] = $player_obj['web_name'] . ' (' . $points_to_display . ')';
                            }
                        }
                    }
                }
            }

            # add in vice captain points if necessary
            if ($use_vice_captain == 1) {
                $gameweek_points += $vice_captain_points;
                $a_starters[] = '';
                $a_starters[] = '<i>VC: ' . $vice_captain_name . ' +' . $vice_captain_points . '</i>';
            }

            $json_standings[$i]['previous_total'] = $previous_total;
            $json_standings[$i]['live_total'] = $live_total + $gameweek_points;
            $json_standings[$i]['projected_total'] = $live_total + $gameweek_points + round($projected_points, 0);
            $json_standings[$i]['gameweek_points'] = $gameweek_points;
            $json_standings[$i]['user_bench_points'] = $user_bench_points;

            // number of players in each status
            $json_standings[$i]['yet_to_play'] = $p_yet_to_play;
            $json_standings[$i]['playing'] = $p_playing;
            $json_standings[$i]['played'] = $p_played;

            // list of players in each status
            $json_standings[$i]['a_played'] = $a_played;
            $json_standings[$i]['a_playing'] = $a_playing;
            $json_standings[$i]['a_to_play'] = $a_to_play;
            $json_standings[$i]['a_bench'] = $a_bench;
            $json_standings[$i]['a_starters'] = $a_starters;

            $json_standings[$i]['transfer_cost'] = $entry_obj->transfer_cost;
            $json_standings[$i]['team_value'] = $entry_obj->team_value;
            $json_standings[$i]['calc_cost'] = $entry_obj->calc_cost;
            $json_standings[$i]['entry'] = $user['entry'];

            if ($triple_captain != 0) {
                $captain_name = $captain_name . ' (3x)';
            }
            $json_standings[$i]['captain_name'] = $captain_name; // . ' (' . $vice_captain_name . ')';
            $json_standings[$i]['event_transfers'] = $entry_obj->event_transfers;
            // $json_standings[$i]['points_so_far'] = $entry_obj->points_so_far;

            if ($get_overall) {
                $sql_standings_insert = rtrim($sql_standings_insert, ',');
                $sql_standings_insert .= '(' . $entry_obj->entry_id . ',' . $json_standings[$i]['live_total'] . '), ';
            }
            $i++;
        };
    }
    if ($get_overall) {
        $db = New db();
        $sql_standings_insert = rtrim($sql_standings_insert, ', ');
        $sql_standings_insert .= ' ON DUPLICATE KEY UPDATE live_total=VALUES(live_total);';
        $r = $db->query($sql_standings_insert);
        if ($r != 1) {
            error_log($r);
            error_log($sql_standings_insert);
        };
        $table = 'overall updated';
    } else {
        $json_standings = array_order($json_standings, 'live_total', SORT_DESC);
        $i = 1;
        $table = '<table id="live_table" class="table table-striped table-condensed table-sm"><thead><tr>';
        $table .= '<th></th><th colspan="3" style=""># Players</th><th colspan="3"># Points</th><th colspan="4">Totals</th></tr><tr>';
        $table .= th('Player');
        $table .= th('Played', 'Players that have completed their match for this gameweek.');
        $table .= th('Playing', 'Players currently in a match.');
        $table .= th('To Play', 'Players that have yet to play this gameweek (Current Form).');
        $table .= th('Pts', 'Player points for this gameweek.');
        $table .= th('Bench', 'Total bench points for this gameweek.');
        // $table .= th('Cost', 'Cost of the transfers made this gameweek.');
        $table .= th('Trans', 'Transfers made this gameweek.');
        $table .= th('Live Total', 'Live Total Points (Live Rank)');
        $table .= th('Prev Total', 'Previous Week Total Points');
        $table .= th('Team Value', 'Team Value + Money in the Bank');
        $table .= th('Captain', 'Captain');
        // $table .= th('Proj Total', 'Projected Total (Points from Players Played/Playing + Form from Players Yet to Play)');

        $table .= '</tr></thead><tbody>';
        foreach ($json_standings as $users => $user) {
            $starters_table = '<table><tbody>';
            foreach ($user['a_starters'] as $p_started) {
                $starters_table .= '<tr><td>' . $p_started . '</td></tr>';
            }
            $starters_table .= '</tbody></table>';

            $projected_table = '<table><tbody>';

            $played_table = '<table><tbody>';
            foreach ($user['a_played'] as $p_played) {
                $played_table .= '<tr><td>' . $p_played . '</td></tr>';
//            $starters_table .= '<tr><td>'.$p_played.'</td></tr>';
                $projected_table .= '<tr><td>' . $p_played . '</td></tr>';
            }
            $played_table .= '</tbody></table>';

            $playing_table = '<table><tbody>';
            foreach ($user['a_playing'] as $p_playing) {
                $playing_table .= '<tr><td>' . $p_playing . '</td></tr>';
//            $starters_table .= '<tr><td>'.$p_playing.'</td></tr>';
                $projected_table .= '<tr><td>' . $p_playing . '</td></tr>';
            }
            $playing_table .= '</tbody></table>';

            $to_play_table = '<table><tbody>';
            foreach ($user['a_to_play'] as $p_to_play) {
                $to_play_table .= '<tr><td>' . $p_to_play . '</td></tr>';
//            $starters_table .= '<tr><td>'.$p_to_play.'</td></tr>';
                $projected_table .= '<tr><td>' . $p_to_play . '</td></tr>';
            }
            $to_play_table .= '</tbody></table>';

            $bench_table = '<table><tbody>';
            foreach ($user['a_bench'] as $p_bench) {
                $bench_table .= '<tr><td>' . $p_bench . '</td></tr>';
            }
            $bench_table .= '</tbody></table>';

            $projected_table .= '</tbody></table>';


            $table .= '<tr class="entry_row" entry="' . $user['entry'] . '">';
            $table .= td('<a href="https://fantasy.premierleague.com/a/team/' . $user['entry'] . '/event/' . CURRENT_GW . '" target="_blank"><img src="i/prm.png" width="5%" /></a></a><a title="<b>'. $user['player_name'] .'</b>" data-html="true" data-toggle="popover" data-trigger="click" data-placement="right" data-container="body" data-content="">' . $user['entry_name'] . '</a>');
            $table .= '<td data-order="' . $user['played'] . '"><a class="played" title="<b>Played</b>" data-html="true" data-toggle="popover" data-trigger="click" data-placement="right" data-container="body" data-content="' . $played_table . '" >' . $user['played'] . '</a></td>';
            $table .= '<td data-order="' . $user['playing'] . '"><a class="playing" title="<b>Playing</b>" data-html="true" data-toggle="popover" data-trigger="click" data-placement="left" data-container="body" data-content="' . $playing_table . '" >' . $user['playing'] . '</a></td>';
            $table .= '<td data-order="' . $user['yet_to_play'] . '"><a class="yet-to-play" title="<b>Yet to Play (Form)</b>" data-html="true" data-toggle="popover" data-trigger="click" data-placement="left" data-container="body" data-content="' . $to_play_table . '" >' . $user['yet_to_play'] . '</a></td>';
            $table .= '<td data-order="' . $user['gameweek_points'] . '"><a class="live_points" title="<b>Live Points</b>" data-html="true" data-toggle="popover" data-trigger="click" data-placement="left" data-container="body" data-content="' . $starters_table . '" >' . $user['gameweek_points'] . '</a></td>';
            $table .= '<td data-order="' . $user['user_bench_points'] . '"><a class="bench" title="<b>Bench Points</b>" data-html="true" data-toggle="popover" data-trigger="click" data-placement="left" data-container="body" data-content="' . $bench_table . '" >' . $user['user_bench_points'] . '</a></td>';
            $table .= '<td data-order="' . $user['event_transfers'] . '"><a data-html="true" title="<b>Transfer Cost: ' . $user['transfer_cost'] . '</b>" data-content="Loading..." data-toggle="popover" data-trigger="click" data-placement="left" data-container="body" id="transfers_' . $user['entry'] . '" class="transfers" data-entry="' . $user['entry'] . '">' . $user['event_transfers'] . '</a></td>';

            $table .= td_order($user['live_total'] . color_rank($user, $i), $user['live_total']);
//            $table .= td_order($user['previous_total'] . ' (' . $user['last_rank'] . ')', $user['previous_total']);
            $table .= td_order($user['previous_total'] . '', $user['previous_total']);

            $table .= td($user['team_value']);
            $table .= td($user['captain_name']);
            // $table .= td_order($user['projected_total'], $user['projected_total']);
            // $table .= '<td data-order="'.$user['projected_total'].'"><a class="proj_points" title="<b>Projected Total</b>" data-html="true" data-toggle="popover" data-trigger="click" data-placement="left" data-container="body" data-content="'.$projected_table.'" >'.$user['projected_total'].'</a></td>';
            $table .= '</tr>';
            $i++;
        }
        $table .= '</tbody></table>';
    }
    return $table;
}

function move_to_starter() {

}

function get_league_picks_api($json_standings) {
    global $url_fpl;
    $pick_tracker = array();
    $league_entry_ids = array();
    foreach ($json_standings as $entry) {
        $entry_id = $entry['entry'];
        $league_entry_ids[$entry_id]['entry_name'] =  $entry['entry_name'];
        $league_entry_ids[$entry_id]['id'] =  $entry['id'];
        $league_entry_ids[$entry_id]['player_name'] =  $entry['player_name'];
        $league_entry_ids[$entry_id]['rank'] =  $entry['rank'];
        $resp = file_get_contents($url_fpl . "entry/" . $entry_id . "/event/" . CURRENT_GW . "/picks");
        $arr = json_decode($resp, true);
        $picks = $arr['picks'];

        foreach ($picks as $pick) {
            $player = $pick['element'];
            $entry_arr = array('entry' => $entry_id, 'multiplier' => $pick['multiplier'], 'position' => $pick['position']);
            $pick_tracker[$player][] = $entry_arr;
        }
    }

    arsort($pick_tracker);
    $num_players_in_league = sizeof($league_entry_ids);

    $db_players = db_get_players();
    $table = '<table id="selections" class="table table-striped table-condensed table-sm"><thead><tr><th>Player</th><th title="Overall Player Ownership">Overall Owned</th><th title="Mini-League Player Ownership">League Owned</th><th>GW Points</th><th>Picks</th></tr></thead><tbody>';
    foreach ($pick_tracker as $player => $picks) {
        $player_info = $db_players[($player -1)];
        $picks_in_league = sizeof($picks);

        $inner_table = '<table><tbody>';
        foreach ($picks as $entry) {
            $inner_rank = ($league_entry_ids[$entry['entry']]['rank'] != null) ? ' ('.$league_entry_ids[$entry['entry']]['rank'].')' : '';
            $inner_value = $league_entry_ids[$entry['entry']]['entry_name'].$inner_rank;
            if ($entry['multiplier'] > 1) {
                $inner_value = '<b>'.$inner_value.'</b>';
            } elseif ($entry['position'] > 11) {
                $inner_value = '<strike>'.$inner_value.'</strike>';
            }
            $inner_table .= '<tr><td>'.str_replace('"', "", $inner_value).'</td></tr>';
        }
        $inner_table .= '</tbody></table>';

        $table .= '<tr>';
        $table .= '<td>'.$player_info['web_name'].'</td>';
        //$table .= '<td>'.$player_info['name'].'</td>';
        $table .= '<td>'.round($player_info['selected_by_percent'],1).'%</td>';
        $table .= '<td>'.diff_percent_ownership($player_info['selected_by_percent'], $picks_in_league, $num_players_in_league).'</td>';
        $table .= '<td>'.$player_info['event_points'].'</td>';
        $table .= '<td data-order="'.$picks_in_league.'"><a title="<b>'.$player_info['web_name'].'</b>" data-html="true" data-toggle="popover" data-trigger="click" data-placement="left" data-container="body" data-content="'.$inner_table.'" >'.$picks_in_league.'</a></td>';
        $table .= '</tr>';
    }
    $table .= '</tbody></table>';
    return $table;
}

function diff_percent_ownership($percent_owned_overall, $picks_in_league, $num_players_in_league){
    $percent_owned_league = ($picks_in_league / $num_players_in_league) * 100;
    $percent_diff = round($percent_owned_league - ($percent_owned_overall),1);

    if ($percent_diff > 0) {
        $color = 'green';
    } else {
        $color = 'red';
    }

    return sprintf('<span style="color: %s;">%s</span>', $color, round($percent_owned_league,1) . '%');

    return $percent_owned_overall .' -> ' . $percent_owned_league . ' = ' . round($percent_diff, 1);
}

function get_teamsheets($team_id, $screen_height) {
    $sql = 'select p.web_name, ps.left, ps.top, p.code, cast((now_cost / 10) as decimal(3,1)) now_cost, cast(((p.total_points / p.minutes) * 90) as decimal(5,1)) projection
            from players p 
            join teams t on p.team = t.id
            join teamsheets ts on ts.id = p.id
            join positions ps on ps.position = ts.position
            where t.id = '.$team_id.';';
    $db = New db;
    $rows = $db->select($sql);
    $body = '';

    foreach ($rows as $player) {
        $top = ($player['top'] * .72) ?: 0;
        $left = ($player['left'] * .55) ?: 0;

        if ($screen_height > 800) {
            $top = $top + ($top * 0.25);
            $left = $left * 1.5;
            $pc = '-pc';
        } else {
            $pc = '';
        }

        $body .= '<div class="player'.$pc.'" style="top: '.$top.'px; left: '.$left.'px;">';
        $body .= '<img class="player-image'.$pc.'" src="http://platform-static-files.s3.amazonaws.com/premierleague/photos/players/110x140/p'.$player['code'].'.png"/>';
        // $body .= '<div class="player-label">'.$player['web_name'].' (£'.$player['now_cost'].')</div>';
        $body .= '<div class="player-label'.$pc.'">'.$player['web_name'].' ('.$player['projection'].')</div>';
        $body .= '</div>';
    }
    return $body;
}

function fixtures_table($shown_gameweeks) {
    $CURRENT_GW = CURRENT_GW + 1;
    $db = New db;
    $rows = $db->select(getsql());

    $strength = get_strength();

    $body = '<table id="fixtures" class="table table-striped table-condensed table-sm"><thead><tr><th>Team</th><th>Overall</th>';
    for ($i = $CURRENT_GW; $i <= ($CURRENT_GW + $shown_gameweeks - 1) and $i <= 38; $i++) {
        $body .= '<th>GW ' . $i . '</th>';
    }
    $body .= '</tr></thead><tbody>';

    foreach ($rows as $row) {
        $overall = 0;
        $bodyTemp = '';
        $body .= '<tr>';
        $body .= '<td>' . $row['name'] . '</td>';
        for ($i = $CURRENT_GW; $i <= ($CURRENT_GW + $shown_gameweeks - 1) and $i <= 38; $i++) {
            if (substr($row['gw' . $i], -2, 1) == 'H') {
                $at = 'home';
            } else {
                $at = 'away';
            }
            $team = substr($row['gw' . $i], 0, -4);
            $bodyTemp .= '<td><p style=" min-width: 130px; height: 0px;">' . format($row['gw' . $i], $strength[$team][$at], $at) . '</p></td>';
            $overall += $strength[$team][$at];
        }
        // echo $overall .' / ' . $gw . ', ';
        $ovr = round(($overall / $shown_gameweeks), 0);
        $body .= '<td>' . format($ovr, $ovr) . '</td>';
        $body .= $bodyTemp;

        $body .= '</tr>';
    }
    $body .= '</tbody></table>';

    return $body;
}

function standings_table($league_id) {
    $db = New db;
    $rows = $db -> select('select entry_name, total, rank, last_rank, value, bank from standings s
                        join entries e on e.player_id = s.player_id and e.`gameweek` = (select max(gameweek) from entries)
                        where league_id = ' . $league_id . ';');

    $thead = '<thead><tr>';
    $thead .=     '<th>Player</th>';
    $thead .=     '<th>Points</th>';
    $thead .=     '<th>Team Value</th>';
    $thead .=     '<th>Bank</th>';
    $thead .=     '<th></th>';
    $thead .= '</tr></thead>';

    $tbody = '<tbody>';

    if ($rows) {
        foreach ($rows as $row) {
            $tbody .= '<tr>';
            $tbody .= '<td>' . $row['entry_name'] . '</td>';
            $tbody .= '<td>' . $row['total'] . '</td>';
            $tbody .= '<td>' . $row['value'] . '</td>';
            $tbody .= '<td>' . $row['bank'] . '</td>';

            if ($row['rank'] < $row['last_rank']) {
                $tbody .= '<td><img src="i/plus16.png"></td>';
            } elseif ($row['rank'] > $row['last_rank']) {
                $tbody .= '<td><img src="i/negative16.png"></td>';
            } else {
                $tbody .= '<td></td>';
            }

            $tbody .= '</tr>';
        }
        $tbody .= '</tbody>';
    } else {
        $tbody = '<tr><td colspan="5">No Data Available</td></tr>';
    }

    $table = $thead . $tbody;
    return $table;
}

function power_table($league_id) {
    $db = New db;
    $sql = 'select s.entry_name, sum(e.points) pts, s.total from standings s 
        join entries e on s.player_id = e.player_id
        where e.gameweek >= '. (CURRENT_GW - 5).' and league_id = '. $league_id.'
        group by s.player_id, s.entry_name, s.total
        order by pts desc;';
    $rows = $db -> select($sql);

    $body = '<tr><td>No Data Available</td></tr>';

    foreach ($rows as $row) {
        $body = '<tr>';
        $body .= '<td>' . $row['entry_name'] . '</td>';
        $body .= '<td>' . $row['pts'] . '</td>';
        $body .= '<td>' . $row['total'] . '</td>';
        $body .= '</tr>';
    }

    return $body;
}

function db_get_players() {
    $db = New db;
    $rows = $db -> select(
        'select p.id, p.web_name, t.name, cast((now_cost / 10) as decimal(3,1)) now_cost, points_per_game,
      cast(((points_per_game - 2) / (now_cost / 10)) as decimal(5,3)) vapm,
     goals_scored, assists, clean_sheets, bps, element_type, total_points, minutes, event_points, selected_by_percent, p.code
     ,ft.gw'.(CURRENT_GW + 1).'
                from players p 
                join teams t on p.team = t.id
                join fixture_table ft on ft.`name` = t.name;');

//    $rows = $db -> select(
//        'select p.id, p.web_name, t.name, cast((now_cost / 10) as decimal(3,1)) now_cost, points_per_game,
//      cast(((points_per_game - 2) / (now_cost / 10)) as decimal(5,3)) vapm,
//     goals_scored, assists, clean_sheets, bps, element_type, total_points, minutes, event_points, selected_by_percent, p.code
//     ,ft.gw'.(CURRENT_GW + 1).',ft.gw'.(CURRENT_GW + 2).',ft.gw'.(CURRENT_GW + 3).',ft.gw'.(CURRENT_GW + 4).'
//                from players p
//                join teams t on p.team = t.id
//                join fixture_table ft on ft.`name` = t.name;');
    return $rows;
}

function db_get_players_for_my_team() {
    $db = New db;
//    $rows = $db -> select(
//        'select p.web_name, p.code, p.element_type, ft.gw'.(CURRENT_GW + 1).'
//                from players p
//                join teams t on p.team = t.id
//                join fixture_table ft on ft.`name` = t.name;');

    $rows = $db -> select(
        'select p.web_name, p.code, p.element_type, ft.gw'.(CURRENT_GW + 1).',ft.gw'.(CURRENT_GW + 2).',ft.gw'.(CURRENT_GW + 3).',ft.gw'.(CURRENT_GW + 4).'
                from players p
                join teams t on p.team = t.id
                join fixture_table ft on ft.`name` = t.name;');
    return $rows;
}

function db_get_players_detail() {
    $db = New db;
    $rows = $db -> select(
        'SELECT
                  pd.id player_match_id,
                  pd.element as id,
                  pd.bps,
                  pd.total_points,
                  pd.fixture,
                  p.web_name,
                  pd.bonus,
                  pd.minutes,
                  p.element_type,
                  p.form
                FROM players_detail pd
                join players p on pd.element = p.id
                WHERE round = '.CURRENT_GW.'
                order by pd.element asc;'); # and pd.id < 30000
    return $rows;
}

function db_get_current_fixtures() {
    $db = New db;
    $rows = $db -> select('SELECT finished, finished_provisional, started FROM fpl.fixtures order by id asc;');
    return $rows;
}

function players_table($positions) {
    $CURRENT_GW = CURRENT_GW + 1;
    $positions_str = '';
    foreach ($positions as $position) {
        $positions_str .= $position . ',';
    }
    $positions_str = rtrim($positions_str, ',');

    $set_min = ($_POST['set_min'] == 'true') ? ' and minutes > 850 ' : '';
    $set_form = ($_POST['form'] == 'true') ? ' and form >= 2 ' : '';
    $set_form = '';
    $set_min = '';
    $query = 'select p.web_name, t.name, cast((now_cost / 10) as decimal(3,1)) now_cost, points_per_game,
      cast(((points_per_game - 2) / (now_cost / 10)) as decimal(5,3)) vapm,
      cast((points_per_game / (now_cost / 10)) as decimal(5,2)) ppgm,
      cast((total_points / (now_cost / 10)) as decimal(5,2)) ppm,
     goals_scored, assists, clean_sheets, bps, element_type, total_points, minutes
     ,ft.gw'.$CURRENT_GW.',ft.gw'.($CURRENT_GW+1).'
                from players p 
                join teams t on p.team = t.id
                join fixture_table ft on ft.`name` = t.name
                where element_type in ('. $positions_str .')'.$set_min.$set_form.';';

    $db = New db;
    $rows = $db -> select($query);

    if ($rows) {
        $positions = [1 => 'GK', 2 => 'DEF', 3 => 'MID', 4 => 'FWD'];
        $strength = get_strength();

        $body = '<thead>
                <tr>
                    <th>Player</th>
                  
                    <th title="Player Price">Cost</th>
                    <th title="Total FPL Points">Pts</th>
                    <th title="Points Per Game">PPG</th>
                    <th title="Points Per Game Per Million">PPGM</th>
                    <th title="Points Per Million">PPM</th>
                    <th title="(Points - 2) / Cost">VAPM</th>
                    <th title="Goals">G</th>
                    <th title="Assists">A</th>
                    <th title="Clean Sheets">CS</th>
                    <th title="Total Bonus Point System">BPS</th>
                    <th title="Minutes Played">Min</th>
                
                    <th>Team</th>
                    <th>GW ' . $CURRENT_GW . '</th>
                    <th>GW ' . ($CURRENT_GW + 1) . '</th>
                </tr>
            </thead>
            <tbody>';

        foreach ($rows as $row) {
            $body .= '<tr position="'.$row['element_type'].'">';
            $body .= '<td>' . $row['web_name'] . '</td>';

            $body .= '<td><p style=" min-width: 50px; height: 0px;">£ ' . $row['now_cost'] . '</p></td>';
            $body .= '<td>' . $row['total_points'] . '</td>';
            $body .= '<td>' . $row['points_per_game'] . '</td>';
            $body .= '<td>' . $row['ppgm'] . '</td>';
            $body .= '<td>' . $row['ppm'] . '</td>';
            $body .= '<td>' . $row['vapm'] . '</td>';
            $body .= '<td>' . $row['goals_scored'] . '</td>';
            $body .= '<td>' . $row['assists'] . '</td>';
            $body .= '<td>' . $row['clean_sheets'] . '</td>';
            $body .= '<td>' . $row['bps'] . '</td>';
            $body .= '<td>' . $row['minutes'] . '</td>';
            // $body .= '<td>' . $positions[$row['element_type']] . '</td>';
//            $body .= '<td>' . $row['name'] . '</td>';
            $body .= '<td></td>';
            if (substr($row['gw' . $CURRENT_GW], -2, 1) == 'H') {
                $at = 'home';
            } else {
                $at = 'away';
            }
            $team = substr($row['gw' . $CURRENT_GW], 0, -4);
            $body .= '<td><p style=" min-width: 130px; height: 0px;">' . format($row['gw' . $CURRENT_GW], $strength[$team][$at], $at) . '</p></td>';
            if (substr($row['gw' . ($CURRENT_GW + 1)], -2, 1) == 'H') {
                $at = 'home';
            } else {
                $at = 'away';
            }
            $team = substr($row['gw' . ($CURRENT_GW + 1)], 0, -4);
            $body .= '<td><p style=" min-width: 130px; height: 0px;">' . format($row['gw' . ($CURRENT_GW + 1)], $strength[$team][$at], $at) . '</p></td>';
            $body .= '</tr>';
        }
        $body .= '</tbody>';

        return $body;
    } else {
        return null;
    }
}

function format($value, $strength, $at = 'home') {
    if ($strength > 1150) {
        if ($at == 'home') {
            $color = 'red';
        } else {
            $color = 'darkred';
        }
    } elseif ($strength <= 1150 and $strength >= 1090) {
        $color = 'orange';
    } else {
        if ($at == 'away') {
            $color = 'green';
        } else {
            $color = 'lightgreen';
        }
    }

    return sprintf('<span style="color: %s;">%s</span>', $color, $value);
}

function getsql() {
    return
    "select * from fixture_table";
}

function get_strength () {
    $sql = "select name, strength_overall_home, strength_overall_away from teams";
    $db = New db;
    $arr = array();
    $inner_arr = array();
    $str = $db -> select($sql);
    for ($i = 0; $i <= 19; $i++) {
        $inner_arr['home'] = $str[$i]['strength_overall_home'];
        $inner_arr['away'] = $str[$i]['strength_overall_away'];
        $arr[$str[$i]['name']] = $inner_arr;
    }

    return $arr;

}

function get_teams_select ($team_id) {
    $sql = "select name, id from teams";
    $db = New db;
    $res = $db -> select($sql);
    $options = '';
    foreach($res as $team) {
        $selected = ($team_id == $team['id']) ? 'selected' : '';
        $options .= '<option value="'.$team['id'].'" '.$selected.' >'.$team['name'].'</option>';
    }
    return $options;
}

function justoffside($league_id = null)
{
    if ($league_id == 433) {
        $page_end = 11;
    } else {
        $page_end = 38;
    }
    $i = 0;
    $results = array();
    $phase = $_POST['phase'];
    if ($phase > 1) {
        $total_to_get = 'total';
        $header_total = 'Month Total';
    } else {
        $total_to_get = 'event_total';
        $header_total = 'Week Total';
    }

    for ($page = 1; $page <= $page_end; $page++) {
        $json_response = file_get_contents(URL_STANDINGS . $league_id . "?phase=".$phase."&le-page=1&ls-page=" . $page);
        $array = json_decode($json_response, true);
        $json_standings = $array['standings']['results'];
        if (count($json_standings) > 0) {
            foreach ($json_standings as $entry) {
                $results[$i]['entry'] = $entry['entry'];
                $results[$i]['entry_name'] = $entry['entry_name'];
                $results[$i]['event_total'] = $entry[$total_to_get];
                $results[$i]['player_name'] = $entry['player_name'];
                $i++;
            }
        } else {
            break;
        }
    }
    $results = array_order($results, 'event_total', SORT_DESC);
    $place = 0;
    $count_tied = 0;
    $prev_player_total = 999999999;

    $jo_table = '<table id="jo_table" class="table table-striped table-condensed table-sm"><thead><tr>';
    $table = '<table id="total_league_table" class="table table-striped table-condensed table-sm"><thead><tr>';
    $header_table = th('Rank');
    $header_table .= th('Team Name');
    $header_table .= th('Player Name');
    $header_table .= th($header_total);
    $header_table .= '</tr></thead><tbody>';
    $jo_table .= $header_table;
    $table .= $header_table;
    foreach ($results as $entry) {
        $curr_total = $entry['event_total'];
        if ($curr_total < $prev_player_total) {
            $place++;
            $place = $place + $count_tied;
            $count_tied = 0;
        } else {
            $count_tied++;
        }

        $i_table = '<tr>';
        $i_table .= td($place, 10);
        $i_table .= td('<a href="https://fantasy.premierleague.com/a/team/' . $entry['entry'] . '/event/' . CURRENT_GW . '" >' . $entry['entry_name'] . '</a>', 40);
        $i_table .= td($entry['player_name'], 40);
        $i_table .= td($curr_total, 10);
        $i_table .= '</tr>';

        $table .= $i_table;
        if ($league_id == 433) {
            if (substr($entry['entry_name'], -15) == '@JustOffsidePod') {
                $jo_table .= $i_table;
            }
        } else {
            if (substr($entry['entry_name'], -9) == '@ FML FPL') {
                $jo_table .= $i_table;
            }
        }

        $prev_player_total = $curr_total;
    }
    $table .= '</tbody></table>';
    $jo_table .= '</tbody></table>';

    $res = array(
        'JO' => $jo_table
        ,'LEAGUE' => $table
    );
    return $res;
}