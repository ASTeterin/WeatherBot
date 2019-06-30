<?php

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
	$array = ['max_temp' => $maxTemp, 'min_temp' => $minTemp, 'rain' => $rain];
	//echo $maxTemp . " " . $minTemp . " " . $rain;
	$data[] = $array;
	//var_dump($array);
}  
return $data;
}