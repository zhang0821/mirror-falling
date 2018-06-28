<?php

    $host = "https://ali-weather.showapi.com";
    $path = "/ip-to-weather";
    $method = "GET";
    $appcode = "8250ac41301a4495889e8b6fd1a29204";
    $headers = array();
    array_push($headers, "Authorization:APPCODE " . $appcode);
    // needMoreDay - 0 - 返回3天 - 1 - 返回后4天 - 不重叠
    $querys = "ip=59.172.151.46&need3HourForcast=0&needAlarm=0&needHourData=0&needIndex=1&needMoreDay=0";
    $bodys = "";
    $url = $host . $path . "?" . $querys;

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_FAILONERROR, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    // 不显示header头信息
    // curl_setopt($curl, CURLOPT_HEADER, true);
    // f1 - 今天
    // f2 - 明天
    // f3 - 后天
    if (1 == strpos("$".$host, "https://"))
    {
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    }
    $result = curl_exec($curl);
    echo "<pre>";
        print_r($result);
    echo "</pre>";

?>