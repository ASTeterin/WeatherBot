<?php
    include('vendor/autoload.php'); //������砥� ������⥪�
    use Telegram\Bot\Api;
    $telegram = new Api('832044822:AAEb48OoiZoxf4YTrS3T3-Z1GWcugj_VMcE'); //��⠭�������� ⮪��, ����祭�� � BotFather
    $result = $telegram -> getWebhookUpdates(); //��।��� � ��६����� $result ������ ���ଠ�� � ᮮ�饭�� ���짮��⥫�
    $text = $result["message"]["text"]; //����� ᮮ�饭��
    $chat_id = $result["message"]["chat"]["id"]; //�������� �����䨪��� ���짮��⥫�
    $name = $result["message"]["from"]["username"]; //���୥�� ���짮��⥫�
    $keyboard = [["/sayhello"],["/help"]]; //���������
    if ($text)
    {
        switch ($text) {
            case '/start': 
                $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' =>  "����� ���������� � ���"]);
                break;
            case '/sayhello': {
                $response = '������, ';
                if (!empty($name)) {
                    $response .= $name;
                } else {
                    $response .= '����������';
                }
                $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' =>  $response]);
            }
            break;
            
        }
      
    }else {
        $telegram->sendMessage([ 'chat_id' => $chat_id, 'text' => "��ࠢ�� ⥪�⮢�� ᮮ�饭��." ]);
    }
    
    
