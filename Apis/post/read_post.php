<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once '../../model/Database.php';
include_once '../../model/PostModel.php';

$database = new Database();
$db = $database->connect();

$post = new PostModel($db);

$response = $post->getAll();

echo json_encode($response);
