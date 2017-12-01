<?php
/**
 * Created by PhpStorm.
 * User: bryce
 * Date: 12/1/2017
 * Time: 1:35 PM
 */
require_once(__DIR__ . '/common.php');
$msg = $_POST['msg'];
slack($msg);