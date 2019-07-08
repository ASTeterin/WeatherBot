<?php
require_once('inc/common.inc.php');
use Telegram\Bot\Api;

  
$telegram = new Api(API_TOKEN); 

function sendSubscribe($telegram, $chatId, $city)
{
    $url = API_URL . urlencode($city) . "&days=2&lang=ru";
    $day = 2;
    $response = getForecast($city, $day);
    $decodeResponse = json_decode($response, true); 
    $forecast= parseForecast($decodeResponse);

    if ($forecast)
    { 
        $reply =  $forecast['location']['city'] . ", " . $forecast['location']['country'] . "\n";
        $reply .= $forecast['forecast'][1]['date'] . ": " . $forecast['forecast'][1]['condition'] . ". \nМинимальная температура " 
        . $forecast['forecast'][1]['min_temp'] . "\nМаксимальная температура " . $forecast['forecast'][1]['max_temp'];
        $telegram->sendMessage([ 'chat_id' => $chatId, 'text' => $reply]);
    }
}

function runSubscribe($telegram)
{
    $users = getSubscribeList();
    foreach ($users as $user) {
        $chatId = $user['id_chat'];
        $favoriteCity = $user['name'];
        sendSubscribe($telegram, $chatId, $favoriteCity);
    }
}

runSubscribe($telegram);


