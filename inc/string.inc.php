<?php

function getSubstBeforeBlank($str)
{
    return mb_ereg_replace('^([^\s]*).*$', '\\1', trim($str));
}


function parseForecast($response)
{
    $data = array();
    $location = array();
    $forecast = array();

    if (isset($response['error']['code'])) {
        return null;
    }

    $city = (isset($response['location']['name'])) ? $response['location']['name'] : null;
    $country = (isset($response['location']['country'])) ? $response['location']['country'] : null;
    $location['city'] = $city;
    $location['country'] = $country; 
  
    foreach($response['forecast']['forecastday'] as $dayForecast) {
        $daily = array();
        $daily['date'] = $dayForecast['date'];
        $daily['max_temp'] = $dayForecast['day']['maxtemp_c'];
        $daily['min_temp'] = $dayForecast['day']['mintemp_c'];
        $daily['condition'] = $dayForecast['day']['condition']['text'];
        $daily['icon'] = $dayForecast['day']['condition']['icon'];
        $forecast[] = $daily;
    }

    $data['location'] = $location;
    $data['forecast'] = $forecast;

    return $data;
}