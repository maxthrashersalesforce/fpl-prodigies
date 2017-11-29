<?php

$now = date("Y-m-d H:i:s"); // games start at 7:45 GMT

if ($now > '2017-11-28 19:45:00') {
    define('CURRENT_GW', 14);
} else {
    define('CURRENT_GW', 13);
}

define('URL_FPL', 'https://fantasy.premierleague.com/drf/');
define('URL_STANDINGS','leagues-classic-standings/');
define('URL_TEAMS',URL_FPL.'teams/');
define('URL_FIXTURES',URL_FPL.'fixtures/');
define('URL_PLAYERS',URL_FPL.'bootstrap-static');
define('URL_PLAYERS_DETAIL',URL_FPL.'element-summary/');


