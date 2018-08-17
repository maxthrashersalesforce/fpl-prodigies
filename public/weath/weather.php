<?php
require_once(__DIR__ . '/../db/db.php');

class weather
{
    function get_player($player) {
        $db = New db;
        $matches = $db -> select(
            'SELECT
                      pd.element AS id,
                      pd.total_points,
                      f.kickoff_time,
                      s.woeid,
                      p.web_name
                    FROM players_detail pd
                      join fixtures f on f.id = pd.fixture
                      join stadiums s on s.id = f.team_h
                      join players p on p.id = pd.element
                    WHERE element = '.$player.';');

        $table = '<h2>'.$matches[0]['web_name'].'</h2><br><table id="player_table" class="table table-striped table-condensed table-sm">';
        $table .= '<thead><th>Weather</th><th>Total Points</th></thead>';
        foreach ($matches as $match) {
            $table .= '<tr><td>';
            $time = substr($match['kickoff_time'], 0, 10);
            $api_time = date("Y/m/d", strtotime($time));
            $table .= $this->get_weather($match['woeid'], $api_time);
            $table .= '</td><td>';
            $table .= $match['total_points'];
            $table .= '</td></tr>';
        }
        return $table;
    }

    function get_weather($location, $date) {
        $all_forecasts = $this->get_response('https://www.metaweather.com/api/location/'.$location.'/'.$date);
        $recent_forecast = $all_forecasts[0];
        $weather_state = $recent_forecast['weather_state_name'];
        return $weather_state;
    }

    function get_response($url) {
        $response = file_get_contents($url);
        return json_decode($response, true);
    }
}

