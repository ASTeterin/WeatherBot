<?php

const HELP_INFO = "Бот позволяет посмотреть прогноз погоды в любых населенных пунктах." .
" Для вывода информации введите <название населенного пункта> и <количество дней>.\n";
const MIN_TEMPERATURE = ". \nМинимальная температура ";
const MAX_TEMPERATURE = "\nМаксимальная температура ";
const CITY = "Населенный пункт ";
const ADD =  " добавлен";
const SUBSCRIBE = 'Подписка оформлена';
const UNSUBSCRIBE = '"Подписка отменена';
const HELLO = 'Здравствуйте, ';
const STRANGER = 'незнакомец';
const WELCOME_LINE = '\nВы находитсь в боте Погода в городах мира!';

const BASE_KEYBOARD = "/help"; 
const START_COMMAND = "/start";
const HELP_COMMAND = "/help";
const ADD_COMMAND = "добавить в избранное";
const SUBSCRIBE_COMMAND = "/subscribe";
const UNSUBSCRIBE_COMMAND = "/unsubscribe";

function startComandHandler($telegram, $chatId, $keyboard, $name)
{
    $reply = HELLO;
        if (empty($name)) {
            $reply .= STRANGER;
        } else {
            $reply .= $name;
        }
    $reply .= WELCOME_LINE;
    $reply_markup = $telegram->replyKeyboardMarkup(['keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false]);
    $telegram->sendMessage(['chat_id' => $chatId, 'text' => $reply, 'reply_markup' => $reply_markup]);
    
    addNewUser($chatId, $name);
}

function helpComandHandler($telegram, $chatId)
{
    $reply = HELP_INFO;
    $telegram->sendMessage([ 'chat_id' => $chatId, 'text' => $reply ]);
}

function printForecast($telegram, $chatId, $forecast, $keyboard)
{
    $reply =  $forecast['location']['city'] . ", " . $forecast['location']['country'];
    $reply_markup = $telegram->replyKeyboardMarkup(['keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false]);
    $telegram->sendMessage(['chat_id' => $chatId, 'text' => $reply, 'reply_markup' => $reply_markup]);

    foreach ($forecast['forecast'] as $dailyForecast) {
        $reply = $dailyForecast['date'] . ": " . $dailyForecast['condition'] . MIN_TEMPERATURE . $dailyForecast['min_temp'] . MAX_TEMPERATURE . $dailyForecast['max_temp'];
        $telegram->sendMessage(['chat_id' => $chatId, 'text' => $reply]);
    }
}

function parseForecast($response)
{
    $data = array();
    $location = array();
    $forecast = array();
    if (isset($response['error']['code'])) {
        return null;
    }
    $city = (isset($response['location']['name'])) ? $response['location']['name'] : null;
    $country = (isset($response['location']['country'])) ? $response['location']['country'] : null;
    $location['city'] = $city;
    $location['country'] = $country; 
    foreach($response['forecast']['forecastday'] as $dayForecast) {
        $daily = array();
        $daily['date'] = $dayForecast['date'];
        $daily['max_temp'] = $dayForecast['day']['maxtemp_c'];
        $daily['min_temp'] = $dayForecast['day']['mintemp_c'];
        $daily['condition'] = $dayForecast['day']['condition']['text'];
        $daily['icon'] = $dayForecast['day']['condition']['icon'];
        $forecast[] = $daily;
    }
    $data['location'] = $location;
    $data['forecast'] = $forecast;
    return $data;
}

function showForecast($telegram, $chatId, $text, $keyboard)
{
    $city = trim(mb_eregi_replace('[0-9]', '', $text));
    $days = ($city == $text)? 1:  preg_replace("/[^,.0-9]/", '', $text);
    $days = ($days > 10)? 10 : $days; 
    
    $response = getForecast($city, $days);
    $decodeResponse = json_decode($response, true); 
    $forecast = parseForecast($decodeResponse);

    if ($forecast)
    {
        if ($city != getLastRequestedCity($chatId)) {
            saveLastRequestedCity($city, $chatId);
            $keyboard[] = [ADD_COMMAND];
        } 
        printForecast($telegram, $chatId, $forecast, $keyboard);   
    }else{
        $reply = CITY . '<b>' . $city . '</b>' . " не найден";
        $telegram->sendMessage(['chat_id' => $chatId, 'parse_mode' => 'HTML', 'disable_web_page_preview' => true, 'text' => $reply]);
    }  
}

function addFavoriteCity($telegram, $chatId)
{
    $city = getLastRequestedCity($chatId);
    saveFavoriteCity($city, $chatId);
    removeSubscribedStatus($chatId);
    $keyboard = getKeyboard($chatId);
    $reply_markup = $telegram->replyKeyboardMarkup(['keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false]);
    $reply = CITY . $city .  ADD;
    $telegram->sendMessage(['chat_id' => $chatId, 'parse_mode' => 'HTML', 'disable_web_page_preview' => true, 'text' => $reply, 'reply_markup' => $reply_markup]);
}

function getKeyboard($chatId)
{
    $keyboard = [[BASE_KEYBOARD]];
    $favoriteCity = getFavoriteCity($chatId);
    if (!is_null($favoriteCity)) {
        $isSubscribed = getSubscribedStatus($chatId);
        $keyboard[] = [$favoriteCity];
        $keyboard[] = ($isSubscribed == 1)? ["/unsubscribe"]: ["/subscribe"];
    }
    return $keyboard;  
}

function switchSubscription($telegram, $chatId)
{
    if (getSubscribedStatus($chatId)) {
        removeSubscribedStatus($chatId);
        $reply = UNSUBSCRIBE;  
    } else {
        setSubscribedStatus($chatId);
        $reply = SUBSCRIBE;
    }
    $keyboard = getKeyboard($chatId);
    $reply_markup = $telegram->replyKeyboardMarkup(['keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false]);
    $telegram->sendMessage(['chat_id' => $chatId, 'text' => $reply, 'reply_markup' => $reply_markup]);   
}

function handleComamnd($command, $telegram, $chatId, $keyboard, $name)
{
    switch ($command) {
        case START_COMMAND:
            startComandHandler($telegram, $chatId, $keyboard, $name);
            break;
        case HELP_COMMAND:
            helpComandHandler($telegram, $chatId);
            break;
        case ADD_COMMAND:
            addFavoriteCity($telegram, $chatId);
            break;
        case SUBSCRIBE_COMMAND:
        case UNSUBSCRIBE_COMMAND: 
            switchSubscription($telegram, $chatId);
            break;
        default:
            showForecast($telegram, $chatId, $command, $keyboard);
    }
}

function startBot($telegram, $result)
{
    /*$text = (isset($result["message"]["text"])) ? mb_strtolower($result["message"]["text"]) : null; //Текст сообщения
    $chatId = (isset($result["message"]["chat"]["id"])) ? $result["message"]["chat"]["id"] : null; //Уникальный идентификатор пользователя
    $name =  (isset($result["message"]["from"]["username"])) ? $result["message"]["from"]["username"] : null; //Юзернейм пользователя*/
    $text = mb_strtolower($result["message"]["text"]); //Текст сообщения
    $chatId = $result["message"]["chat"]["id"]; //Уникальный идентификатор пользователя
    $name = $result["message"]["from"]["username"];
    
    if ($chatId) {
        addNewUser($chatId, $name);
    }
    $keyboard = getKeyboard($chatId);
    if ($text) {
        handleComamnd($text, $telegram, $chatId, $keyboard, $name);
    }    
}
