<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once '../../model/Database.php';
include_once '../../model/UserModel.php';

$database = new Database;
$db = $database->connect();

$user = new UserModel($db);
$res = $user->get();

if (!$res) echo 'error error';
else echo json_encode(($res));
