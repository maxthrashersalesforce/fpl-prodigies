<script>
  !function(){var analytics=window.analytics=window.analytics||[];if(!analytics.initialize)if(analytics.invoked)window.console&&console.error&&console.error("Segment snippet included twice.");else{analytics.invoked=!0;analytics.methods=["trackSubmit","trackClick","trackLink","trackForm","pageview","identify","reset","group","track","ready","alias","debug","page","once","off","on"];analytics.factory=function(t){return function(){var e=Array.prototype.slice.call(arguments);e.unshift(t);analytics.push(e);return analytics}};for(var t=0;t<analytics.methods.length;t++){var e=analytics.methods[t];analytics[e]=analytics.factory(e)}analytics.load=function(t){var e=document.createElement("script");e.type="text/javascript";e.async=!0;e.src=("https:"===document.location.protocol?"https://":"http://")+"cdn.segment.com/analytics.js/v1/"+t+"/analytics.min.js";var n=document.getElementsByTagName("script")[0];n.parentNode.insertBefore(e,n)};analytics.SNIPPET_VERSION="4.0.0";
  analytics.load("wr696Jbp3LFN0wMVjzsmNPm9xWFoN0kd");
  analytics.page();
  }}();
</script>

<?php

require_once('db.php');

$url_fpl = "https://fantasy.premierleague.com/drf/";
$url_standings = "leagues-classic-standings/";
$url_players = "bootstrap-static";
 
 if (gethostname() == 'scotchbox') {
    $server = "localhost";
    $user = "root";
    $pw = "root";
    $db = 'fpl';
    $port = null;
} elseif (gethostname() == 'aws') {
    $server = "aap18lpm7xe6ss.cauxglmclxtj.us-east-1.rds.amazonaws.com";
        $user = "root";
        $pw = "rootroot";
        $db = 'ebdb';
        $port = '3306';
} else {    
    $server = "127.0.0.1";
    $user = "root";
    $pw = "Thysk9315";
    $db = 'fpl';
    $port = '3306';
}
?>

<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="icon" href="i/epl.png">

        <title>Drongy's</title>

        <!-- Bootstrap core CSS -->
        <link href="css/bootstrap.min.css" rel="stylesheet">
        <!-- Bootstrap theme -->
        <link href="css/bootstrap-theme.min.css" rel="stylesheet">
        <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
        
        <link href="https://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css" rel="stylesheet">
        <!-- Custom styles for this template -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    </head>
    <body>     
        <div class="container theme-showcase" role="main">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a data-toggle="tab" href="#standings">Standings</a>
                </li>
                <li>
                    <a data-toggle="tab" href="#power">Power Rankings</a>
                </li>
                <li>
                    <a data-toggle="tab" href="#attack">Attack</a>
                </li>
                <li>
                    <a data-toggle="tab" href="#defense">Defense</a>
                </li>
                <li>
                    <a data-toggle="tab" href="#player">Players</a>
                </li>
            </ul>
            <div class="tab-content">
                <div id="standings" class="tab-pane fade in active">
                    <!-- <input type="text"> -->
                    <table class="table table-striped">
                        <?php echo standings_table('270578'); ?>
                    </table>
                </div>
                <div id="attack" class="tab-pane fade">
                    <iframe src="http://docs.google.com/gview?url=https://fantasypl.files.wordpress.com/2016/09/attack5.pdf&embedded=true" style="width: 100%; height: 100%;" frameborder="0"></iframe>
                </div>
                <div id="defense" class="tab-pane fade">
                    <iframe src="http://docs.google.com/gview?url=https://fantasypl.files.wordpress.com/2016/09/defense5.pdf&embedded=true" style="width: 100%; height: 100%;" frameborder="0"></iframe>
                </div>
                <div id="power" class="tab-pane fade">
                    <table id="power" class="table table-striped">
                        <?php power_table(270578, 27); ?>
                    </table>
                </div>
                <div id="player" class="tab-pane fade">
                    <table id="players" class="display">
                        <thead>
                            <tr>
                                <th>Player</th>
                                <th>Goals</th>
                                <th>Assists</th>
                                <th>Clean Sheets</th>
                                <th>BPS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php players_table(); ?>
                        </tbody>
                    </table>
                </div>
           <!--  <div class="col-md-4">
                <table class="table table-striped">
                    <thead>
                        <th>Gameweek</th>
                        <th>Player</th>
                        <th>Points</th>
                    </thead>
                    <tbody>
                        <?php // echo get_winners('270578', 1); ?>
                    </tbody>
                </table>
            </div> -->

        </div>
        <script type="text/javascript" src="js/bootstrap.min.js"></script>
        <script type="text/javascript" src="js/bootstrap.js"></script>
        <script src="js/test.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js"></script>
    </body>
