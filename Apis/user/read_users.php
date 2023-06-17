<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once '../../model/Database.php';
include_once '../../model/UserModel.php';

$database = new Database();
$db = $database->connect();

$user = new UserModel($db);

$response = $user->getAll();

echo json_encode($response);
