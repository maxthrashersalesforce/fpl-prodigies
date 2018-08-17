<?php
/**
 * Created by PhpStorm.
 * User: bryce
 * Date: 11/29/2017
 * Time: 9:38 AM
 */
require_once ('weather.php');
$w = new weather();


$w -> get_player();

//$r = $w -> get('1', 2);
//echo '<pre>'; print_r($r); echo '</pre>';