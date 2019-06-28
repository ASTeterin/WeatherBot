<?php
    include('vendor/autoload.php'); //Подключаем библиотеку
    use Telegram\Bot\Api;
    $telegram = new Api('832044822:AAEb48OoiZoxf4YTrS3T3-Z1GWcugj_VMcE'); //Устанавливаем токен, полученный у BotFather
    $result = $telegram -> getWebhookUpdates(); //Передаем в переменную $result полную информацию о сообщении пользователя
    
    $text = $result["message"]["text"]; //Текст сообщения
    $chat_id = $result["message"]["chat"]["id"]; //Уникальный идентификатор пользователя
    $name = $result["message"]["from"]["username"]; //Юзернейм пользователя


    $keyboard = [["/sayhello"],["/help"]]; //??????????
    if($text){
        if ($text == "/start") {
            
            $reply = "Hello!";
            $reply_markup = $telegram->replyKeyboardMarkup([ 'keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false ]);
            $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup ]);
        }elseif ($text == "/help") {
           
            $reply = "Info.";
            $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply ]);
        }elseif ($text == "/sayhello") {
  
            $reply = "Hello, " . $name;
            $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply ]);
        }else{
            $reply = "?? ??????? \"<b>".$text."</b>\" ?????? ?? ???????.";
        }
    }
?>
