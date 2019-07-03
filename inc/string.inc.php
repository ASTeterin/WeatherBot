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

/*function parseForecast($forecast)
{
    $data = array();
    foreach($forecast as $daylyForecast) {
        $date = substr($daylyForecast, 0, 10);
        $maxTempPos = strpos($daylyForecast, "maxtemp_c", 0) + 11;
        $maxTempPosEnd = strpos($daylyForecast, ",", $maxTempPos);
        $maxTemp = substr($daylyForecast, $maxTempPos, $maxTempPosEnd - $maxTempPos); 

        $minTempPos = strpos($daylyForecast, "mintemp_c", $maxTempPosEnd) + 11;
        $minTempPosEnd = strpos($daylyForecast, ",", $minTempPos);
        $minTemp = substr($daylyForecast, $minTempPos, $minTempPosEnd - $minTempPos); 

        $rainPos = strpos($daylyForecast, "{\"text\":\"", $minTempPosEnd) + 9;
        $rainPosEnd = strpos($daylyForecast, ",", $rainPos); 
        $rain = substr($daylyForecast, $rainPos, $rainPosEnd - $rainPos - 1); 
        $array = ['date' => $date, 'max_temp' => $maxTemp, 'min_temp' => $minTemp, 'rain' => $rain];

        $data[] = $array;
    }  
    return $data;
}*/

function parseForecast($forecast)
{
    $data = array();
    $location = array();
    $forecast = array();

    $city = $response['location']['name'];
    $country = $response['location']['country'];
    $location[] = ['city' => $city];
    $location[] = ['country' => $country]; 
  
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