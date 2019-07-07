<?php

const HOST = 'b8rg15mwxwynuk9q.chr7pe7iynqr.eu-west-1.rds.amazonaws.com';
const USER = 'dv8waucz07pdmu54';
const PASSWORD = 'nvy4ervmlp4wpzhe';
const DATABASE = 'lq8s88g5if4zpb7e';
$db = null;


function createDbConnection()
{ 
    global $db;
    $db = new MysqliDb(HOST, USER, PASSWORD, DATABASE);
}

function findCity($city): ?int
{
    global $db;
    $db->where("name", $city);
    $city_str = $db->getOne("city");
    return (isset($city_str)) ? $city_str['id_city'] : null; 
}

function findUser($chatId)
{
    global $db;
    $db->where("id_chat", $chatId);
    $user = $db->getOne("session");
    return (isset($user)) ? $user['id_session'] : null; 
}

function addCity($city): ?int
{
    global $db;
    $id = findCity($city);
    if (is_null($id)) {
        $data = [
            "name" => $city,
        ];
        $id = $db->insert('city', $data);
    }
    return $id;
}

function getCityName($idCity): ?string
{
    global $db;
    $db->where("id_city", $idCity);
    $city_str = $db->getOne("city");
    return (isset($city_str)) ? $city_str['name'] : null;  
}

function saveFavoriteCity($city, $chatId)
{
    global $db;
    $cityId = addCity($city);
    $userId = findUser($chatId);
    if(!is_null($userId)) {
        $data = ["city" => $cityId];
        $db->where("id_session", $userId);
        $db->update('session', $data);
    }
}

function saveLastRequestedCity($city, $chatId)
{
    global $db;
    $cityId = addCity($city);
    $userId = findUser($chatId);
    if(!is_null($userId)) {
        $data = ["last_request" => $cityId];
        $db->where("id_session", $userId);
        $db->update('session', $data);
    }
}

function getFavoriteCity($chatId)
{
    global $db;
    $db->join('session', 'session.city = city.id_city', 'LEFT');
    $db->where("id_chat", $chatId);
    $city = $db->get('city', null, 'city.name');
    return (isset($city[0]['name']))? $city[0]['name'] : null; 
}

function getLastRequestedCity($chatId)
{
    global $db;
    $db->join('session', 'session.last_request = city.id_city', 'LEFT');
    $db->where("id_chat", $chatId);
    $city = $db->get('city', null, 'city.name');
    return (isset($city[0]['name']))? $city[0]['name'] : null; 
}

function getSubscribedStatus($chatId)
{
    global $db;
    $db->where("id_chat", $chatId);
    $user = $db->getOne("session");
    return $user['subscription']; 
}

function addNewUser($chatId, $name)
{  
    global $db;
    if (is_null(findUser($chatId))) {
        $data = [
            "name" => $name,
            "id_chat" => $chatId
        ];
        $id = $db->insert('session', $data);
    }
}

function setSubscribedStatus($chatId)
{
    global $db;
    $user_id = findUser($chatId);
    if(!is_null($user_id)) {
        $data = [
            "subscription" => 1,
        ];
        $db->where("id_session", $user_id);
        $db->update('session', $data);
    }
}

function removeSubscribedStatus($chatId)
{
    global $db;
    $user_id = findUser($chatId);
    if(!is_null($user_id)) {
        $data = [
            "subscription" => 0,
        ];
        $db->where("id_session", $user_id);
        $db->update('session', $data);
    }
}

function getSubscribeList()
{
    global $db;
    $db->where("subscription", 1);
    return $db->get("session", null, ["id_chat", "city"]);
}




