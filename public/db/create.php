<?php

require_once('db.php');

$db = New db();

// $query = $db -> query('create database fpl');

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
if (!$query) {
    echo 'error: ' . $sql;
}

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
if (!$query) {
    echo 'error: ' . $sql;
}

$query = $db -> query('drop table entries');

$sql = 'CREATE TABLE `entries` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `player_id` int(11) DEFAULT NULL,
  `gameweek` int(11) DEFAULT NULL,
  `points` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;';

$query = $db -> query($sql);
if (!$query) {
    echo 'error: ' . $sql;
}

$query = $db -> query('drop table teams');

$sql = 'CREATE TABLE `teams` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` nvarchar(255) DEFAULT NULL,
    `code` int(11) DEFAULT NULL,
    `short_name` nvarchar(10) DEFAULT NULL,
    `unavailable` nvarchar(5) default null,
    `strength` int(11) DEFAULT NULL,
    `position` int(11) DEFAULT NULL,
    `played` int(11) DEFAULT NULL,
    `win` int(11) DEFAULT NULL,
    `loss` int(11) DEFAULT NULL,
    `draw` int(11) DEFAULT NULL,
    `points` int(11) DEFAULT NULL,
    -- `form` float(5,2) default null,
    `link_url` nvarchar(255) DEFAULT NULL,
    `strength_overall_home` int(11) DEFAULT NULL,
    `strength_overall_away` int(11) DEFAULT NULL,
    `strength_attack_home` int(11) DEFAULT NULL,
    `strength_attack_away` int(11) DEFAULT NULL,
    `strength_defence_home` int(11) DEFAULT NULL,
    `strength_defence_away` int(11) DEFAULT NULL,
    `team_division` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;';

$query = $db -> query($sql);
if (!$query) {
    echo 'error: ' . $sql;
}

$query = $db -> query('drop table players');

$sql = 'CREATE TABLE `players` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `name` nvarchar(255) DEFAULT NULL,
    `team` int(11) DEFAULT NULL,
    `photo` nvarchar(255) DEFAULT NULL,
    `web_name` nvarchar(255) DEFAULT NULL,
    `team_code` int(11) DEFAULT NULL,
    `status` nvarchar(255) DEFAULT NULL,
    `code` int(11) DEFAULT NULL,
    `first_name` nvarchar(255) DEFAULT NULL,
    `second_name` nvarchar(255) DEFAULT NULL,
    `squad_number` int(3) DEFAULT NULL,
    `news` nvarchar(4000) DEFAULT NULL,
    `now_cost` int(11) DEFAULT NULL,
    `chance_of_playing_this_round` int(11) DEFAULT NULL,
    `chance_of_playing_next_round` int(11) DEFAULT NULL,
    `value_form` float(5,2) DEFAULT NULL,
    `value_season` float(5,2) DEFAULT NULL,
    `cost_change_start` int(11) DEFAULT NULL,
    `cost_change_event` int(11) DEFAULT NULL,
    `cost_change_start_fall` int(11) DEFAULT NULL,
    `cost_change_event_fall` int(11) DEFAULT NULL,
    `in_dreamteam` nvarchar(5) default null,
    `dreamteam_count` int(11) DEFAULT NULL,
    `selected_by_percent` float(5,2) default null,
    `form` float(5,2) default null,
    `transfers_out` int(11) DEFAULT NULL,
    `transfers_in` int(11) DEFAULT NULL,
    `transfers_out_event` int(11) DEFAULT NULL,
    `transfers_in_event` int(11) DEFAULT NULL,
    `loans_in` int(11) DEFAULT NULL,
    `loans_out` int(11) DEFAULT NULL,
    `loaned_in` int(11) DEFAULT NULL,
    `loaned_out` int(11) DEFAULT NULL,
    `total_points` int(11) DEFAULT NULL,
    `event_points` int(11) DEFAULT NULL,
    `points_per_game` float(5,2),
    `ep_this` float(5,2) default null,
    `ep_next` float(5,2) default null,
    `special` nvarchar(5) default null,
    `minutes` int(11) DEFAULT NULL,
    `goals_scored` int(11) DEFAULT NULL,
    `assists` int(11) DEFAULT NULL,
    `clean_sheets` int(11) DEFAULT NULL,
    `goals_conceded` int(11) DEFAULT NULL,
    `own_goals` int(11) DEFAULT NULL,
    `penalties_saved` int(11) DEFAULT NULL,
    `penalties_missed` int(11) DEFAULT NULL,
    `yellow_cards` int(11) DEFAULT NULL,
    `red_cards` int(11) DEFAULT NULL,
    `saves` int(11) DEFAULT NULL,
    `bonus` int(11) DEFAULT NULL,
    `bps` int(11) DEFAULT NULL,
    `influence` float(5,2) DEFAULT NULL,
    `creativity` float(5,2) DEFAULT NULL,
    `threat` float(5,2) DEFAULT NULL,
    `ict_index` float(5,2) DEFAULT NULL,
    `ea_index` int(11) DEFAULT NULL,
    `element_type` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;';

