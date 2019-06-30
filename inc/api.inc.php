<?php

    function getDataFromApi($url): ?string
    {
        $ch = curl_init();
        // установка URL и других необходимых параметров
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // загрузка страницы и выдача её браузеру
        $data = curl_exec($ch);
        // завершение сеанса и освобождение ресурсов
        curl_close($ch);
        return $data;
    }