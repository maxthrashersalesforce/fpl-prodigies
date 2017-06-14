<?php

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

function players_table() {
    $db = New db;
    $rows = $db -> select('select id, name, team, goals_scored, assists, clean_sheets, goals_conceded, bps from players');
    
    $body = '<thead>
                <tr>
                    <th>Player</th>
                    <th>Goals</th>
                    <th>Assists</th>
                    <th>Clean Sheets</th>
                    <th>BPS</th>
                </tr>
            </thead>
            <tbody>';
    foreach ($rows as $row) {
        $body .= '<tr>';
        $body .= '<td>' . $row['name'] . '</td>';
        $body .= '<td>' . $row['goals_scored'] . '</td>';
        $body .= '<td>' . $row['assists'] . '</td>';
        $body .= '<td>' . $row['clean_sheets'] . '</td>';
        $body .= '<td>' . $row['bps'] . '</td>';
        $body .= '</tr>';
    }
    $body .= '</tbody>';

    return $body;
}


?>