<?php

use Google\Auth\Credentials\ServiceAccountCredentials;
use Google\Auth\HttpHandler\HttpHandlerFactory;

require 'vendor/autoload.php';

$credential = new ServiceAccountCredentials(
    "https://www.googleapis.com/auth/firebase.messaging",
    json_decode(file_get_contents("key.json"), true)
);

$token = $credential->fetchAuthToken(HttpHandlerFactory::build());

$ch = curl_init("https://fcm.googleapis.com/v1/projects/multimarcasapp-2fa97/messages:send");

$message = array(
    "message" => array(
        "token" => "et6QN8nAVx9wOjrX4Vm0xN:APA91bFhfzCnHcvIHg2HQhyL025DI3jO2lsqT5YRhwyzic5Htcegxi0js_Ri2w18vYcUE_FQ2oikjl0-tyD82YVbbvcsbQ0bQ2-mjxBL6M67WwvMK22HVEFj9RGYdzVwFH9pjslQobpz",
        "webpush" => array(
            "fcm_options" => array(
                "link" => "https://google.com"
            )
        ),
        "data" => array(
            "title" => "Background Message Title",
            "body" => "Background message body"
        )
    )
);

curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Authorization: Bearer ' . $token['access_token']
));

curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($message));
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);

curl_close($ch);

echo $response;
?>
