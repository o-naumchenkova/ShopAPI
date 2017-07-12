<?php
header('Content-type: text/html; charset=UTF-8');
require_once 'ShopAPI.php';

$shopAPI = new ShopAPI();

$action = array_key_exists("action", $_REQUEST) ? $_REQUEST["action"] : null;
$params = array_key_exists("params", $_REQUEST) ? $_REQUEST["params"] : [];
$shopAPI->Dispatch($action, $params);

?>