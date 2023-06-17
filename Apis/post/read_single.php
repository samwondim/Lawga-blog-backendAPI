<?php
header('Acces-Control-Allow-Origin: *');
header('Content-Type: Application/json');

include_once '../model/Database.php';
include_once '../model/UserModel.php';
include_once '../inc/bootstrap.php';

$database = new Database(DB_HOST, DB_NAME, DB_USER, DB_PASS);
$post = new PostModel($database);

$id = $_GET['id'];

$result = $post->get($id);
echo json_encode($result);
