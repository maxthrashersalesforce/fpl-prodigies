<?php

echo get_winners(3281, 1);


function get_winners($league_id, $page) {
    $last_played_gameweek = 38;

    $body = '<table>';

    $json_response = file_get_contents('https://fantasy.premierleague.com/drf/' . 'leagues-classic-standings/' . $league_id . "?phase=1&le-page=1&ls-page=" . $page);
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
                $resp = file_get_contents('https://fantasy.premierleague.com/drf/' . "entry/" . $entry['entry'] . "/event/" . $gameweek . "/picks");
                $arr = json_decode($resp, true);
                $map_user_name[$entry['entry']] = $entry['entry_name'];

                $all_entries = $arr['entry_history'];
                $pts = $all_entries['points'];
                // echo('points: ' . $pts);
                $u_name = $map_user_name[$entry['entry']];
                // echo $pts . ' > ' . $week_winner_pts . '<br>';
                if (($pts > $week_winner_pts) && ($u_name != "Stranger Ings") && ($u_name != "Jose & the Lads")) { // && ($u_name != "Farke Me Sideways")) {
                    $week_winner_pts = $pts;
                    $week_winner_name = '<td>' . $u_name . '</td>';
                }
                else if (($pts == $week_winner_pts) && ($u_name != "Stranger Ings") && ($u_name != "Jose & the Lads")) { // && ($u_name != "Farke Me Sideways")) {
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
    return $body . '</table>';
}