<?php

class spotifyUser
{
    private $URL = 'https://www.api.spotify.com';
    private $KEY = '9c7e3fd10710461b8127423dbfcecf47';
    private $SECRET = '11317452a28e4364863fbf87572d8905';

    public function getUser($userId)
    {

        $params = array(
            'Authorization' => 'Bearer '. $this->KEY
        //, 'id' => $userId
        );

        $endpoint = '/v1/me/player/recently-played';
        $url = $this->URL . '/' . $endpoint . '?' . ((!empty($params)) ? http_build_query($params, '', '&') : '');

        $headers = array(
            'Accept: application/json',
        );

        $response = null;
        if (extension_loaded('curl')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $response = curl_exec($ch);
            $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $header = substr($response, 0, $headerSize);
            $body = substr($response, $headerSize);
            $errorNumber = curl_errno($ch);
            $errorMessage = curl_error($ch);
            if ($errorNumber > 0) {
                throw new Exception('Method failed: ' . $endpoint . ': ' . $errorMessage);
            }
            curl_close($ch);
        } else {
            throw new Exception('CURL library not loaded!');
        }

        // Try and cadge the results into a half-decent array
        $results = null;
        $results = json_decode($body);

        if ($results !== null) {
            return $results;
        } else {
            throw new Exception('Server error on "' . $url . '": ' . $response);
        }
    }
}