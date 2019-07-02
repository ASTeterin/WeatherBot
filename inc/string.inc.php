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

function parseForecast($response)
{
    $data = array();
    $city = $response['location']['name'];
    $country = $response['location']['country'];
    $data[] = ['city' => $city];
    $data[] = ['country' => $country];
    $forecast = array();
    
    foreach($response['forecast']['forecastday'] as $dayForecast) {
        $forecast['date'] = $dayForecast;
        $forecast['max_temp'] = $dayForecast['day']['maxtemp_c'];
        $forecast['min_temp'] = $dayForecast['day']['mintemp_c'];
        $forecast['condition'] = $dayForecast['day']['condition']['text'];
    }
}

function parseForecast($forecast)
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
        //echo $maxTemp . " " . $minTemp . " " . $rain;
        $data[] = $array;
        //var_dump($array);
    }  
    return $data;
}