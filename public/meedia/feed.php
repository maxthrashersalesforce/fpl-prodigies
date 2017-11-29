<?php
/**
 * Created by PhpStorm.
 * User: bryce
 * Date: 11/27/2017
 * Time: 11:11 AM
 */

require_once ('grUser.php');
require_once ('spotifyUser.php');

//$gr = new grUser();
//$r = $gr->getUser(35874684);
//echo $r;

$spotify = new spotifyUser();
$r = $spotify->getUser(12345);
echo $r;
