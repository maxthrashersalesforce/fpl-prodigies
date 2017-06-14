<?php

require_once($_SERVER['DOCUMENT_ROOT'].'/db/db.php');

$url_fpl = "https://fantasy.premierleague.com/drf/";
$url_standings = "leagues-classic-standings/";
$url_teams = $url_fpl . 'teams/';
$url_fixtures = $url_fpl . 'fixtures/';
$url_players = $url_fpl . 'bootstrap-static';

$last_played_gameweek = 38;
$league_id = 270578;
// $league_id = 2637;
// $league_id = 6211;

get_teams($url_teams);
get_fixtures($url_fixtures);
get_players($url_players);
// get_league_standings($league_id, $last_played_gameweek);


// $rows = get_winners($league_id, 1);
// echo '<table>'.$rows.'</table>';


function get_fpl_response($url) {
    $response = file_get_contents($url);
    $json = json_decode($response, true);

    if (!$json) {
        return $json;
    } else {
        return $json;
    }
}

function get_league_standings($league_id, $last_played_gameweek) {

    $db = New db();
    $query = $db -> query('delete from standings where league_id = ' . $league_id . ';');
    $last_page = 1;

    for ($page = 1; $page <= $last_page; $page++) {
        $full_standings = get_fpl_response('https://fantasy.premierleague.com/drf/' . 'leagues-classic-standings/' . $league_id . '?phase=1&le-page=1&ls-page=' . $page);

        if ($full_standings) {
            $standings = $full_standings['standings']['results'];

            foreach ($standings as $entry) {
                $sql = 'insert into standings (league_id, player_id, entry_name, total, rank, last_rank, player_name 
                    ) values ('
                    . $league_id
                    . ',' . $entry['entry']
                    . ',"' . $entry['entry_name']
                    . '",' . $entry['total']
                    . ',' . $entry['rank']
                    . ',' . $entry['last_rank']
                    . ',"' . $entry['player_name']
                    . '");';
                $query = $db -> query($sql);
            }
            
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
    $query = $db -> query('truncate table teams;');

    foreach ($teams as $team) {
        $query = $db -> query('insert into teams (id, name) values (' . $team['id'] . ',"' . $team['name'] . '" )');
    }
    echo 'teams refreshed.';
}

function get_fixtures($url) {
    $fixtures = get_fpl_response($url);

    $db = New db();
    $sql = 'truncate table fixtures;';
    $query = $db -> query($sql);

    foreach ($fixtures as $fixture) {
        if ($fixture['team_h_score']) {
            $sql = 'insert into fixtures (id, h_id, h_score, a_id, a_score, gameweek) values ('
                    . $fixture['id'] 
                    . ',' . $fixture['team_h'] 
                    . ',' . $fixture['team_h_score'] 
                    . ',' . $fixture['team_a'] 
                    . ',' . $fixture['team_a_score'] 
                    . ',' . $fixture['event']
                    . ');';
                    echo $sql;
            $query = $db -> query($sql);
        }
    }    
    echo 'results refreshed.';
}

function get_players($url) {
    $full_players = get_fpl_response($url);
    $players = $full_players['elements'];

    $db = New db();
    $query = $db -> query('truncate table players;');

    foreach ($players as $player) {
        $query = $db -> query(
            'insert into players (id, name, team, goals_scored, assists, clean_sheets, goals_conceded, bps) values ('
            . $player['id']
            . ',"' . $player['web_name']
            . '",' . $player['team']
            . ',' . $player['goals_scored']
            . ',' . $player['assists']
            . ',' . $player['clean_sheets']
            . ',' . $player['goals_conceded']
            . ',' . $player['bps']
            . ');');
    }
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
    $query = $db -> query($sql);
    echo $sql . '<br>';
}

function get_winners($league_id, $page) {
    global $url_fpl, $url_standings, $url_players, $last_played_gameweek;
    $body = '';

    $json_response = file_get_contents($url_fpl . $url_standings . $league_id . "?phase=1&le-page=1&ls-page=" . $page);
    $array = json_decode($json_response, true);
    $json_standings = $array['standings']['results'];

    if ($json_standings == false) {
        return null;
    } else {

        for ($gameweek = 1; $gameweek <= $last_played_gameweek; ++$gameweek) {
            $week_winner_name = '';
            $week_winner_pts = 0;
            $week_winner_total = 0;
            $num_of_winners = 1;

            foreach ($json_standings as $entry) {
                $resp = file_get_contents($url_fpl . "entry/" . $entry['entry'] . "/event/" . $gameweek . "/picks");
                $arr = json_decode($resp, true);
                $map_user_name[$entry['entry']] = $entry['entry_name'];

                $all_entries = $arr['entry_history'];
                $pts = $all_entries['points'];
                // echo('points: ' . $pts);
                $u_name = $map_user_name[$entry['entry']];
                // echo $pts . ' > ' . $week_winner_pts . '<br>';
                if (($pts > $week_winner_pts) && ($u_name != "Mid Table or Bust")) {
                    $week_winner_pts = $pts;
                    $week_winner_name = '<td>' . $u_name . '</td>';
                } 
                else if (($pts == $week_winner_pts) && ($u_name != "Mid Table or Bust")) {
                    $week_winner_pts = $pts;
                    $week_winner_name = '<td>' . $u_name . '</td>' . $week_winner_name;
                    $num_of_winners++;
                }
            }
            $body .= '<tr>';
            $body .= '<td>' . $gameweek . '</td>';
            $body .= '<td>' . $week_winner_pts . '</td>';
            $body .= $week_winner_name;
            // $body .= '<td>' . $week_winner_name . '</td>';
            $body .= '</tr>';
        }
    }
    return $body;
}

function my_team ($player_id) {
    $table = '';

    return $table;
}
?>