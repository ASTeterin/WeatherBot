<?php

function getSubstBeforeBlank($str)
{
    return mb_ereg_replace('^([^\s]*).*$', '\\1', trim($str));
}



function removeExtraSymbols($str, $symbol)
{
    $words = [];
    $tempStr = '';
    $words = explode($symbol , $str);
    foreach ($words as $word) {
        if ($word != '') {
            $tempStr .= $word . $symbol;
        }
    }
    return rtrim($tempStr, $symbol);
}


function parseForecast($forecast)
{
    $data = array();
    $location = array();
    $forecast = array();

    $city = $response['location']['name'];
    $country = $response['location']['country'];
    $location['city'] = $city;
    $location['country'] = $country; 
  
    foreach($response['forecast']['forecastday'] as $dayForecast) {
        $dayly['date'] = $dayForecast['date'];
        $dayly['max_temp'] = $dayForecast['day']['maxtemp_c'];
        $dayly['min_temp'] = $dayForecast['day']['mintemp_c'];
        $dayly['condition'] = $dayForecast['day']['condition']['text'];
        $dayly['icon'] = $dayForecast['day']['condition']['icon'];
        $forecast[] = $dayly;
    }

    $data['location'] = $location;
    $data['forecast'] = $forecast;

    return $data;
}