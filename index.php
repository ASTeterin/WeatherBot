<?php
include('vendor/autoload.php'); //���������� ����������
use Telegram\Bot\Api;
$telegram = new Api('802656277:AAHoBZsE6BrOwU3M8WRgWCW1q36FJls7h3c'); //������������� �����, ���������� � BotFather
$result = $telegram -> getWebhookUpdates(); //�������� � ���������� $result ������ ���������� � ��������� ������������
$text = $result["message"]["text"]; //����� ���������
$chat_id = $result["message"]["chat"]["id"]; //���������� ������������� ������������
$name = $result["message"]["from"]["username"]; //�������� ������������
$keyboard = [["/sayhello"],["/help"]]; //����������
if($text){
    if ($text == "/start") {
        $reply = "����� ���������� � ����!";
        $reply_markup = $telegram->replyKeyboardMarkup([ 'keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false ]);
        $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup ]);
    }elseif ($text == "/help") {
        $reply = "���������� � �������.";
        $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply ]);
    }elseif ($text == "/sayhello") {
        $reply = "������, " . $name;
        $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply ]);
    }else{
        $reply = "�� ������� \"<b>".$text."</b>\" ������ �� �������.";
        $telegram->sendMessage([ 'chat_id' => $chat_id, 'parse_mode'=> 'HTML', 'text' => $reply ]);
    }
}else{
    $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => "��������� ��������� ���������." ]);
}
?>