<?php

require_once('db.php');

$url_fpl = "https://fantasy.premierleague.com/drf/";
$url_standings = "leagues-classic-standings/";
$url_teams = $url_fpl . 'teams/';
$url_fixtures = $url_fpl . 'fixtures/';
$url_players = $url_fpl . 'bootstrap-static';

$last_played_gameweek = 32;
get_teams($url_teams);
get_fixtures($url_fixtures);
get_players($url_players);

$league_id = 270578;
get_league_standings($league_id, $last_played_gameweek);

function get_league_standings($league_id, $last_played_gameweek) {
    $page = 1;
    $full_standings = get_fpl_response('https://fantasy.premierleague.com/drf/' . 'leagues-classic-standings/' . $league_id . '?phase=1&le-page=1&ls-page=' . $page);
    $standings = $full_standings['standings']['results'];

    $db = New db();
    $query = $db -> query('delete from standings where league_id = ' . $league_id . ';');
    foreach ($standings as $entry) {
        $query = $db -> query('insert into standings (league_id, player_id, entry_name, total, rank, last_rank) values ('
            . $league_id
            . ',' . $entry['entry']
            . ',"' . $entry['entry_name']
            . '",' . $entry['total']
            . ',' . $entry['rank']
            . ',' . $entry['last_rank']
            . ');'
        );
    }
    
    $rows = $db -> select('select player_id from standings where league_id = ' . $league_id . ';');
    foreach ($rows as $row) {
        for ($gameweek = 1; $gameweek <= $last_played_gameweek; ++$gameweek) {
            $url_entry = 'https://fantasy.premierleague.com/drf/entry/'. $row['player_id'] .'/event/'.$gameweek.'/picks';
            get_entries($url_entry, $row['player_id'], $gameweek);
        }
    }
}

function get_fpl_response($url) {
    $response = file_get_contents($url);
    $json = json_decode($response, true);
    return $json;
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
    // $query = $db -> query('truncate table players;');

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
        'insert into entries (id, player_id, gameweek, points, value, bank) values ('
        . $entry['id']
        . ',' . $player_id
        . ',' . $gameweek
        . ',' . $entry['points']
        . ',' . $entry['value']
        . ',' . $entry['bank'] 
        . ');';
    $query = $db -> query($sql);
    echo $sql . '<br>';
}

?>