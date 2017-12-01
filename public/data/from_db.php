<?php
// error_reporting(0);
require_once(__DIR__ . '/../db/db.php');
require_once(__DIR__ . '/../classes/entry.php');
require_once(__DIR__ . '/../common.php');
require_once(__DIR__ . '/../weath/weather.php');

$url_fpl = "https://fantasy.premierleague.com/drf/";
$url_standings = "leagues-classic-standings/";
$url_standings_h2h = "leagues-h2h-standings/";
$url_teams = $url_fpl . 'teams/';
$url_fixtures = $url_fpl . 'fixtures/';
$url_players = $url_fpl . 'bootstrap-static';
$success = 0;
$arr = array();
$mode = $_POST['mode'] ?: '';

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
        $league = $_POST['league'] ?: 313; // 3281;
        if ($league == '00000') {
            $body = 'Enter your League ID above to get selections!';
        } else {
            $body = get_league_picks($league);
        }
        $success = 1;
        $arr = array(
            'SUCCESS' => $success
            ,'BODY' => $body
        );
        break;
    case 'test':
        $league = $_POST['league'] ?: 3281; // 3281;
        if ($league == '00000') {
            $body = 'Enter your League ID above to get your live table!';
        } else {
            $body = get_league_picks_($league);
        }
        $success = 1;
        $arr = array(
            'SUCCESS' => $success
            ,'BODY' => $body
        );
        break;
    case 'team':
        $team = $_POST['team'] ?: 81182;
        $body = get_team($team);
        $success = 1;
        $arr = array(
            'SUCCESS' => $success
            ,'BODY' => $body
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
    $p = db_get_players();

    $current = array();
    foreach($t as $player) {
        $current[] = $p[$player['element'] - 1];
    }

    $t = array_order($current, 'element_type', SORT_ASC);

    $teams = array();
    for ($i = CURRENT_GW + 1; $i <= CURRENT_GW + 1 + 3; $i++) {
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

function array_order()
{
    $args = func_get_args();
    $data = array_shift($args);
    foreach ($args as $n => $field) {
        if (is_string($field)) {
            $tmp = array();
            foreach ($data as $key => $row)
                $tmp[$key] = $row[$field];
            $args[$n] = $tmp;
        }
    }
    $args[] = &$data;
    call_user_func_array('array_multisort', $args);
    return array_pop($args);
}

function get_league_picks($league_id) {
    global $url_fpl, $url_standings, $url_standings_h2h;
    $page = 1;
    $json_response = file_get_contents($url_fpl . $url_standings . $league_id . "?phase=1&le-page=1&ls-page=" . $page);
    $array = json_decode($json_response, true);
    $json_standings = $array['standings']['results'];

    if (count($json_standings) > 0) {
        return get_league_picks_api($json_standings);
    } else {
        $json_response = file_get_contents($url_fpl . $url_standings_h2h . $league_id);
        $array = json_decode($json_response, true);
        $json_standings = $array['standings']['results'];
        if (count($json_standings) > 0) {
            return get_league_picks_api($json_standings);
        } else {
            return 'Error retrieving league ID!';
        }
    }
}

function get_league_picks_($league_id) {
    global $url_fpl, $url_standings, $url_standings_h2h;
    $page = 1;
    $json_response = file_get_contents($url_fpl . $url_standings . $league_id . "?phase=1&le-page=1&ls-page=" . $page);
    $array = json_decode($json_response, true);
    $json_standings = $array['standings']['results'];

    if (count($json_standings) > 0) {
        return get_league_picks_api_($json_standings);
    } else {
        $json_response = file_get_contents($url_fpl . $url_standings_h2h . $league_id);
        $array = json_decode($json_response, true);
        $json_standings = $array['standings']['results'];
        if (count($json_standings) > 0) {
            return get_league_picks_api_($json_standings);
        } else {
            return 'Error retrieving league ID!';
        }
    }
}

function get_league_picks_api_($json_standings) {
    $i = 0;
    $db_players = db_get_players_detail();
    $fixtures = db_get_current_fixtures();
    foreach ($json_standings as $entry) {
        $user_points = 0;
        $user_bench_points = 0;
        $p_played = 0;
        $p_yet_to_play = 0;
        $p_playing = 0;
        $captain = 0;
        $a_played = array();
        $a_playing = array();
        $a_to_play = array();
        $a_bench = array();
        $new_total = $json_standings[$i]['total'];

        $entry_obj = new entry();
        if ($entry_obj->get($entry['entry'])) {
            $picks = $entry_obj->picks;

            foreach ($picks as $pick) {
                $player_obj = $db_players[$pick['element'] - 1];
                $points = $player_obj['total_points'] * $pick['multiplier'];
                if ($pick['multiplier'] > 1) {
                    $captain = $pick['element'];
                }
                if ($pick['position'] <= 11) {
                    $fixture_id = $player_obj['fixture'];
                    $fixture_obj = $fixtures[$fixture_id - 1];
                    $user_points += $points;
                    if (!$fixture_obj['finished']) {
                        $new_total += $points;
                    }
                    if ($fixture_obj['finished_provisional']) {
                        $p_played++;
                        if ($player_obj['id'] == $captain) {
                            $a_played[] = '<b>'.$player_obj['web_name'] . ' (' . $points . ')</b>';
                        } else {
                            $a_played[] = $player_obj['web_name'] . ' (' . $points . ')';
                        }
                    } else if ($fixture_obj['started']) {
                        $p_playing++;
                        if ($player_obj['id'] == $captain) {
                            $a_playing[] = '<b>'.$player_obj['web_name'] . ' (' . $points . ')</b>';
                        } else {
                            $a_playing[] = $player_obj['web_name'] . ' (' . $points . ')';
                        }
                    } else {
                        $p_yet_to_play++;
                        if ($player_obj['id'] == $captain) {
                            $a_to_play[] = '<b>'.$player_obj['web_name'] . '</b>';
                        } else {
                            $a_to_play[] = $player_obj['web_name'];
                        }
                    }
                } else {
                    $user_bench_points += $points;
                    $a_bench[] = $player_obj['web_name'] . ' (' . $points . ')';
                }
            }
            $json_standings[$i]['user_points'] = $user_points;
            $json_standings[$i]['user_bench_points'] = $user_bench_points;
            $json_standings[$i]['yet_to_play'] = $p_yet_to_play;
            $json_standings[$i]['playing'] = $p_playing;
            $json_standings[$i]['played'] = $p_played;
            $json_standings[$i]['new_total'] = $new_total;
            $json_standings[$i]['a_played'] = $a_played;
            $json_standings[$i]['a_playing'] = $a_playing;
            $json_standings[$i]['a_to_play'] = $a_to_play;
            $json_standings[$i]['a_bench'] = $a_bench;
            $json_standings[$i]['transfer_cost'] = $entry_obj->transfer_cost;
            $json_standings[$i]['team_value'] = $entry_obj->team_value;
            $json_standings[$i]['calc_cost'] = $entry_obj->calc_cost;

            $i++;
        };
    }
    $json_standings = array_order($json_standings, 'new_total', SORT_DESC);
    $i = 1;
    $table = '<table id="live_table" class="table table-striped table-condensed table-sm"><thead><tr>';
    $table .= '<th></th><th colspan="3" style=""># Players</th><th colspan="3"># Points</th><th colspan="2">Totals</th></tr><tr>';
    $table .= th('Player');
    $table .= th('Played', 'Players that have completed their match for this gameweek.');
    $table .= th('Playing', 'Players currently in a match.');
    $table .= th('To Play', 'Players that have yet to play this gameweek.');
    $table .= th('Pts', 'Player points for this gameweek.');
    $table .= th('Bench', 'Total bench points for this gameweek.');
    $table .= th('Cost', 'Cost of the transfers made this gameweek.');
    $table .= th('Live Total', 'Live Total Points (Live Rank)');
    $table .= th('Prev Total', 'Previous Week Total Points');
    $table .= th('Team Value', 'Team Value + Money in the Bank');

    $table .= '</tr></thead><tbody>';
    foreach ($json_standings as $users => $user) {
        $played_table = '<table><tbody>';
        foreach ($user['a_played'] as $p_played) {
            $played_table .= '<tr><td>'.$p_played.'</td></tr>';
        }
        $played_table .= '</tbody></table>';

        $playing_table = '<table><tbody>';
        foreach ($user['a_playing'] as $p_playing) {
            $playing_table .= '<tr><td>'.$p_playing.'</td></tr>';
        }
        $playing_table .= '</tbody></table>';

        $to_play_table = '<table><tbody>';
        foreach ($user['a_to_play'] as $p_to_play) {
            $to_play_table .= '<tr><td>'.$p_to_play.'</td></tr>';
        }
        $to_play_table .= '</tbody></table>';

        $bench_table = '<table><tbody>';
        foreach ($user['a_bench'] as $p_bench) {
            $bench_table .= '<tr><td>'.$p_bench.'</td></tr>';
        }
        $bench_table .= '</tbody></table>';

        $table .= '<tr>';
        $table .= td($user['entry_name']);
        $table .= '<td data-order="'.$user['played'].'"><a class="played" title="<b>Played</b>" data-html="true" data-toggle="popover" data-trigger="click" data-placement="left" data-container="body" data-content="'.$played_table.'" >'.$user['played'].'</a></td>';
        $table .= '<td data-order="'.$user['playing'].'"><a class="playing" title="<b>Playing</b>" data-html="true" data-toggle="popover" data-trigger="click" data-placement="left" data-container="body" data-content="'.$playing_table.'" >'.$user['playing'].'</a></td>';
        $table .= '<td data-order="'.$user['yet_to_play'].'"><a class="yet-to-play" title="<b>Yet to Play</b>" data-html="true" data-toggle="popover" data-trigger="click" data-placement="left" data-container="body" data-content="'.$to_play_table.'" >'.$user['yet_to_play'].'</a></td>';
        $table .= td($user['user_points']);
        $table .= '<td data-order="'.$user['user_bench_points'].'"><a class="bench" title="<b>Bench Points</b>" data-html="true" data-toggle="popover" data-trigger="click" data-placement="left" data-container="body" data-content="'.$bench_table.'" >'.$user['user_bench_points'].'</a></td>';
        $table .= td(($user['transfer_cost'] == 0) ? $user['transfer_cost'] : '-'.$user['transfer_cost']);
        if ($user['calc_cost']) {
            $table .= td(($user['new_total'] - $user['transfer_cost']) . ' ('.$i.')');
            $table .= td($user['new_total'] - $user['user_points']);
        } else {
            $table .= td($user['new_total'] . ' ('.$i.')');
            $table .= td($user['new_total'] - $user['user_points'] + $user['transfer_cost']);
        }
        $table .= td($user['team_value']);
        $table .= '</tr>';
        $i++;
    }
    $table .= '</tbody></table>';
    return $table;
}

function th($name, $title = null) {
    $title = $title ?: $name;
    $h = '<th class="text-nowrap" title="'.$title.'">'.$name.'</th>';
    return $h;
}

function td($name) {
    $d = '<td class="text-nowrap">'.$name.'</td>';
    return $d;
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
            // $pick_tracker[$player][$entry_id][] = $pick['is_captain'];
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
            $inner_value = $league_entry_ids[$entry['entry']]['entry_name'].' ('.$league_entry_ids[$entry['entry']]['rank'];
            if ($entry['multiplier'] > 1) {
                $inner_value = '<b>'.$inner_value.'</b>';
            } elseif ($entry['position'] > 11) {
                $inner_value = '<strike>'.$inner_value.'</strike>';
            }
            $inner_table .= '<tr><td>'.$inner_value.')</td></tr>';
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
    for ($i = $CURRENT_GW; $i <= ($CURRENT_GW + $shown_gameweeks - 1); $i++) {
        $body .= '<th>GW ' . $i . '</th>';
    }
    $body .= '</tr></thead><tbody>';

    foreach ($rows as $row) {
        $overall = 0;
        $bodyTemp = '';
        $body .= '<tr>';
        $body .= '<td>' . $row['name'] . '</td>';
        for ($i = $CURRENT_GW; $i <= ($CURRENT_GW + $shown_gameweeks - 1); $i++) {
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
     ,ft.gw'.(CURRENT_GW + 1).',ft.gw'.(CURRENT_GW + 2).',ft.gw'.(CURRENT_GW + 3).',ft.gw'.(CURRENT_GW + 4).'
                from players p 
                join teams t on p.team = t.id
                join fixture_table ft on ft.`name` = t.name;');
    return $rows;
}

function db_get_players_detail() {
    $db = New db;
    $rows = $db -> select(
        'SELECT
                  pd.element as id,
                  pd.bps,
                  pd.total_points,
                  pd.fixture,
                  p.web_name
                FROM players_detail pd
                join players p on pd.element = p.id
                WHERE round = '.CURRENT_GW.';');
    return $rows;
}

function db_get_current_fixtures() {
    $db = New db;
    $rows = $db -> select(
        'SELECT * FROM fixtures;');
                // WHERE event = '.CURRENT_GW.';');
    return $rows;
}
function players_table($positions) {
    $CURRENT_GW = CURRENT_GW + 1;
    $positions_str = '';
    foreach ($positions as $position) {
        $positions_str .= $position . ',';
    }
    $positions_str = rtrim($positions_str, ',');

    $query = 'select p.web_name, t.name, cast((now_cost / 10) as decimal(3,1)) now_cost, points_per_game,
      cast(((points_per_game - 2) / (now_cost / 10)) as decimal(5,3)) vapm,
     goals_scored, assists, clean_sheets, bps, element_type, total_points, minutes
     ,ft.gw'.$CURRENT_GW.',ft.gw'.($CURRENT_GW+1).'
                from players p 
                join teams t on p.team = t.id
                join fixture_table ft on ft.`name` = t.name
                where element_type in ('. $positions_str .')';
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
            $body .= '<td>' . $row['vapm'] . '</td>';
            $body .= '<td>' . $row['goals_scored'] . '</td>';
            $body .= '<td>' . $row['assists'] . '</td>';
            $body .= '<td>' . $row['clean_sheets'] . '</td>';
            $body .= '<td>' . $row['bps'] . '</td>';
            $body .= '<td>' . $row['minutes'] . '</td>';
            // $body .= '<td>' . $positions[$row['element_type']] . '</td>';
            $body .= '<td>' . $row['name'] . '</td>';
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
        return $positions_str;
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