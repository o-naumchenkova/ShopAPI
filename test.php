<?php
$params = Array(
		"action"   	=> "get_all_categories",
		"params"	=> []
		);


$url      = "http://localhost/mytest/";

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
// указываем, что у нас POST запрос
curl_setopt($ch, CURLOPT_POST, 1);
// добавляем переменные
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));

$result = curl_exec($ch);
curl_close($ch);

print_r($result);
?>