<?php

require_once(__DIR__ . '/../db/db.php');
require_once(__DIR__ . '/../common.php');
// require_once(__DIR__ . '/../data/from_db.php');
$url_fpl = "https://fantasy.premierleague.com/drf/";
$url_standings = "leagues-classic-standings/";
$url_teams = $url_fpl . 'teams/';
$url_fixtures = $url_fpl . 'fixtures/';
$url_players = $url_fpl . 'bootstrap-static';
$url_players_detail = $url_fpl . 'element-summary/';

$last_played_gameweek = 12;
$league_id = 3281;
// $league_id = 2637;
// $league_id = 6211;
// get_league_standings($league_id, $last_played_gameweek);
//$rows = get_winners($league_id, 1);
// echo '<table>'.$rows.'</table>';

//get_players($url_players);

// get_teams($url_teams);
// create($url_fixtures);

// does bonus updates
get_fixtures_($url_fixtures);


//get_players_($url_players);
// get_players_detail($url_players_detail);



function get_league_standings($league_id, $last_played_gameweek) {

    $db = New db();
    $query = $db -> query('delete from standings where league_id = ' . $league_id . ';');
    $last_page = 1;

    for ($page = 1; $page <= $last_page; $page++) {
        $full_standings = get_fpl_response('https://fantasy.premierleague.com/drf/' . 'leagues-classic-standings/' . $league_id . '?phase=1&le-page=1&ls-page=' . $page);

        if ($full_standings) {
            $standings = $full_standings['standings']['results'];

            $sql = '';
            foreach ($standings as $entry) {
                $sql .= 'insert into standings (league_id, player_id, entry_name, total, rank, last_rank, player_name 
                    ) values ('
                    . $league_id
                    . ',' . $entry['entry']
                    . ',"' . $entry['entry_name']
                    . '",' . $entry['total']
                    . ',' . $entry['rank']
                    . ',' . $entry['last_rank']
                    . ',"' . $entry['player_name']
                    . '"); ';

            }
            $db -> query($sql);

            $rows = $db -> select('select player_id from standings where league_id = ' . $league_id . ';');
            foreach ($rows as $row) {
                for ($gameweek = 1; $gameweek <= $last_played_gameweek; ++$gameweek) {
                    $url_entry = 'https://fantasy.premierleague.com/drf/entry/'. $row['player_id'] .'/event/'.$gameweek.'/picks';
                    get_entries($url_entry, $row['player_id'], $gameweek);
                }
            }
        } else {
            $page = $last_page;
        }
    }
}

function get_teams($url) {
    // returns all premier league teams to the teams table
    $teams = get_fpl_response($url);

    $db = New db();
    $db -> query('truncate table teams;');

    foreach ($teams as $team) {
        try {
            $sql = 'insert into teams (id,
            name,
            code,
            short_name,
            unavailable,
            strength,
            position,
            played,
            win,
            loss,
            draw,
            points,
            
            link_url,
            strength_overall_home,
            strength_overall_away,
            strength_attack_home,
            strength_attack_away,
            strength_defence_home,
            strength_defence_away,
            team_division) values (' .
                '"' . $team['id'] . '",' .
                '"' . $team['name'] . '",' .
                '"' . $team['code'] . '",' .
                '"' . $team['short_name'] . '",' .
                '"' . $team['unavailable'] . '",' .
                '"' . $team['strength'] . '",' .
                '"' . $team['position'] . '",' .
                '"' . $team['played'] . '",' .
                '"' . $team['win'] . '",' .
                '"' . $team['loss'] . '",' .
                '"' . $team['draw'] . '",' .
                '"' . $team['points'] . '",' .
              //  '"' . $team['form'] . '",' .
                '"' . $team['link_url'] . '",' .
                '"' . $team['strength_overall_home'] . '",' .
                '"' . $team['strength_overall_away'] . '",' .
                '"' . $team['strength_attack_home'] . '",' .
                '"' . $team['strength_attack_away'] . '",' .
                '"' . $team['strength_defence_home'] . '",' .
                '"' . $team['strength_defence_away'] . '",' .
                '"' . $team['team_division'] . '")';

            echo $sql . '<br>';
            $res = $db->query($sql);
            if (!$res) {
                echo 'exception: ' . $db->error() , '<br>';
            }
        } catch (Exception $e) {
            echo 'exception: ' . $e;
        }
    }
    if (true) {
        echo 'teams refreshed.';
        echo $res;
    } else {
        echo $res;
    }

}

