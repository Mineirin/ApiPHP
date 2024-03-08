<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');

// Incluindo arquivos necessários
include_once('config/Database.php');
include_once('models/Request.php');

// Conexão com o banco de dados
$database = new Database;
$db = $database->connect();

// Verificar se a conexão com o banco de dados foi bem-sucedida
if (!$db) {
    echo json_encode(['error' => 'Database connection error']);
    exit;
}

$token = new Request($db);

if (!$token->auth()){
    http_response_code(404);
    echo json_encode([
       'message' => 'Não foi possível gerar o token, entre em contato com o administrador',
    ]);
}
else{
    http_response_code(404);
    echo json_encode($token->auth());
}