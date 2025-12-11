<?php
header("Content-Type: application/json");

$apiKey = "AIzaSyAMy4ncN8WQC-F4eVA7okwXhV49SuB0kfg";
$url = "https://generativelanguage.googleapis.com/v1beta/models?key=" . $apiKey;

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true
]);

$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo json_encode(["error" => curl_error($ch)]);
    exit;
}

curl_close($ch);

echo $response;
