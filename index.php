<?php
    include('vendor/autoload.php'); //���������� ����������
    use Telegram\Bot\Api;
    $telegram = new Api('832044822:AAEb48OoiZoxf4YTrS3T3-Z1GWcugj_VMcE'); //������������� �����, ���������� � BotFather
    $result = $telegram -> getWebhookUpdates(); //�������� � ���������� $result ������ ���������� � ��������� ������������
    print_r($result);
    $text = $result["message"]["text"]; //����� ���������
    $chat_id = $result["message"]["chat"]["id"]; //���������� ������������� ������������
    $name = $result["message"]["from"]["username"]; //�������� ������������
    if($text == "/sayhello") {
	 
        $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => "Hello World" ]);
       
}else{
    $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => "��������� ��������� ���������." ]);
}
?>