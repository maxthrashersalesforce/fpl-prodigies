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