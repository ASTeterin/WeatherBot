<?php
	include('vendor/autoload.php'); //Подключаем библиотеку
	use Telegram\Bot\Api;
	$telegram = new Api('832044822:AAEb48OoiZoxf4YTrS3T3-Z1GWcugj_VMcE'); //Устанавливаем токен, полученный у BotFather
	$result = $telegram -> getWebhookUpdates(); //Передаем в переменную $result полную информацию о сообщении пользователя
	$text = $result["message"]["text"]; //Текст сообщения
	$chat_id = $result["message"]["chat"]["id"]; //Уникальный идентификатор пользователя
	$name = $result["message"]["from"]["username"]; //Юзернейм пользователя
	$keyboard = [["/sayhello"],["/help"]]; //Клавиатура
	
	if($text){
		/*if ($text == "/sayhello") {*/
			$telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $name ]);
		/*}*/
	}else{
		$telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => "Отправьте текстовое сообщение." ]);
	}
?>
