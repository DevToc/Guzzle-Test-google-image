<?php

    require 'vendor/autoload.php';


use GuzzleHttp\Client;

$url = "https://photos.app.goo.gl/F8kukkVQix2yKhJT7";
$client = new Client();
$response = $client->get($url);

$body = $response->getBody()->getContents();

// Print the raw response

// Try to extract the AF_initDataCallback content
$re = '/<script class="ds:[^"]+" nonce="[^"]+">AF_initDataCallback\(\{[^<]+, data:([^<]+)\}\);<\/script>/m';
preg_match_all($re, $body, $matches, PREG_SET_ORDER, 0);

$json = str_replace(', sideChannel: {}', '', $matches[1]);
$data = json_decode($json[1], true);
if(isset($data[1])){
    // limit to 15 entries by gallery
    //$data[1] = array_slice($data[1], 0, 15);
    $images = array_map(function ($image) {
        // . '=w4032-h2268-no'
        //  . '=w' . $image[1][1] . '-h' . $image[1][2] . '-no'
        $image[1][1] = 1920;
        $image[1][2] = 1280;
        return [
            'id' => $image[0],
            // default url
            'url' => $image[1][0] . '=w' . $image[1][1] . '-h' . $image[1][2] . '-no',
            // max size
            'width' => $image[1][1],
            'height' => $image[1][2]
        ];
    }, $data[1]);
    $resdata = [
        'id' => $data[3][0],
        'name' => $data[3][1],
        'images' => $images
    ];
    echo json_encode($resdata);
   }