</html>

<?php

function standings_table($league_id) {
    $db = New db;
    $rows = $db -> select('select entry_name, total, rank, last_rank, value, bank from standings s
                        join entries e on e.player_id = s.player_id and e.`gameweek` = (select max(gameweek) from entries)
                        where league_id = ' . $league_id . ';');

    $thead .= '<thead>';
    $thead .=     '<th>Player</th>';
    $thead .=     '<th>Points</th>';
    $thead .=     '<th>Team Value</th>';
    $thead .=     '<th>Bank</th>';
    $thead .=     '<th></th>';
    $thead .= '</thead>';

    // $body = '<tbody><tr><td>td</td></tr></tbody>';
    $tbody .= '<tbody>';
   // print_r($rows);
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

    foreach ($rows as $row) {
        $body .= '<tr>';
        $body .= '<td>' . $row['entry_name'] . '</td>';
        $body .= '<td>' . $row['pts'] . '</td>';
        $body .= '<td>' . $row['total'] . '</td>';
        $body .= '</tr>';
    }

    echo $body;
}

function players_table() {
    $db = New db;
    $rows = $db -> select('select id, name, team, goals_scored, assists, clean_sheets, goals_conceded, bps from players');
 
    foreach ($rows as $row) {
        $body .= '<tr>';
        $body .= '<td>' . $row['name'] . '</td>';
        $body .= '<td>' . $row['goals_scored'] . '</td>';
        $body .= '<td>' . $row['assists'] . '</td>';
        $body .= '<td>' . $row['clean_sheets'] . '</td>';
        $body .= '<td>' . $row['bps'] . '</td>';
        $body .= '</tr>';
    }

    echo $body;
}

function get_winners($league_id, $page) {
    global $url_fpl, $url_standings, $url_players;
    $body = '';

    $json_response = file_get_contents($url_fpl . $url_standings . $league_id . "?phase=1&le-page=1&ls-page=" . $page);
    $array = json_decode($json_response, true);
    $json_standings = $array['standings']['results'];

    if ($json_standings == false) {
        return null;
    } else {

        for ($gameweek = 1; $gameweek <= 5; ++$gameweek) {
            $week_winner_name = '';
            $week_winner_pts = 0;
            $week_winner_total = 0;

            foreach ($json_standings as $entry) {
                $resp = file_get_contents($url_fpl . "entry/" . $entry['entry'] . "/event/" . $gameweek . "/picks");
                $arr = json_decode($resp, true);
                $map_user_name[$entry['entry']] = $entry['entry_name'];

                $all_entries = $arr['entry_history'];
                $pts = $all_entries['points'];
                $u_name = $map_user_name[$entry['entry']];

                if ($pts > $week_winner_pts && $u_name != "Mid Table or Bust") {
                    $week_winner_pts = $pts;
                    $week_winner_name = $u_name;
                }
            }
            $body .= '<tr>';
            $body .= '<td>' . $gameweek . '</td>';
            $body .= '<td>' . $week_winner_name . '</td>';
            $body .= '<td>' . $week_winner_pts . '</td>';
            $body .= '</tr>';
        }
    }
    return $body;
}

?>
