<?php

$now = date("Y-m-d H:i:s"); // games start at 7:45 GMT
// $go_live = '2018-01-01 12:10:00';
// $down_time = '2018-01-01 11:30:00';

if ($now > '2018-05-13 14:40:00') { // if ($now > '2017-12-12 12:10:00') { 12:10 is the usual saturday start time, night games are 19:45, so 19:25
    define('CURRENT_GW', 1);
    define('TIME_TIL_LIVE', 0);
} else {
    define('CURRENT_GW', 1);
    define ('TIME_TIL_LIVE', 0);
}

define('URL_FPL', 'https://fantasy.premierleague.com/drf/');
define('URL_STANDINGS',URL_FPL.'leagues-classic-standings/');
define('URL_STANDINGS_H2H',URL_FPL.'leagues-h2h-standings/');
define('URL_TEAMS',URL_FPL.'teams/');
define('URL_FIXTURES',URL_FPL.'fixtures/');
define('URL_PLAYERS',URL_FPL.'bootstrap-static');
define('URL_PLAYERS_DETAIL',URL_FPL.'element-summary/');

session_start();

function slack_err($msg, $channel = 'fpl-prodigies')
{
    /**
     * Send debugging / error messages to Slack.
     *
     * Example:
     *
     * slack($msg)                     --> sends a message to #idb-logs, the default channel
     * slack($msg, '#channel')         --> sends a message to another channel
     * slack($msg, '@bryce.heltzel');  --> sends a message to a user
     **/

    $de_bk = debug_backtrace();
    $call = array_shift($de_bk);

    $url = "https://hooks.slack.com/services/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";

    if (is_array($msg)) {
        $text = "ERR: " . $_SERVER['PHP_SELF'] . "\nLN: " . $call['line'] . "\nID: " . USERID . "\nMSG: " . print_r($msg, true);
        $pretext = print_r($msg, true);
    } elseif ($msg instanceof DateTime) {
        $text = "ERR: " . $_SERVER['PHP_SELF'] . "\nLN: " . $call['line'] . "\nID: " . USERID . "\nMSG: " . $msg->format('Y-m-d H:i:s');
        $pretext = $msg->format('Y-m-d H:i:s');
    } else {
        $text = "ERR: " . $_SERVER['PHP_SELF'] . "\nLN: " . $call['line'] . "\nID: " . USERID . "\nMSG: " . $msg;
        $pretext = $msg;
    }

    $channel = isset($channel) ? $channel : "fpl-prodigies";

    $fields = array(
        array(
            "title" => "File"
        , "value" => mrkdwn_code($_SERVER['PHP_SELF'])
        , "short" => "true"
        )
    , array(
            "title" => "User"
        , "value" => mrkdwn_code('n/a')
        , "short" => "true"
        )
    , array(
            "title" => "Line"
        , "value" => mrkdwn_code($call['line'])
        , "short" => "true"
        )
    );

    $attachment = array(
        "fallback" => $text
    , "pretext" => $pretext
    , "color" => "EFD01B"
    , "fields" => $fields
    , "mrkdwn_in" => array("fields", "pretext")
    );
    $post = array(
        "channel" => $channel
    , "icon_emoji" => ":grey_exclamation:"
    , "username" => "FPL Bot"
    , "attachments" => array($attachment)
    );

    $payload = json_encode($post);

    // debug!
    // $pp = json_encode($post, JSON_PRETTY_PRINT);
    // echo $pp;

    $slack_curl = curl_init($url);

    curl_setopt($slack_curl, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($slack_curl, CURLOPT_POSTFIELDS, array('payload' => $payload));
    curl_setopt($slack_curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($slack_curl, CURLOPT_VERBOSE, true);
    curl_setopt($slack_curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($slack_curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($slack_curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
    curl_setopt($slack_curl, CURLOPT_FOLLOWLOCATION, true);

    $slack_resp = curl_exec($slack_curl);

    curl_close($slack_curl);

    return $slack_resp;
}

function slack($msg, $channel = 'fpl-prodigies')
{
    /**
     * Send debugging / error messages to Slack.
     *
     * Example:
     *
     * slack($msg)                     --> sends a message to #idb-logs, the default channel
     * slack($msg, '#channel')         --> sends a message to another channel
     * slack($msg, '@bryce.heltzel');  --> sends a message to a user
     **/

    $url = "https://hooks.slack.com/services/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";
    $channel = isset($channel) ? $channel : "fpl-prodigies";

    $fields = array(
        array(
            "title" => "File"
        , "value" => mrkdwn_code($_SERVER['PHP_SELF'])
        , "short" => "true"
        )
    );

    $text = $msg;
    $pretext = $msg;

    $attachment = array(
        "fallback" => $text
        , "pretext" => $pretext
        , "color" => "EFD01B"
        , "fields" => $fields
        , "mrkdwn_in" => array("fields", "pretext")
    );
    $post = array(
        "channel" => $channel
        , "icon_emoji" => ":grey_exclamation:"
        , "username" => "FPL Bot"
        , "attachments" => array($attachment)
    );

    $payload = json_encode($post);

    $slack_curl = curl_init($url);

    curl_setopt($slack_curl, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($slack_curl, CURLOPT_POSTFIELDS, array('payload' => $payload));
    curl_setopt($slack_curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($slack_curl, CURLOPT_VERBOSE, true);
    curl_setopt($slack_curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($slack_curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($slack_curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
    curl_setopt($slack_curl, CURLOPT_FOLLOWLOCATION, true);

    $slack_resp = curl_exec($slack_curl);

    curl_close($slack_curl);

    return $slack_resp;
}

function mrkdwn_code($code)
{
    $code = "`" . $code . "`";
    return $code;
}

function time_elapsed()
{
    static $last = null;

    $now = microtime(true);

    if ($last != null) {
        error_log('<!-- ' . ($now - $last) . ' -->');
    }

    $last = $now;
}

function array_order()
{
    $args = func_get_args();
    $data = array_shift($args);
    foreach ($args as $n => $field) {
        if (is_string($field)) {
            $tmp = array();
            foreach ($data as $key => $row)
                $tmp[$key] = $row[$field];
            $args[$n] = $tmp;
        }
    }
    $args[] = &$data;
    call_user_func_array('array_multisort', $args);
    return array_pop($args);
}

function th($name, $title = null, $width = null) {
    $title = $title ?: $name;
    $width = ($width == null) ? '' : ' style="'.$width.'%"';
    $h = '<th class="text-nowrap" title="'.$title.'" '.$width.'>'.$name.'</th>';
    return $h;
}

function td($name, $width = null) {
    $width = ($width == null) ? '' : ' style="width: '.$width.'%"';
    $d = '<td class="text-nowrap" '.$width.'>'.$name.'</td>';
    return $d;
}

function td_order($name, $order) {
    $d = '<td data-order="'.$order.'" class="text-nowrap">'.$name.'</td>';
    return $d;
}

function color_rank($user, $new_rank) {
    if ($user['last_rank'] == null) {
        $color = 'black';
    } else if ($user['last_rank'] < $new_rank) {
        $color = 'red';
    } else if ($user['last_rank'] == $new_rank) {
        $color = 'black';
    } else {
        $color = 'green';
    }

    return sprintf('<span style="color: %s;"> (%s)</span>', $color, $new_rank);
}

function get_fpl_response($url) {
    $response = file_get_contents($url);
    $json = json_decode($response, true);

    if (!$json) {
        return $json;
    } else {
        return $json;
    }
}