// old - dnu
function get_fixtures($url) {
    $fixtures = get_fpl_response($url);

    $db = New db();
    $sql = 'truncate table fixtures;';
    $query = $db -> query($sql);

    foreach ($fixtures as $fixture) {
        if (true) {
            $sql = 'insert into fixtures (id, h_id, h_score, a_id, a_score, gameweek) values ('
                    . $fixture['id'] 
                    . ',' . $fixture['team_h']
                    . ',' . ($fixture['team_h_score'] ?: 0)
                    . ',' . $fixture['team_a']
                    . ',' . ($fixture['team_a_score'] ?: 0)
                    . ',' . $fixture['event']
                    . ');';
                    echo $sql;
            $query = $db -> query($sql);
        }
    }    
    echo 'results refreshed.';
}

// get / update fixtures and current bonus
function get_fixtures_($url) {
    $fixtures = get_fpl_response($url);
    $db = New db();
    $i = 0;

    $sql = 'update players_detail set bonus = 0 where round = '.CURRENT_GW.' and bonus != 0;';
    $db->query($sql);

    foreach ($fixtures as $fixture) {
        if ($fixture['event'] >= CURRENT_GW) {
            $insert = 'insert into fpl.fixtures (';
            foreach ($fixture as $key => $value) {
                if ($key != 'stats') {
                    $insert .= $key . ',';
                }
            }
            $insert = rtrim($insert, ',');
            $insert .= ') values (';
            $update = 'ON DUPLICATE KEY UPDATE ';
            foreach ($fixture as $key => $value) {
                if ($key != 'stats') {
                    $value = ($value == '') ? 0 : $value;
                    $insert .= "'" . $value . "',";
                    $update .= $key . '=' . "'" . $value . "',";
                }
            }
            $insert = rtrim($insert, ',');
            $update = rtrim($update, ',');
            $insert .= ') ' . $update . '; ';

            $r = $db->query($insert);
            if ($r != 1) {
                error_log($r);
                error_log($insert);
                echo('err: '. $r);
            } else {
//                echo($insert.'<br>');
                $i++;
            };

            // update bonus
            if (count($fixture['stats']) > 0 ) {
                $away = $fixture['stats'][9]['bps']['a'];
                $home = $fixture['stats'][9]['bps']['h'];

                $bonus = array();

                for ($j = 0; $j < 4; $j++) {
                    $bonus[] = $away[$j];
                    $bonus[] = $home[$j];
                }
                $bonus = array_order($bonus, 'value', SORT_DESC);

                if ($bonus[0]['value'] > $bonus[1]['value']) {
                    bonus($db, $bonus[0]['element'], 3, $fixture['id']);
                    if ($bonus[1]['value'] > $bonus[2]['value']) {
                        bonus($db, $bonus[1]['element'], 2, $fixture['id']);
                        bonus($db, $bonus[2]['element'], 1, $fixture['id']);
                    } else {
                        bonus($db, $bonus[1]['element'], 2, $fixture['id']);
                        bonus($db, $bonus[2]['element'], 2, $fixture['id']);
                    }
                } else {
                    bonus($db, $bonus[0]['element'], 3, $fixture['id']);
                    bonus($db, $bonus[1]['element'], 3, $fixture['id']);
                    if ($bonus[2]['value'] > $bonus[3]['value']) {
                        bonus($db, $bonus[2]['element'], 1, $fixture['id']);
                    } else {
                        bonus($db, $bonus[2]['element'], 1, $fixture['id']);
                        bonus($db, $bonus[3]['element'], 1, $fixture['id']);
                    }
                }
            }
        }
    }
    echo $i . ' fixtures refreshed.';
}

function bonus($db, $element, $bonus, $fixture) {
    $sql = 'update fpl.players_detail set bonus = '.$bonus.' where round = '.CURRENT_GW.' and element = '.$element.' and fixture = '.$fixture.';';
    $db->query($sql);
}
//
//function array_order()
//{
//    $args = func_get_args();
//    $data = array_shift($args);
//    foreach ($args as $n => $field) {
//        if (is_string($field)) {
//            $tmp = array();
//            foreach ($data as $key => $row)
//                $tmp[$key] = $row[$field];
//            $args[$n] = $tmp;
//        }
//    }
//    $args[] = &$data;
//    call_user_func_array('array_multisort', $args);
//    return array_pop($args);
//}

