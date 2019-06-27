<?php
	include('vendor/autoload.php'); //Подключаем библиотеку
    use Telegram\Bot\Api;
    $telegram = new Api('832044822:AAEb48OoiZoxf4YTrS3T3-Z1GWcugj_VMcE'); //Устанавливаем токен, полученный у BotFather
    $result = $telegram -> getWebhookUpdates(); //Передаем в переменную $result полную информацию о сообщении пользователя
    $text = $result["message"]["text"]; //Текст сообщения
    $chat_id = $result["message"]["chat"]["id"]; //Уникальный идентификатор пользователя
    $name = $result["message"]["from"]["username"]; //Юзернейм пользователя
    if($text)
    {
	 if ($text == '/start') {
            if (strlen($name) == 0) {
                $reply = 'Добро пожаловать, Незнакомец!';
            }
            else {
                $reply = 'Добро пожаловать, '.$name.'!';
            }
            $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply]);
       
}else{
    $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => "Отправьте текстовое сообщение." ]);
}
?>