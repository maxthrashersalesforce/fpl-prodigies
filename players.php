<?php

    $url_fpl = "https://fantasy.premierleague.com/drf/";
    $url_standings = "leagues-classic-standings/";
    $url_players = "bootstrap-static";
    $server = "localhost";
    $user = "root";
    $pw = "root";
    $db = 'fpl';

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
        <link href="https://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css" rel="stylesheet">
        <!-- Custom styles for this template -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    </head>
    <body>
        <table id="players" class="display">
            <thead>
                <tr>
                    <th>Player</th>
                    <th>Points</th>
                </tr>
            </thead>
            <tbody>
            <tr><td>1</td><td>1</td></tr>
    <?php
        global $server, $user, $pw, $db;
        $body = '';

        $conn = mysqli_connect($server, $user, $pw, $db);

        $sql = 'select id, name, team, goals_scored, assists, clean_sheets, goals_conceded, bps from players;';
        $result = $conn->query($sql);
        while($row = $result->fetch_assoc()) {
            $body .= '<tr>';
            $body .= '<td>' . $row['name'] . '</td>';
            $body .= '<td>' . $row['goals_scored'] . '</td>';
            $body .= '</tr>';
        }

        $conn->close;

        echo $body;
    ?>
            </tbody>
        </table>
        <script type="text/javascript" src="js/bootstrap.min.js"></script>
        <script type="text/javascript" src="js/bootstrap.js"></script>
        <script src="js/test.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js"></script>
    </body>
</html>