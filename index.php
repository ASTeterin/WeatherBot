<?php
	include('vendor/autoload.php'); //���������� ����������
    use Telegram\Bot\Api;
    $telegram = new Api('885752742:AAF63rND57OidzVAJ3ReDp7qGkX7oVaunBY'); //������������� �����, ���������� � BotFather
    $result = $telegram -> getWebhookUpdates(); //�������� � ���������� $result ������ ���������� � ��������� ������������
    $text = $result["message"]["text"]; //����� ���������
    $chat_id = $result["message"]["chat"]["id"]; //���������� ������������� ������������
    $name = $result["message"]["from"]["username"]; //�������� ������������
    $keyboard = [["��������� ������"],["������������ ������"]]; //����������
    if($text)
    {
        if ($text == "/start")
        {
            if (strlen($name) != 0)
            {
                $reply = "����� ����������, ".$name."!";
            }
            else
            {
                $reply = "����� ����������, ����������";
            }
            $reply_markup = $telegram->replyKeyboardMarkup([ 'keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => false ]);
            $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => $reply, 'reply_markup' => $reply_markup ]);
        }
}