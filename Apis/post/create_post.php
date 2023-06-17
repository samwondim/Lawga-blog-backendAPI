<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: Application/json');

include_once '../../model/Database.php';
include_once '../../model/PostModel.php';

$database = new Database();
$db = $database->connect();

$post = new PostModel($db);

$id = $post->create();

echo json_encode($id);
