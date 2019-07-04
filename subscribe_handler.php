<?php
require_once('inc/common.inc.php');
use Telegram\Bot\Api;

  
$telegram = new Api(API_TOKEN); //Устанавливаем токен, полученный у BotFather

function sendSubscribe($telegram, $chat_id, $city)
{
    
    $url = API_URL . urlencode($city) . "&days=2&lang=ru";
    $day = 2;
    $response = getForecast($city, $day);


    if (!strpos($response, "error"))
    {
        $decodeResponse = json_decode($response, true); 
        $forecast= parseForecast($decodeResponse);
        $reply =  $forecast['location']['city'] . ", " . $forecast['location']['country'] . "\n";

      
        $reply .= $forecast['forecast'][1]['date'] . ": " . $forecast['forecast'][1]['condition'] . ". \nМинимальная температура " 
        . $forecast['forecast'][1]['min_temp'] . "\nМаксимальная температура " . $forecast['forecast'][1]['max_temp'];
        $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply]);
    
    }
}

function runSubscribe($telegram)
{
    $users = getSubscribeList();
    foreach ($users as $user) {
        
        $chat_id = $user['id_chat'];
        $favoriteCity = $user['city'];
        //error_log($chat_id);
        sendSubscribe($telegram, $chat_id, $favoriteCity);
    }
}

runSubscribe($telegram);


