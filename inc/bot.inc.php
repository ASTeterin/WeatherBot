<?php

const API_TOKEN = '832044822:AAEb48OoiZoxf4YTrS3T3-Z1GWcugj_VMcE';
const API_URL = "http://api.apixu.com/v1/forecast.json?key=a063d1eac8054ab392f195555192506&q=";
$url = "";

function initBot($token)
{
    $telegram = new Api($token); //Устанавливаем токен, полученный у BotFather
    return $telegram -> getWebhookUpdates(); //Передаем в переменную $result полную информацию о сообщении пользователя
}

function startComandHandler($telegram, $chat_id, $keyboard, $name)
{
    $reply = "Здравствуйте, ";
        if (empty($name)) {
            $reply .= "незнакомец";
        } else {
            $reply .= $name;
        }
    $reply .= "\nВы находитсь в боте Погода в городах мира!";
    $reply_markup = $telegram->replyKeyboardMarkup([ 'keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false ]);
    $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup ]);
    
    addNewUser($chat_id, $name);
}

function helpComandHandler($telegram, $chat_id)
{
    $reply = "Бот позволяет посмотреть прогноз погоды в любых населенных пунктах.\n"
       . "Для вывода информации введите <название населенного пункта> и <количество дней>.\n"
       . "Для добавления города в избранные введите комманду /add <название города>";
    $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply ]);
}

function showForecast($telegram, $chat_id, $text, &$keyboard)
{
    $city = trim(mb_eregi_replace('[0-9]', '', $text));
    $days = ($city == $text)? 1:  preg_replace("/[^,.0-9]/", '', $text);
    $days = ($days > 10)? 10 : $days; 
    
    global $url; 
    $url = API_URL . urlencode($city) . "&days=" . $days . "&lang=ru";
    $response = getForecast();
    if (!strpos($response, "error"))
    {
        if ($city != getLastRequestedCity($chat_id)) {
            addLastRequestedCity($city, $chat_id);
            $keyboard[] = ["/add " . $city];
        }
        $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $city ]);
        $forecast = explode("\"date\":\"", $response);
        
       
        
        $weather = parseForecast($forecast);
        for ($i = 1; $i < count($weather); $i++) {
            $reply = $weather[$i]['date'] . " " . $weather[$i]['rain'] . ". \nМинимальная температура " . $weather[$i]['min_temp'] . "\nМаксимальная температура " . $weather[$i]['max_temp'];
            $reply_markup = $telegram->replyKeyboardMarkup([ 'keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false ]);
            $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup ]);
            //$telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply ]);
        }
    }else{
        $reply = "Населенный пункт " . '<b>' . $city . '</b>' . " не найден";
        $telegram->sendMessage([ 'chat_id' => $chat_id, 'parse_mode' => 'HTML', 'disable_web_page_preview' => true, 'text' => $reply ]);
    }  
}

function addFavoriteCity($telegram, $chat_id, $city)
{
    saveFavoriteCity($city, $chat_id);
    $keyboard = [["/help"],["/start"]];
    initKeyboard($keyboard, $chat_id);
    $keyboard[] = ["/subscribe"];
    $reply_markup = $telegram->replyKeyboardMarkup([ 'keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false ]);
    $reply = "Населенный пункт " . '<b>' . $city . '</b>' . " добавлен";
    $telegram->sendMessage([ 'chat_id' => $chat_id, 'parse_mode' => 'HTML', 'disable_web_page_preview' => true, 'text' => $reply, 'reply_markup' => $reply_markup ]);
}

function addFavoriteCityFromDB($telegram, $chat_id)
{
    $city = getLastRequestedCity($chat_id);
    addFavoriteCity($telegram, $chat_id, $city );
}
     
     

function addFavoriteCityFromRequest($telegram, $chat_id, $text, $keyboard)
{
    $city = substr($text, 5);
    //$days = ($city == $text)? 1:  preg_replace("/[^,.0-9]/", '', $text);
    addFavoriteCity($telegram, $chat_id, $city );
}

function initKeyboard(&$keyboard, $chat_id)
{
    $favoriteCity = getFavoriteCity($chat_id);
    $isSubscribed = getSubscribedStatus($chat_id);
    if (!is_null($favoriteCity)) {
        $keyboard[] = [$favoriteCity];
    }
}

function startBot($telegram, $result)
{
    $text = mb_strtolower($result["message"]["text"]); //Текст сообщения
    $chat_id = $result["message"]["chat"]["id"]; //Уникальный идентификатор пользователя
    $name = $result["message"]["from"]["username"]; //Юзернейм пользователя
    $keyboard = [["/help"],["/start"]];
    initKeyboard($keyboard, $chat_id);
    if($text){
        
        if ($text == "/start") {
            startComandHandler($telegram, $chat_id, $keyboard, $name);
            
        }elseif ($text == "/help") {
            helpComandHandler($telegram, $chat_id);
        }elseif ($text == "Добавить в избранное") {
            addFavoriteCityFromDB($telegram, $chat_id);
        }elseif (getSubstBeforeBlank($text) == "/add") {
            addFavoriteCityFromRequest($telegram, $chat_id, $text, $keyboard);
        }else{
            showForecast($telegram, $chat_id, $text, $keyboard);
        }
    }    
}
