<?php
require_once('inc/common.inc.php');
use Telegram\Bot\Api;

const API_TOKEN = '832044822:AAEb48OoiZoxf4YTrS3T3-Z1GWcugj_VMcE';
const API_URL = "http://api.apixu.com/v1/forecast.json?key=a063d1eac8054ab392f195555192506&q=";
$url = "";
  
$telegram = new Api(API_TOKEN); //Устанавливаем токен, полученный у BotFather
//$result = $telegram -> getWebhookUpdates(); //Передаем в переменную $result полную информацию о сообщении пользователя  

function runSubscribe($telegram, $chat_id, $city)
{
    global $url; 
    $url = API_URL . urlencode($city) . "&days=2&lang=ru";
    $response = getForecast();
    if (!strpos($response, "error"))
    {
        $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $city ]);
        $forecast = explode("\"date\":\"", $response);

        $weather = parseForecast($forecast);
        for ($i = 2; $i < count($weather); $i++) {
            $reply = $weather[$i]['date'] . " " . $weather[$i]['rain'] . ". \nМинимальная температура " . $weather[$i]['min_temp'] . "\nМаксимальная температура " . $weather[$i]['max_temp'];
            $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply ]);
        }
    }
}

runSubscribe($telegram, "601359283", "Москва");

