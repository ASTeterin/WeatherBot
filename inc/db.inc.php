<?php

$db = null;

function createDbConnection()
{ 
    global $db;
    $db = new MysqliDb ('b8rg15mwxwynuk9q.chr7pe7iynqr.eu-west-1.rds.amazonaws.com', 'dv8waucz07pdmu54', 'nvy4ervmlp4wpzhe', 'lq8s88g5if4zpb7e');
}
//$db = new MysqliDb ('b8rg15mwxwynuk9q.chr7pe7iynqr.eu-west-1.rds.amazonaws.com', 'dv8waucz07pdmu54', 'nvy4ervmlp4wpzhe', 'lq8s88g5if4zpb7e');

function addFavoriteCity($city, $chat_id)
{
    /*$db->where ("id_chat", $chat_id);
    $user = $db->getOne ("session");*/
    global $db;
    $user_id = findUser($chat_id);
    if(!is_null($user_id)) {
        $data = [
            "city" => $city,
        ];
        $db->where ("id_session", $user_id);
        $db->update ('session', $data);
    }
}

function findUser($chat_id)
{
    global $db;
    $db->where ("id_chat", $chat_id);
    $user = $db->getOne ("session");
    return $user['id_session']; 
}

function addNewUser($chat_id, $name)
{  
    global $db;
    if (is_null(findUser($chat_id))) {
        $data = [
            "name" => $name,
            "id_chat" => $chat_id,
        ];
        $id = $db->insert ('session', $data);
    }
}

function createUser($chat_id, $name)
{
    global $db;
    addNewUser($db, $chat_id, $name);
}