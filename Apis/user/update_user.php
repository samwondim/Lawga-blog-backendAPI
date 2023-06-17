<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: Application/json');

include_once '../../model/Database.php';
include_once '../../model/UserModel.php';

$database = new Database();
$db = $database->connect();

$user = new UserModel($db);
$row = $user->update();

if ($row === false) {
    echo json_encode(['message' => 'update failed!']);
} else {
    echo json_encode($row);
}
