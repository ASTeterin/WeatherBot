<?php

const API_TOKEN = '832044822:AAEb48OoiZoxf4YTrS3T3-Z1GWcugj_VMcE';
const API_URL = "http://api.apixu.com/v1/forecast.json?key=a063d1eac8054ab392f195555192506&q=";    

function getDataFromApi($url): ?string
    {
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $data = curl_exec($ch);

        curl_close($ch);
        return $data;
    }
    
    function getForecast($city, $days): string
    {
        $url = API_URL . urlencode($city) . "&days=" . $days . "&lang=ru";
        return getDataFromApi($url); 
    }
    
    