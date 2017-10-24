<?php
error_reporting(0);
require_once(__DIR__ . '/../db/db.php');
$url_fpl = "https://fantasy.premierleague.com/drf/";
$url_standings = "leagues-classic-standings/";
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
        $body = get_league_picks($league);
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
}

echo (sizeof($arr) > 0 ) ? json_encode($arr) : null;

function get_team($team_id) {
    $team_id=81182;
    global $url_fpl;
    $resp = file_get_contents($url_fpl . "my-team/" . $team_id . '/');
    $array = json_decode($resp, true);
    $current_team = $array['picks'];
    $table = '<table id="team" class="table table-striped table-condensed"><thead><tr>
                <th>Player</th><th>Team</th><th>GW Points</th><th>Picks</th>
                </tr></thead><tbody>';

    return $resp;
    foreach ($current_team as $player) {
        $table .= '<tr>';
        $table .= '<td>'.$player['element'].'</td>';
        $table .= '</tr>';
    }
    $table .= '</tbody></table>';
    return $table;
}

function get_league_picks($league_id) {
    global $url_fpl, $url_standings;
    $page = 1;
    $current_gameweek = 9;
    $json_response = file_get_contents($url_fpl . $url_standings . $league_id . "?phase=1&le-page=1&ls-page=" . $page);
    $array = json_decode($json_response, true);
    $json_standings = $array['standings']['results'];

    if (!$json_standings) {
        return 'error';
    } else {
        $pick_tracker = array();
        $league_entry_ids = array();
        foreach ($json_standings as $entry) {
            $entry_id = $entry['entry'];
            $league_entry_ids[$entry_id]['entry_name'] =  $entry['entry_name'];
            $league_entry_ids[$entry_id]['id'] =  $entry['id'];
            $league_entry_ids[$entry_id]['player_name'] =  $entry['player_name'];
            $league_entry_ids[$entry_id]['rank'] =  $entry['rank'];
            $resp = file_get_contents($url_fpl . "entry/" . $entry_id . "/event/" . $current_gameweek . "/picks");
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

        if ($league_id == 3281) {
        //    echo '<pre>'; print_r($pick_tracker); echo '</pre>';
        }

        $db_players = db_get_players();
        $table = '<table id="selections" class="table table-striped table-condensed"><thead><tr><th>Player</th><th>Team</th><th>GW Points</th><th>Picks</th></tr></thead><tbody>';
        foreach ($pick_tracker as $player => $picks) {
            $player_info = $db_players[($player -1)];

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
            $table .= '<td>'.$player_info['name'].'</td>';
            $table .= '<td>'.$player_info['event_points'].'</td>';
            $table .= '<td data-order="'.sizeof($picks).'"><a title="<b>'.$player_info['web_name'].'</b>" data-html="true" data-toggle="popover" data-trigger="click" data-placement="left" data-container="body" data-content="'.$inner_table.'" >'.sizeof($picks).'</a></td>';
            $table .= '</tr>';
        }
        $table .= '</tbody></table>';
    }
    return $table;
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
    $CURRENT_GW = 10;
    $db = New db;
    $rows = $db->select(getsql());

    $strength = get_strength();

    $body = '<table id="fixtures" class="table table-striped table-condensed"><thead><tr><th>Team</th><th>Overall</th>';
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

    $thead = '<thead>';
    $thead .=     '<th>Player</th>';
    $thead .=     '<th>Points</th>';
    $thead .=     '<th>Team Value</th>';
    $thead .=     '<th>Bank</th>';
    $thead .=     '<th></th>';
    $thead .= '</thead>';

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

function power_table($league_id, $current_gameweek) {
    $db = New db;
    $sql = 'select s.entry_name, sum(e.points) pts, s.total from standings s 
        join entries e on s.player_id = e.player_id
        where e.gameweek >= '. ($current_gameweek - 5).' and league_id = '. $league_id.'
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
     goals_scored, assists, clean_sheets, bps, element_type, total_points, minutes, event_points
                from players p 
                join teams t on p.team = t.id
                join fixture_table ft on ft.`name` = t.name;');
    return $rows;
}

function players_table($positions) {
    $CURRENT_GW = 10;
    $positions_str = '';
    foreach ($positions as $position) {
        $positions_str .= $position . ',';
    }
    $positions_str = rtrim($positions_str, ',');

    $db = New db;
    $rows = $db -> select(
            'select p.web_name, t.name, cast((now_cost / 10) as decimal(3,1)) now_cost, points_per_game,
      cast(((points_per_game - 2) / (now_cost / 10)) as decimal(5,3)) vapm,
     goals_scored, assists, clean_sheets, bps, element_type, total_points, minutes
     ,ft.gw'.$CURRENT_GW.',ft.gw'.($CURRENT_GW+1).'
                from players p 
                join teams t on p.team = t.id
                join fixture_table ft on ft.`name` = t.name
                where element_type in ('. $positions_str .')');

    if ($rows) {
        $positions = [1 => 'GK', 2 => 'DEF', 3 => 'MID', 4 => 'FWD'];
        $strength = get_strength();

        $body = '<thead>
                <tr>
                    <th>Player</th>
                    
                    <th>Cost</th>
                    <th>Points</th>
                    <th title="Points Per Game">PPG</th>
                    <th title="(Points - 2) / Cost">VAPM</th>
                    <th>Goals</th>
                    <th>Assists</th>
                    <th>CS</th>
                    <th>BPS</th>
                    <th>Mins</th>
                
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