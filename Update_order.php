<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

Header('Access-Control-Allow-Origin: *');
Header('Content-Type: application/json');
Header('Access-Control-Allow-Method: POST');

include_once('config/Database.php');
include_once('models/Request.php');

$database = new Database;
$db = $database->connect();

$post = new Request($db);
$data = json_decode(file_get_contents("php://input"), true);

if ($data) { // Verifica se o JSON foi recebido corretamente
    $params = [
        'ticketID' => $data['ticketID'],
        'ticketDetails' => [
            'serviceProvider' => $data['ticketDetails']['serviceProvider'],
            'serialNumberOld' => $data['ticketDetails']['serialNumberOld'],
            'urgencyCode' => $data['ticketDetails']['urgencyCode'],
            'partNumber' => $data['ticketDetails']['partNumber'],
            'scheduled' => $data['ticketDetails']['scheduled'],
            'ltn' => $data['ticketDetails']['ltn']
        ],
        'merchantDetails' => [
            'CNPJ' => $data['merchantDetails']['CNPJ'],
            'name' => $data['merchantDetails']['name'],
            'tradeName' => $data['merchantDetails']['tradeName'],
            'address' => [
                'street' => $data['merchantDetails']['address']['street'],
                'number' => $data['merchantDetails']['address']['number'],
                'neighborhood' => $data['merchantDetails']['address']['neighborhood'],
                'zipCode' => $data['merchantDetails']['address']['zipCode'],
                'city' => $data['merchantDetails']['address']['city'],
                'state' => $data['merchantDetails']['address']['state'],
                'complement' => $data['merchantDetails']['address']['complement']
            ],
            'contactName' => $data['merchantDetails']['contactName'],
            'phone' => $data['merchantDetails']['phone'],
            'description' => $data['merchantDetails']['description'],
            'geolocation' => $data['merchantDetails']['geolocation'],
            'notes' => $data['merchantDetails']['notes'],
            'workDays' => [
                'sunday' => $data['merchantDetails']['workDays']['sunday'],
                'monday' => $data['merchantDetails']['workDays']['monday'],
                'tuesday' => $data['merchantDetails']['workDays']['tuesday'],
                'wednesday' => $data['merchantDetails']['workDays']['wednesday'],
                'thursday' => $data['merchantDetails']['workDays']['thursday'],
                'friday' => $data['merchantDetails']['workDays']['friday'],
                'saturday' => $data['merchantDetails']['workDays']['saturday'],
            ],
            'maintenanceDays' => [
                'sunday' => $data['merchantDetails']['maintenanceDays']['sunday'],
                'monday' => $data['merchantDetails']['maintenanceDays']['monday'],
                'tuesday' => $data['merchantDetails']['maintenanceDays']['tuesday'],
                'wednesday' => $data['merchantDetails']['maintenanceDays']['wednesday'],
                'thursday' => $data['merchantDetails']['maintenanceDays']['thursday'],
                'friday' => $data['merchantDetails']['maintenanceDays']['friday'],
                'saturday' => $data['merchantDetails']['maintenanceDays']['saturday']
            ]
        ],
        'originalDataElements' => $data['originalDataElements'],
        'sysRetRefNumber' => $data['sysRetRefNumber'],
        'info' => $data['info'],
    ];

    if ($post->update_order($params)) {
        echo json_encode(['message' => 'Update successfully']);
    } else {
        echo json_encode(['message' => 'Update error']);
    }
} else {
    echo json_encode(['message' => 'No JSON data received']);
}
