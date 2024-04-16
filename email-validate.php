<?php

$email = "sonam.patelsdsfdf@nrt.co.in";

$key = "Z56xVu57UwqwGKtbzsvE2";

$url = "https://app.emaillistvalidation.com/api/verifEmailv2?secret=".$key."&email=".$email;


$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $url);

curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true );

$response = curl_exec($ch);

$result = json_decode($response);
echo $result;

curl_close($ch);




?>