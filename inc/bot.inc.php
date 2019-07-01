<?php

const API_TOKEN = '832044822:AAEb48OoiZoxf4YTrS3T3-Z1GWcugj_VMcE';
const API_URL = "http://api.apixu.com/v1/forecast.json?key=a063d1eac8054ab392f195555192506&q=";

function initBot($token)
{
    $telegram = new Api($token); //Устанавливаем токен, полученный у BotFather
    return $telegram -> getWebhookUpdates(); //Передаем в переменную $result полную информацию о сообщении пользователя
}

function startBot($telegram, $chat_id, $keyboard, $name)
{
    $reply = "Здравствуйте, ";
        if (empty($name)) {
            $reply .= "незнакомец";
        } else {
            $reply .= $name;
        }
    $reply .= "\nВы находитсь в в боте Погода в городах мира!";
    $reply_markup = $telegram->replyKeyboardMarkup([ 'keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false ]);
    $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup ]);
    
    createUser($chat_id, $name);
}

function helpBot($telegram, $chat_id)
{
    $reply = "Бот позволяет посмотреть прогноз погоды в любых населенных пунктах.\n"
       . "Для вывода информации введите название населенного пункта и количество дней, на которые необходим прогноз " ;
    $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply ]);
}

function showForecast($telegram, $chat_id, $text)
{
    list($city, $days) = explode(" ", removeExtraSymbols($text, " ")) ;
    $url = API_URL . urlencode($city) . "&days=" . $days . "&lang=ru";
    $str = getDataFromApi($url);
    if (!strpos($str, "error"))
    {
        $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $city ]);
        $forecast = explode("\"date\":\"", $str);

        $weather = parseForecast($forecast);
        for ($i = 1; $i < count($weather); $i++) {
            $reply = $weather[$i]['date'] . " " . $weather[$i]['rain'] . ". \nМинимальная температура " . $weather[$i]['min_temp'] . "\nМаксимальная температура " . $weather[$i]['max_temp'];
            $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply ]);
        }
    }else{
        $reply = "Населенный пункт " . '<b>' . $city . '</b>' . " не найден";
        $telegram->sendMessage([ 'chat_id' => $chat_id, 'parse_mode' => 'HTML', 'disable_web_page_preview' => true, 'text' => $reply ]);
    }  
}

function botWorking($telegram, $result)
{
    $text = $result["message"]["text"]; //Текст сообщения
    $chat_id = $result["message"]["chat"]["id"]; //Уникальный идентификатор пользователя
    $name = $result["message"]["from"]["username"]; //Юзернейм пользователя
    $keyboard = [["/help"],["/start"]];
    
    if($text){
        if ($text == "/start") {
            startBot($telegram, $chat_id, $keyboard, $name);
        }elseif ($text == "/help") {
            helpBot($telegram, $chat_id);
        }else{
            showForecast($telegram, $chat_id, $text);
        }
    }    
}
