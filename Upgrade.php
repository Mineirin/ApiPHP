<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');

include_once('config/Database.php');
include_once('models/Request.php');

$database = new Database;
$db = $database->connect();
$post = new Request($db);
$data = json_decode(file_get_contents("php://input"), true);

if ($data) {
    $params = [
        'ticketID' => $data['ticketID'],
        'partNumber' => $data['partNumber'],
        'urgencyCode' => $data['urgencyCode'],
        'geolocation' => $data['geolocation'],
        'sysRetRefNumber' => $data['sysRetRefNumber'],
        'originalDataElements' => $data['originalDataElements'],
    ];
    if ($post->upgrade($params)) {
        // Após a inserção, recupere o ticketID e sysRetRefNumber
        $ticketID = $params['ticketID'];
        $sysRetRefNumber = $params['ticketID']; // Aqui você deve ajustar para o valor correto
    
        http_response_code(200);
        echo json_encode(['message' => 'Post added successfully', 'ticketID' => $ticketID, 'sysRetRefNumber' => $sysRetRefNumber]);
    } else {
        // Se a ordem falhar, retornar uma mensagem de erro
        http_response_code(500);
        echo json_encode(['message' => 'Failed to add post']);
    }
    
} else {
    echo json_encode(['message' => 'No JSON data received']);
}
