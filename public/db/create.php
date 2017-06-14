<?php

require_once('db.php');

$db = New db();

$query = $db -> query('drop table standings;');

$sql = 'CREATE TABLE `standings` (
  `id` int(11) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `league_id` int(11) DEFAULT NULL,
  `player_id` int(11) DEFAULT NULL,
  `entry_name` nvarchar(255) DEFAULT NULL,
  `total` int(11) DEFAULT NULL,
  `rank` int(11) DEFAULT NULL,
  `last_rank` int(11) DEFAULT NULL,
  `player_name` nvarchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;';

$query = $db -> query($sql);


$query = $db -> query('drop table fixtures');

$sql = 'CREATE TABLE `fixtures` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `h_id` int(11) DEFAULT NULL,
  `h_score` int(11) DEFAULT NULL,
  `a_id` int(11) DEFAULT NULL,
  `a_score` int(11) DEFAULT NULL,
  `gameweek` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;';

$query = $db -> query($sql);

$query = $db -> query('drop table entries');

$sql = 'CREATE TABLE `entries` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `player_id` int(11) DEFAULT NULL,
  `gameweek` int(11) DEFAULT NULL,
  `points` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;';

$query = $db -> query($sql);

// $query = $db -> query('drop table fixtures');

$sql = 'CREATE TABLE `teams` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` nvarchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;';

$query = $db -> query($sql);

// $query = $db -> query('drop table fixtures');

$sql = 'CREATE TABLE `players` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` nvarchar(255) DEFAULT NULL,
  `team` int(11) DEFAULT NULL,
  `goals_scored` int(11) DEFAULT NULL,
  `assists` int(11) DEFAULT NULL,
  `clean_sheets` int(11) DEFAULT NULL,
  `goals_conceded` int(11) DEFAULT NULL,
  `bps` int(11) DEFAULT NULL,
  
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;';

$query = $db -> query($sql);

?>