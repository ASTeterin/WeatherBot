<?php 

include('vendor/autoload.php'); //���������� ����������
use Telegram\Bot\Api; 

$telegram = new Api('832044822:AAEb48OoiZoxf4YTrS3T3-Z1GWcugj_VMcE'); //������������� �����, ���������� � BotFather
$result = $telegram -> getWebhookUpdates(); //�������� � ���������� $result ������ ���������� � ��������� ������������
echo $result;    
$text = $result["message"]["text"]; //����� ���������
$chat_id = $result["message"]["chat"]["id"]; //���������� ������������� ������������
$name = $result["message"]["from"]["username"]; //�������� ������������
$keyboard = [["��������� ������"],["��������"],["�����"]]; //����������

if($name) {
	$reply = "����� ���������� � ����!";
        $reply_markup = $telegram->replyKeyboardMarkup([ 'keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false ]);
	$telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup ]);
}