$query = $db -> query($sql);
if (!$query) {
    echo 'error: ' . $sql;
}

$query = $db -> query('drop view fixture_table');

$sql = "create view fixture_table
as
select t.name, 
	ifnull((
		select concat(t2.name) from fixtures f 
		join teams t2 on t2.id = f.h_id 
		where f.`a_id` = t.id and gameweek = 1
	), (select concat(t2.name, ' (H)') from fixtures f 
		join teams t2 on t2.id = f.a_id 
		where f.`h_id` = t.id and gameweek = 1
	)) gw1, ifnull((
		select concat(t2.name, ' (A)') from fixtures f 
		join teams t2 on t2.id = f.h_id 
		where f.`a_id` = t.id and gameweek = 2
	), (select concat(t2.name, ' (H)') from fixtures f 
		join teams t2 on t2.id = f.a_id 
		where f.`h_id` = t.id and gameweek = 2
	)) gw2, ifnull((
		select concat(t2.name, ' (A)')  from fixtures f 
		join teams t2 on t2.id = f.h_id 
		where f.`a_id` = t.id and gameweek = 3
	), (select concat(t2.name, ' (H)') from fixtures f 
		join teams t2 on t2.id = f.a_id 
		where f.`h_id` = t.id and gameweek = 3
	)) gw3, ifnull((
		select concat(t2.name, ' (A)')  from fixtures f 
		join teams t2 on t2.id = f.h_id 
		where f.`a_id` = t.id and gameweek = 4
	), (select concat(t2.name, ' (H)') from fixtures f 
		join teams t2 on t2.id = f.a_id 
		where f.`h_id` = t.id and gameweek = 4
	)) gw4, ifnull((
		select concat(t2.name, ' (A)')  from fixtures f 
		join teams t2 on t2.id = f.h_id 
		where f.`a_id` = t.id and gameweek = 5
	), (select concat(t2.name, ' (H)') from fixtures f 
		join teams t2 on t2.id = f.a_id 
		where f.`h_id` = t.id and gameweek = 5
	)) gw5, ifnull((
		select concat(t2.name, ' (A)')  from fixtures f 
		join teams t2 on t2.id = f.h_id 
		where f.`a_id` = t.id and gameweek = 6
	), (select concat(t2.name, ' (H)') from fixtures f 
		join teams t2 on t2.id = f.a_id 
		where f.`h_id` = t.id and gameweek = 6
	)) gw6, ifnull((
		select concat(t2.name, ' (A)')  from fixtures f 
		join teams t2 on t2.id = f.h_id 
		where f.`a_id` = t.id and gameweek = 7
	), (select concat(t2.name, ' (H)') from fixtures f 
		join teams t2 on t2.id = f.a_id 
		where f.`h_id` = t.id and gameweek = 7
	)) gw7, ifnull((
		select concat(t2.name, ' (A)')  from fixtures f 
		join teams t2 on t2.id = f.h_id 
		where f.`a_id` = t.id and gameweek = 8
	), (select concat(t2.name, ' (H)') from fixtures f 
		join teams t2 on t2.id = f.a_id 
		where f.`h_id` = t.id and gameweek = 8
	)) gw8, ifnull((
		select concat(t2.name, ' (A)')  from fixtures f 
		join teams t2 on t2.id = f.h_id 
		where f.`a_id` = t.id and gameweek = 9
	), (select concat(t2.name, ' (H)') from fixtures f 
		join teams t2 on t2.id = f.a_id 
		where f.`h_id` = t.id and gameweek = 9
	)) gw9, ifnull((
		select concat(t2.name, ' (A)')  from fixtures f 
		join teams t2 on t2.id = f.h_id 
		where f.`a_id` = t.id and gameweek = 10
	), (select concat(t2.name, ' (H)') from fixtures f 
		join teams t2 on t2.id = f.a_id 
		where f.`h_id` = t.id and gameweek = 10
	)) gw10, ifnull((
		select concat(t2.name, ' (A)')  from fixtures f 
		join teams t2 on t2.id = f.h_id 
		where f.`a_id` = t.id and gameweek = 11
	), (select concat(t2.name, ' (H)') from fixtures f 
		join teams t2 on t2.id = f.a_id 
		where f.`h_id` = t.id and gameweek = 11
	)) gw11, ifnull((
		select concat(t2.name, ' (A)')  from fixtures f 
		join teams t2 on t2.id = f.h_id 
		where f.`a_id` = t.id and gameweek = 12
	), (select concat(t2.name, ' (H)') from fixtures f 
		join teams t2 on t2.id = f.a_id 
		where f.`h_id` = t.id and gameweek = 12
	)) gw12, ifnull((
		select concat(t2.name, ' (A)')  from fixtures f 
		join teams t2 on t2.id = f.h_id 
		where f.`a_id` = t.id and gameweek = 13
	), (select concat(t2.name, ' (H)') from fixtures f 
		join teams t2 on t2.id = f.a_id 
		where f.`h_id` = t.id and gameweek = 13
	)) gw13, ifnull((
		select concat(t2.name, ' (A)')  from fixtures f 
		join teams t2 on t2.id = f.h_id 
		where f.`a_id` = t.id and gameweek = 14
	), (select concat(t2.name, ' (H)') from fixtures f 
		join teams t2 on t2.id = f.a_id 
		where f.`h_id` = t.id and gameweek = 14
	)) gw14, ifnull((
		select concat(t2.name, ' (A)')  from fixtures f 
		join teams t2 on t2.id = f.h_id 
		where f.`a_id` = t.id and gameweek = 15
	), (select concat(t2.name, ' (H)') from fixtures f 
		join teams t2 on t2.id = f.a_id 
		where f.`h_id` = t.id and gameweek = 15
	)) gw15, ifnull((
		select concat(t2.name, ' (A)')  from fixtures f 
		join teams t2 on t2.id = f.h_id 
		where f.`a_id` = t.id and gameweek = 16
	), (select concat(t2.name, ' (H)') from fixtures f 
		join teams t2 on t2.id = f.a_id 
		where f.`h_id` = t.id and gameweek = 16
	)) gw16, ifnull((
		select concat(t2.name, ' (A)')  from fixtures f 
		join teams t2 on t2.id = f.h_id 
		where f.`a_id` = t.id and gameweek = 17
	), (select concat(t2.name, ' (H)') from fixtures f 
		join teams t2 on t2.id = f.a_id 
		where f.`h_id` = t.id and gameweek = 17
	)) gw17, ifnull((
		select concat(t2.name, ' (A)')  from fixtures f 
		join teams t2 on t2.id = f.h_id 
		where f.`a_id` = t.id and gameweek = 18
	), (select concat(t2.name, ' (H)') from fixtures f 
		join teams t2 on t2.id = f.a_id 
		where f.`h_id` = t.id and gameweek = 18
	)) gw18, ifnull((
		select concat(t2.name, ' (A)')  from fixtures f 
		join teams t2 on t2.id = f.h_id 
		where f.`a_id` = t.id and gameweek = 19
	), (select concat(t2.name, ' (H)') from fixtures f 
		join teams t2 on t2.id = f.a_id 
		where f.`h_id` = t.id and gameweek = 19
	)) gw19, ifnull((
		select concat(t2.name, ' (A)')  from fixtures f 
		join teams t2 on t2.id = f.h_id 
		where f.`a_id` = t.id and gameweek = 20
	), (select concat(t2.name, ' (H)') from fixtures f 
		join teams t2 on t2.id = f.a_id 
		where f.`h_id` = t.id and gameweek = 20
	)) gw20, ifnull((
		select concat(t2.name, ' (A)')  from fixtures f 
		join teams t2 on t2.id = f.h_id 
		where f.`a_id` = t.id and gameweek = 21
	), (select concat(t2.name, ' (H)') from fixtures f 
		join teams t2 on t2.id = f.a_id 
		where f.`h_id` = t.id and gameweek = 21
	)) gw21, ifnull((
		select concat(t2.name, ' (A)')  from fixtures f 
		join teams t2 on t2.id = f.h_id 
		where f.`a_id` = t.id and gameweek = 22
	), (select concat(t2.name, ' (H)') from fixtures f 
		join teams t2 on t2.id = f.a_id 
		where f.`h_id` = t.id and gameweek = 22
	)) gw22, ifnull((
		select concat(t2.name, ' (A)')  from fixtures f 
		join teams t2 on t2.id = f.h_id 
		where f.`a_id` = t.id and gameweek = 23
	), (select concat(t2.name, ' (H)') from fixtures f 
		join teams t2 on t2.id = f.a_id 
		where f.`h_id` = t.id and gameweek = 23
	)) gw23, ifnull((
		select concat(t2.name, ' (A)')  from fixtures f 
		join teams t2 on t2.id = f.h_id 
		where f.`a_id` = t.id and gameweek = 24
	), (select concat(t2.name, ' (H)') from fixtures f 
		join teams t2 on t2.id = f.a_id 
		where f.`h_id` = t.id and gameweek = 24
	)) gw24, ifnull((
		select concat(t2.name, ' (A)')  from fixtures f 
		join teams t2 on t2.id = f.h_id 
		where f.`a_id` = t.id and gameweek = 25
	), (select concat(t2.name, ' (H)') from fixtures f 
		join teams t2 on t2.id = f.a_id 
		where f.`h_id` = t.id and gameweek = 25
	)) gw25, ifnull((
		select concat(t2.name, ' (A)')  from fixtures f 
		join teams t2 on t2.id = f.h_id 
		where f.`a_id` = t.id and gameweek = 26
	), (select concat(t2.name, ' (H)') from fixtures f 
		join teams t2 on t2.id = f.a_id 
		where f.`h_id` = t.id and gameweek = 26
	)) gw26, ifnull((
		select concat(t2.name, ' (A)')  from fixtures f 
		join teams t2 on t2.id = f.h_id 
		where f.`a_id` = t.id and gameweek = 27
	), (select concat(t2.name, ' (H)') from fixtures f 
		join teams t2 on t2.id = f.a_id 
		where f.`h_id` = t.id and gameweek = 27
	)) gw27, ifnull((
		select concat(t2.name, ' (A)')  from fixtures f 
		join teams t2 on t2.id = f.h_id 
		where f.`a_id` = t.id and gameweek = 28
	), (select concat(t2.name, ' (H)') from fixtures f 
		join teams t2 on t2.id = f.a_id 
		where f.`h_id` = t.id and gameweek = 28
	)) gw28, ifnull((
		select concat(t2.name, ' (A)')  from fixtures f 
		join teams t2 on t2.id = f.h_id 
		where f.`a_id` = t.id and gameweek = 29
	), (select concat(t2.name, ' (H)') from fixtures f 
		join teams t2 on t2.id = f.a_id 
		where f.`h_id` = t.id and gameweek = 29
	)) gw29, ifnull((
		select concat(t2.name, ' (A)')  from fixtures f 
		join teams t2 on t2.id = f.h_id 
		where f.`a_id` = t.id and gameweek = 30
	), (select concat(t2.name, ' (H)') from fixtures f 
		join teams t2 on t2.id = f.a_id 
		where f.`h_id` = t.id and gameweek = 30
	)) gw30, ifnull((
		select concat(t2.name, ' (A)')  from fixtures f 
		join teams t2 on t2.id = f.h_id 
		where f.`a_id` = t.id and gameweek = 31
	), (select concat(t2.name, ' (H)') from fixtures f 
		join teams t2 on t2.id = f.a_id 
		where f.`h_id` = t.id and gameweek = 31
	)) gw31, ifnull((
		select concat(t2.name, ' (A)')  from fixtures f 
		join teams t2 on t2.id = f.h_id 
		where f.`a_id` = t.id and gameweek = 32
	), (select concat(t2.name, ' (H)') from fixtures f 
		join teams t2 on t2.id = f.a_id 
		where f.`h_id` = t.id and gameweek = 32
	)) gw32, ifnull((
		select concat(t2.name, ' (A)')  from fixtures f 
		join teams t2 on t2.id = f.h_id 
		where f.`a_id` = t.id and gameweek = 33
	), (select concat(t2.name, ' (H)') from fixtures f 
		join teams t2 on t2.id = f.a_id 
		where f.`h_id` = t.id and gameweek = 33
	)) gw33, ifnull((
		select concat(t2.name, ' (A)')  from fixtures f 
		join teams t2 on t2.id = f.h_id 
		where f.`a_id` = t.id and gameweek = 34
	), (select concat(t2.name, ' (H)') from fixtures f 
		join teams t2 on t2.id = f.a_id 
		where f.`h_id` = t.id and gameweek = 34
	)) gw34, ifnull((
		select concat(t2.name, ' (A)')  from fixtures f 
		join teams t2 on t2.id = f.h_id 
		where f.`a_id` = t.id and gameweek = 35
	), (select concat(t2.name, ' (H)') from fixtures f 
		join teams t2 on t2.id = f.a_id 
		where f.`h_id` = t.id and gameweek = 35
	)) gw35, ifnull((
		select concat(t2.name, ' (A)')  from fixtures f 
		join teams t2 on t2.id = f.h_id 
		where f.`a_id` = t.id and gameweek = 36
	), (select concat(t2.name, ' (H)') from fixtures f 
		join teams t2 on t2.id = f.a_id 
		where f.`h_id` = t.id and gameweek = 36
	)) gw36, ifnull((
		select concat(t2.name, ' (A)')  from fixtures f 
		join teams t2 on t2.id = f.h_id 
		where f.`a_id` = t.id and gameweek = 37
	), (select concat(t2.name, ' (H)') from fixtures f 
		join teams t2 on t2.id = f.a_id 
		where f.`h_id` = t.id and gameweek = 37
	)) gw37, ifnull((
		select concat(t2.name, ' (A)')  from fixtures f 
		join teams t2 on t2.id = f.h_id 
		where f.`a_id` = t.id and gameweek = 38
	), (select concat(t2.name, ' (H)') from fixtures f 
		join teams t2 on t2.id = f.a_id 
		where f.`h_id` = t.id and gameweek = 38
	)) gw38
from teams t;";

$query = $db -> query($sql);
if (!$query) {
    echo 'error: ' . $sql;
}

?>