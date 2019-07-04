<?php
require_once('inc/common.inc.php');
use Telegram\Bot\Api;

$url = "";
  
$telegram = new Api(API_TOKEN); //Устанавливаем токен, полученный у BotFather

function sendSubscribe($telegram, $chat_id, $city)
{
    global $url; 
    $url = API_URL . urlencode($city) . "&days=2&lang=ru";
    $response = getForecast();


    if (!strpos($response, "error"))
    {
        $decodeResponse = json_decode($response, true); 
        $forecast= parseForecast($decodeResponse);
        $reply =  $forecast['location']['city'] . ", " . $forecast['location']['country'];

      
        $reply .= $forecast[1]['date'] . ": " . $forecast[1]['condition'] . ". \nМинимальная температура " . $forecast[1]['min_temp'] . "\nМаксимальная температура " . $forecast[1]['max_temp'];
        $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply]);
    



    /*if (!strpos($response, "error"))
    {
        $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $city ]);
        $forecast = explode("\"date\":\"", $response);

        $weather = parseForecast($forecast);
        for ($i = 2; $i < count($weather); $i++) {
            $reply = $weather[$i]['date'] . " " . $weather[$i]['rain'] . ". \nМинимальная температура " . $weather[$i]['min_temp'] . "\nМаксимальная температура " . $weather[$i]['max_temp'];
            $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply ]);
        }*/
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


