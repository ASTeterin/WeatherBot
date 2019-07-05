<?php


const BASE_KEYBOARD = "/help"; 
const HELP_INFO = "Бот позволяет посмотреть прогноз погоды в любых населенных пунктах." .
" Для вывода информации введите <название населенного пункта> и <количество дней>.\n";
const MIN_TEMPERATURE = ". \nМинимальная температура ";
const MAX_TEMPERATURE = "\nМаксимальная температура ";
const CITY = "Населенный пункт ";
const ADD =  " добавлен";

const START_COMMAND = "/start";
const HELP_COMMAND = "/help";
const ADD_COMMAND = "Добавить в избранное";
const SUBSCRIBE_COMMAND = "/subscribe";
const UNSUBSCRIBE_COMMAND = "/unsubscribe";




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
    $reply = HELP_INFO;
    $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply ]);
}

function showForecast($telegram, $chat_id, $text, &$keyboard)
{
    $city = trim(mb_eregi_replace('[0-9]', '', $text));
    $days = ($city == $text)? 1:  preg_replace("/[^,.0-9]/", '', $text);
    $days = ($days > 10)? 10 : $days; 
    
    
    $response = getForecast($city, $days);
    

    if (!strpos($response, "error"))
    {
        if ($city != getLastRequestedCity($chat_id)) {
            addLastRequestedCity($city, $chat_id);
            $keyboard[] = ["/add " . $city];
        }
        $decodeResponse = json_decode($response, true); 
        $weather = parseForecast($decodeResponse);
        $reply =  $weather['location']['city'] . ", " . $weather['location']['country'];
        $reply_markup = $telegram->replyKeyboardMarkup([ 'keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false ]);
        $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup ]);

        foreach ($weather['forecast'] as $dailyForecast) {
            $reply = $dailyForecast['date'] . ": " . $dailyForecast['condition'] . MIN_TEMPERATURE . $dailyForecast['min_temp'] . MAX_TEMPERATURE . $dailyForecast['max_temp'];
            $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply]);
        }
    }else{
        $reply = CITY . '<b>' . $city . '</b>' . " не найден";
        $telegram->sendMessage([ 'chat_id' => $chat_id, 'parse_mode' => 'HTML', 'disable_web_page_preview' => true, 'text' => $reply ]);
    }  
}

function addFavoriteCity($telegram, $chat_id, $city)
{
    saveFavoriteCity($city, $chat_id);
    removeSubscribedStatus($chat_id);
    $keyboard = [[BASE_KEYBOARD]];
    initKeyboard($keyboard, $chat_id);
    $reply_markup = $telegram->replyKeyboardMarkup([ 'keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false ]);
    $reply = CITY . $city .  ADD;
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
    addFavoriteCity($telegram, $chat_id, $city );
}

function initKeyboard(&$keyboard, $chat_id)
{
    $favoriteCity = getFavoriteCity($chat_id);
    if (!is_null($favoriteCity)) {
        $isSubscribed = getSubscribedStatus($chat_id);
        $keyboard[] = [$favoriteCity];
        $keyboard[] = ($isSubscribed == 1)? ["/unsubscribe"]: ["/subscribe"];
    }  
}

function subscribeOnFavoriteCity($telegram, $chat_id)
{
    setSubscribedStatus($chat_id);
    $keyboard = [[BASE_KEYBOARD]];
    initKeyboard($keyboard, $chat_id);
    $reply_markup = $telegram->replyKeyboardMarkup([ 'keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false ]);
    $reply = "Подписка оформлена";
    $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup ]);   
}

function unsubscribeOnFavoriteCity($telegram, $chat_id)
{
    removeSubscribedStatus($chat_id);
    $keyboard = [[BASE_KEYBOARD]];
    initKeyboard($keyboard, $chat_id);
    $reply_markup = $telegram->replyKeyboardMarkup([ 'keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false ]);
    $reply = "Подписка отменена";
    $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup ]);   
}

function handleComand

function startBot($telegram, $result)
{
    $text = mb_strtolower($result["message"]["text"]); //Текст сообщения
    $chat_id = $result["message"]["chat"]["id"]; //Уникальный идентификатор пользователя
    $name = $result["message"]["from"]["username"]; //Юзернейм пользователя
    $keyboard = [[BASE_KEYBOARD]];
    initKeyboard($keyboard, $chat_id);
    if ($text) {
        if ($text == START_COMMAND) {
            startComandHandler($telegram, $chat_id, $keyboard, $name);
            
        } elseif ($text == HELP_COMMAND) {
            helpComandHandler($telegram, $chat_id);
        } elseif ($text == ADD_COMMAND) {
            addFavoriteCityFromDB($telegram, $chat_id);
        } elseif ($text == SUBSCRIBE_COMMAND) {
            subscribeOnFavoriteCity($telegram, $chat_id);    
        } elseif ($text == SUBSCRIBE_COMMAND) {
            unsubscribeOnFavoriteCity($telegram, $chat_id);
        } else{
            showForecast($telegram, $chat_id, $text, $keyboard);
        }
    }    
}
