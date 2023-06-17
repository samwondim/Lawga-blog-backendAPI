<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: Application/json');

include_once '../../model/Database.php';
include_once '../../model/UserModel.php';

$database = new Database();
$db = $database->connect();

$user = new UserModel($db);
$response = $user->delete();

echo json_encode($response);
