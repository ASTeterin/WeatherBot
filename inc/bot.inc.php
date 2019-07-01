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
    //$reply = "Погода в городах мира!";
    $reply_markup = $telegram->replyKeyboardMarkup([ 'keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false ]);
    $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup ]);
}

function helpBot($telegram, $chat_id)
{
    $reply = "Бот позволяет посмотреть прогноз погоды в любых населенных пунктах. "
                . "Для вывода информации введите название населенного пункта и количество дней, на которые необходим прогноз " ;
    $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply ]);
}

function botWorking($telegram, $chat_id, $text, $name, $keyboard)
{
    if($text){
        if ($text == "/start") {
            startBot($telegram, $chat_id, $keyboard, $name);
            /*$reply = "Погода в городах мира!";
            $reply_markup = $telegram->replyKeyboardMarkup([ 'keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false ]);
            $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup ]);*/
        }elseif ($text == "/help") {
            /*$reply = "Бот позволяет посмотреть прогноз погоды в любых населенных пунктах. "
                    . "Для вывода информации введите название населенного пункта и количество дней, на которые необходим прогноз " ;
            $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply ]);*/
            helpBot($telegram, $chat_id);
        }elseif ($text == "/sayhello") {
            $reply = "Здравствуйте, ";
            if (empty($name)) {
                $reply .= "незнакомец";
            } else {
                $reply .= $name;
            }
            $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply ]);
        }else{
            list($city, $days) = explode(" ", removeExtraSymbols($text, " ")) ;
            $url = "http://api.apixu.com/v1/forecast.json?key=a063d1eac8054ab392f195555192506&q=" . urlencode($city) . "&days=" . $days . "&lang=ru";
            $str = getDataFromApi($url);
            if (!strpos($str, "error"))
            {
                $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $city ]);
                $forecast = explode("\"date\":\"", $str);

                $weather = parseForecast($forecast);
                for ($i = 1; $i < count($weather); $i++) {
                    //$reaply = ""; 
                    $reply = $weather[$i]['date'] . " " . $weather[$i]['rain'] . ". Минимальная температура " . $weather[$i]['min_temp'] . ", максимальная температура " . $weather[$i]['max_temp'];
                    $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply ]);
                }
            }else{
                $reply = "Населенный пункт " . '<b>' . $city . '</b>' . " не найден";
                $telegram->sendMessage([ 'chat_id' => $chat_id, 'parse_mode' => 'HTML', 'disable_web_page_preview' => true, 'text' => $reply ]);
            }
        }
    }    
}
