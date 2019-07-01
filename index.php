<?php

    include('vendor/autoload.php'); //Подключаем библиотеку
    require_once('inc/common.inc.php');
    use Telegram\Bot\Api;
    
    $telegram = new Api(API_TOKEN); //Устанавливаем токен, полученный у BotFather
    $result = $telegram -> getWebhookUpdates(); //Передаем в переменную $result полную информацию о сообщении пользователя    
   
    botWorking($telegram, $result);

    
   
 