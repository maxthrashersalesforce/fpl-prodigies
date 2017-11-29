<?php
/**
 * Created by PhpStorm.
 * User: bryce
 * Date: 11/27/2017
 * Time: 10:58 AM
 */

class grUser
{
    private $URL = 'https://www.goodreads.com';
    private $KEY = 'OZol1R3uOigt3eM2t3HyA';
    private $SECRET = 'LZ2Uq7iPd6N714R7TWLuUGMQFRUfaURjoV8zl7JSJc';

    public function getUser($userId) {

        $params = array(
            'key' => $this->KEY
            ,'id' => $userId
        );

        $endpoint = 'user/show';
        $url = $this->URL .'/'. $endpoint . '?' . ((!empty($params)) ? http_build_query($params, '', '&') : '');
        $headers = array(
            'Accept: application/xml',
        );
        if(isset($params['format']) && $params['format'] === 'json') {
            $headers = array(
                'Accept: application/json',
            );
        }
        // Execute via CURL
        $response = null;
        if(extension_loaded('curl')) {
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
            if($errorNumber > 0)
            {
                throw new Exception('Method failed: ' . $endpoint . ': ' . $errorMessage);
            }
            curl_close($ch);
        } else {
            throw new Exception('CURL library not loaded!');
        }

        // Try and cadge the results into a half-decent array
        $results = null;
        if(isset($params['format']) && $params['format'] === 'json') {
            $results = json_decode($body);
        } else {
//            $p = xml_parser_create();
//            xml_parse_into_struct($p, $body, $vals, $index);
//            xml_parser_free($p);
            return 1; // $vals;
           // $results = json_decode(json_encode((array)simplexml_load_string($body, 'SimpleXMLElement', LIBXML_NOCDATA)), 1); // I know, I'm a terrible human being
        }

        if($results !== null) {
            // Cache & return results
            // $this->addCache($endpoint, $params, $results);
            return $results;
        } else {
            throw new Exception('Server error on "' . $url . '": ' . $response);
        }
    }

}