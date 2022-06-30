<?php
require_once __DIR__ . '/../vendor/autoload.php';
include_once __DIR__ . '/../utilities/credentials.php';
if (isset($mongoLink)) {
    $client = new MongoDB\Client($mongoLink);
    $mongodb = $client->progettoBasi;
}else{
    exit;
}