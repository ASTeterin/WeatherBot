<?php
    include('vendor/autoload.php'); //���������� ����������
    use Telegram\Bot\Api;
    $telegram = new Api('832044822:AAEb48OoiZoxf4YTrS3T3-Z1GWcugj_VMcE'); //������������� �����, ���������� � BotFather
    $result = $telegram -> getWebhookUpdates(); //�������� � ���������� $result ������ ���������� � ��������� ������������
    $text = $result["message"]["text"]; //����� ���������
    $chat_id = $result["message"]["chat"]["id"]; //���������� ������������� ������������
    $name = $result["message"]["from"]["username"]; //�������� ������������
    if($text == "/sayhello") {
        $response = "������, ";
        if (!empty($name)) {
            $response .= $name;
        } else {
            $response .= "����������";
        }
        $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' =>  $response]);
       
    }else {
        $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => "��������� ��������� ���������." ]);
    }
