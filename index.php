<?php
    

    include('vendor/autoload.php'); //Подключаем библиотеку
    require_once('inc/common.inc.php');
    use Telegram\Bot\Api;
    $telegram = new Api('832044822:AAEb48OoiZoxf4YTrS3T3-Z1GWcugj_VMcE'); //Устанавливаем токен, полученный у BotFather
    $result = $telegram -> getWebhookUpdates(); //Передаем в переменную $result полную информацию о сообщении пользователя
    
    $text = $result["message"]["text"]; //Текст сообщения
    $chat_id = $result["message"]["chat"]["id"]; //Уникальный идентификатор пользователя
    $name = $result["message"]["from"]["username"]; //Юзернейм пользователя
    
    function getDataFromApi($url): ?string
    {
        $ch = curl_init();
        // установка URL и других необходимых параметров
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // загрузка страницы и выдача её браузеру
        $data = curl_exec($ch);
        // завершение сеанса и освобождение ресурсов
        curl_close($ch);
        return $data;
    }

    $keyboard = [["/sayhello"],["/start"]]; 
    if($text){
        if ($text == "/start") {
            $reply = "Погода в городах мира!";
            $reply_markup = $telegram->replyKeyboardMarkup([ 'keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false ]);
            $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup ]);
        }elseif ($text == "/sayhello") {
            $reply = "Здравствуйте, ";
            if (empty($name)) {
                $reply .= "незнакомец";
            } else {
                $reply .= $name;
            }
            $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply ]);
        }else{
            //$reply = "неизвестная комманда " .$text ;
            $url = "http://api.apixu.com/v1/forecast.json?key=a063d1eac8054ab392f195555192506&q=" . $text . "&days=1&lang=ru";
//
            $str = getDataFromApi($url);
	$forecast = explode("\"date\":\"", $str);
  
//print_r($forecast);

	$weather = parseForecast($forecast);
	//$reply = "";
	//foreach ($weather as $dailyWeather) {
        for ($i = 1; i < count($weather); $i++)
            $reaply = ""; 
            //$reply = $dailyWeather['rain'] . " минимальная температура " . $dailyWeather['max_temp'] . " Максимальная температура" . $dailyWeather['max_temp'];
        $reply = $weather[$i]['rain'] . " минимальная температура " . $weather[$i]['max_temp'] . " Максимальная температура" . $weather[$i]['max_temp'];
               
        $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply ]);
		
        }
    
        }
    }