function get_players($url) {
    $full_players = get_fpl_response($url);
    $players = $full_players['elements'];

    $db = New db();
    $query = $db -> query('truncate table players;');

    foreach ($players as $player) {
        $query = $db -> query(
            'insert into fpl.players (id, web_name, team, goals_scored, assists, clean_sheets, goals_conceded, bps, now_cost, element_type, total_points,
              code, minutes, points_per_game, event_points, selected_by_percent) values ('
            . $player['id']
            . ',"' . $player['web_name']
            . '",' . $player['team']
            . ',' . $player['goals_scored']
            . ',' . $player['assists']
            . ',' . $player['clean_sheets']
            . ',' . $player['goals_conceded']
            . ',' . $player['bps']
            . ',' . $player['now_cost']
            . ',' . $player['element_type']
            . ',' . $player['total_points']
            . ',' . $player['code']
            . ',' . $player['minutes']
            . ',' . $player['points_per_game']
            . ',' . $player['event_points']
            . ',' . $player['selected_by_percent']
            . ');');
    }
    // $db->close();
    echo 'players refreshed.';
}

function get_players_($url) {
    $full_players = get_fpl_response($url);
    $players = $full_players['elements'];

    $db = New db();

    foreach ($players as $player) {
        $insert = 'insert into fpl.players (';
        foreach ($player as $key => $value) {
            $insert .= $key.',';
        }
        $insert = rtrim($insert,',');
        $insert .= ') values (';
        $update = 'ON DUPLICATE KEY UPDATE ';
        foreach ($player as $key => $value) {
            $value = ($value == '') ? 0 : $value;
            $value = str_replace("'", "''", $value);
            $insert .= "'" . $value. "',";
            $update .= $key.'='."'" . $value. "',";
        }
        $insert = rtrim($insert,',');
        $update = rtrim($update,',');
        $insert .= ') '.$update.'; ';

        $r = $db->query($insert);
        if ($r != 1) {
            error_log($r);
            error_log($insert);
        };
    }
    // $db->close();
    error_log('players refreshed.');
}

function get_players_detail($url) {
    $db = New db();
    // $db -> query('truncate table players_detail;');

    for ($i = 1; $i <= 560; $i++) { // 560
        $full_players = get_fpl_response($url.$i);
        $matches = $full_players['history'];
        foreach ($matches as $match) {
            $insert = 'insert into players_detail (';
            foreach ($match as $key => $value) {
                $insert .= $key.',';
            }
            $insert = rtrim($insert,',');
            $insert .= ') values (';
            foreach ($match as $key => $value) {
                $value = ($value == '') ? 0 : $value;
                $insert .= "'" . $value. "',";
            }
            $insert = rtrim($insert,',');
            $insert .= '); ';
            $db->query($insert);
        }
    }
    $db->close();
}

function get_entries($url, $player_id, $gameweek) {
    $full_entries = get_fpl_response($url);
    $entry = $full_entries['entry_history'];
    $db = New db();
   // $query = $db -> query('truncate table entries;');

    $sql = 
        'insert into entries (id, player_id, gameweek, points, value, bank, total_points) values ('
        . $entry['id']
        . ',' . $player_id
        . ',' . $gameweek
        . ',' . $entry['points']
        . ',' . $entry['value']
        . ',' . $entry['bank'] 
        . ',' . $entry['total_points'] 
        . ');';
    $db -> query($sql);
    echo $sql . '<br>';
}



function my_team ($player_id) {
    $table = '';
    return $table;
}

function get_whoscored () {
    $json_response = 'https://www.whoscored.com/StatisticsFeed/1/GetPlayerStatistics?category=summary&subcategory=offensive&statsAccumulationType=0&isCurrent=true&playerId=&teamIds=&matchId=&stageId=15151&tournamentOptions=2&sortBy=Rating&sortAscending=&age=&ageComparisonType=&appearances=&appearancesComparisonType=&field=Overall&nationality=&positionOptions=&timeOfTheGameEnd=&timeOfTheGameStart=&isMinApp=true&page=&includeZeroValues=&numberOfPlayersToPick=10';
    $array = json_decode($json_response, true);
    $players = $array['playerTableStats'];
}

function create($url) {
    $fixtures = get_fpl_response($url);
    $fixture = $fixtures[0];
    $insert = 'create table fixtures (';
    foreach ($fixture as $key => $value) {
        if ($key != 'stats') {
            $insert .= $key . ' int(11),<br>';
        }
    }
    $insert = rtrim($insert,',');
    $insert .= ') ; ';

    echo $insert;
}

?>