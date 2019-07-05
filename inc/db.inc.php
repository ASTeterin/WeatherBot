<?php

const HOST = 'b8rg15mwxwynuk9q.chr7pe7iynqr.eu-west-1.rds.amazonaws.com';
const USER = 'dv8waucz07pdmu54';
const PASSWORD = 'nvy4ervmlp4wpzhe';
const DATABASE = 'lq8s88g5if4zpb7e';
$db = null;


function createDbConnection()
{ 
    global $db;
    $db = new MysqliDb (HOST, USER, PASSWORD, DATABASE);
}

function saveFavoriteCity($city, $chat_id)
{
    global $db;
    $user_id = findUser($chat_id);
    if(!is_null($user_id)) {
        $data = ["city" => $city];
        $db->where ("id_session", $user_id);
        $db->update ('session', $data);
    }
}

function getFavoriteCity($chat_id)
{
    global $db;
    $db->where ("id_chat", $chat_id);
    $user = $db->getOne ("session");
    return $user['city']; 
}

function getSubscribedStatus($chat_id)
{
    global $db;
    $db->where ("id_chat", $chat_id);
    $user = $db->getOne ("session");
    return $user['subscription']; 
}

function getLastRequestedCity($chat_id)
{
    global $db;
    $db->where ("id_chat", $chat_id);
    $user = $db->getOne ("session");
    return $user['last_request']; 
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


function addLastRequestedCity($city, $chat_id)
{
    global $db;
    $user_id = findUser($chat_id);
    if (!is_null($user_id)) {
        $data = [
            "last_request" => $city,
        ];
        $db->where("id_session", $user_id);
        $db->update('session', $data);
    }
}

function setSubscribedStatus($chat_id)
{
    global $db;
    $user_id = findUser($chat_id);
    if(!is_null($user_id)) {
        $data = [
            "subscription" => 1,
        ];
        $db->where ("id_session", $user_id);
        $db->update ('session', $data);
    }
}


function removeSubscribedStatus($chat_id)
{
    global $db;
    $user_id = findUser($chat_id);
    if(!is_null($user_id)) {
        $data = [
            "subscription" => 0,
        ];
        $db->where ("id_session", $user_id);
        $db->update ('session', $data);
    }
}

function getSubscribeList()
{
    global $db;
    $db->where ("subscription", 1);
    return $db->get("session", null, ["id_chat", "city"]);
}