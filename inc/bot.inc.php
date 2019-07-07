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

function startComandHandler($telegram, $chatId, $keyboard, $name)
{
    $reply = "Здравствуйте, ";
        if (empty($name)) {
            $reply .= "незнакомец";
        } else {
            $reply .= $name;
        }
    $reply .= "\nВы находитсь в боте Погода в городах мира!";
    $reply_markup = $telegram->replyKeyboardMarkup([ 'keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false ]);
    $telegram->sendMessage([ 'chat_id' => $chatId, 'text' => $reply, 'reply_markup' => $reply_markup ]);
    
    addNewUser($chatId, $name);
}

function helpComandHandler($telegram, $chatId)
{
    $reply = HELP_INFO;
    $telegram->sendMessage([ 'chat_id' => $chatId, 'text' => $reply ]);
}

function showForecast($telegram, $chatId, $text, &$keyboard)
{
    $city = trim(mb_eregi_replace('[0-9]', '', $text));
    $days = ($city == $text)? 1:  preg_replace("/[^,.0-9]/", '', $text);
    $days = ($days > 10)? 10 : $days; 
    
    $response = getForecast($city, $days);

    if (!strpos($response, "error"))
    {
        if ($city != getLastRequestedCity($chatId)) {
            addLastRequestedCity($city, $chatId);
            $keyboard[] = ["/add " . $city];
        }
        $decodeResponse = json_decode($response, true); 
        $weather = parseForecast($decodeResponse);
        $reply =  $weather['location']['city'] . ", " . $weather['location']['country'];
        $reply_markup = $telegram->replyKeyboardMarkup([ 'keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false ]);
        $telegram->sendMessage([ 'chat_id' => $chatId, 'text' => $reply, 'reply_markup' => $reply_markup ]);

        foreach ($weather['forecast'] as $dailyForecast) {
            $reply = $dailyForecast['date'] . ": " . $dailyForecast['condition'] . MIN_TEMPERATURE . $dailyForecast['min_temp'] . MAX_TEMPERATURE . $dailyForecast['max_temp'];
            $telegram->sendMessage([ 'chat_id' => $chatId, 'text' => $reply]);
        }
    }else{
        $reply = CITY . '<b>' . $city . '</b>' . " не найден";
        $telegram->sendMessage([ 'chat_id' => $chatId, 'parse_mode' => 'HTML', 'disable_web_page_preview' => true, 'text' => $reply ]);
    }  
}

function addFavoriteCity($telegram, $chatId)
{
    $city = getLastRequestedCity($chatId);
    saveFavoriteCity($city, $chatId);
    removeSubscribedStatus($chatId);
    $keyboard = [[BASE_KEYBOARD]];
    initKeyboard($keyboard, $chatId);
    $reply_markup = $telegram->replyKeyboardMarkup([ 'keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false ]);
    $reply = CITY . $city .  ADD;
    $telegram->sendMessage([ 'chat_id' => $chatId, 'parse_mode' => 'HTML', 'disable_web_page_preview' => true, 'text' => $reply, 'reply_markup' => $reply_markup ]);
}


function initKeyboard(&$keyboard, $chatId)
{
    $favoriteCity = getFavoriteCity($chatId);
    if (!is_null($favoriteCity)) {
        $isSubscribed = getSubscribedStatus($chatId);
        $keyboard[] = [$favoriteCity];
        $keyboard[] = ($isSubscribed == 1)? ["/unsubscribe"]: ["/subscribe"];
    }  
}

function subscribeOnFavoriteCity($telegram, $chatId)
{
    setSubscribedStatus($chatId);
    $keyboard = [[BASE_KEYBOARD]];
    initKeyboard($keyboard, $chatId);
    $reply_markup = $telegram->replyKeyboardMarkup([ 'keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false ]);
    $reply = "Подписка оформлена";
    $telegram->sendMessage([ 'chat_id' => $chatId, 'text' => $reply, 'reply_markup' => $reply_markup ]);   
}

function unsubscribeOnFavoriteCity($telegram, $chatId)
{
    removeSubscribedStatus($chatId);
    $keyboard = [[BASE_KEYBOARD]];
    initKeyboard($keyboard, $chatId);
    $reply_markup = $telegram->replyKeyboardMarkup([ 'keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false ]);
    $reply = "Подписка отменена";
    $telegram->sendMessage([ 'chat_id' => $chatId, 'text' => $reply, 'reply_markup' => $reply_markup ]);   
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
            subscribeOnFavoriteCity($telegram, $chatId);
            break;
        case UNSUBSCRIBE_COMMAND:
            unsubscribeOnFavoriteCity($telegram, $chatId);
            break;
        default:
            showForecast($telegram, $chatId, $command, $keyboard);
    }
}

function startBot($telegram, $result)
{
    $text = mb_strtolower($result["message"]["text"]); //Текст сообщения
    $chatId = $result["message"]["chat"]["id"]; //Уникальный идентификатор пользователя
    $name = $result["message"]["from"]["username"]; //Юзернейм пользователя
    $keyboard = [[BASE_KEYBOARD]];
    initKeyboard($keyboard, $chatId);
    if ($text) {
        handleComamnd($text, $telegram, $chatId, $keyboard, $name);
    }    
}
