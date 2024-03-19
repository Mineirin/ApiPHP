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

if ($data) { // Verifica se o JSON foi recebido corretamente
    $params = [
        'ticketID' => $data['ticketID'],
        'ticketDetails' => [
            'createDate' => $data['ticketDetails']['createDate'],
            'institution' => [
                'institutionNumber' => $data['ticketDetails']['institution']['institutionNumber'],
                'institutionName' => $data['ticketDetails']['institution']['institutionName'],
                'institutionContractID' => $data['ticketDetails']['institution']['institutionContractID']
            ],
            'status' => $data['ticketDetails']['status'],
            'channel' => $data['ticketDetails']['channel'],
            'channelType' => $data['ticketDetails']['channelType'],
            'subChannel' => $data['ticketDetails']['subChannel'],
            'agentName' => $data['ticketDetails']['agentName'],
            'type' => $data['ticketDetails']['type'],
            'ltn' => $data['ticketDetails']['ltn'],
            'technology' => $data['ticketDetails']['technology'],
            'serviceProvider' => $data['ticketDetails']['serviceProvider'],
            'modal' => $data['ticketDetails']['modal'],
            'connectivity' => $data['ticketDetails']['connectivity'],
            'accessories' => $data['ticketDetails']['accessories'],
            'urgencyCode' => $data['ticketDetails']['urgencyCode'],
            'sla' => $data['ticketDetails']['sla'],
            'partNumber' => $data['ticketDetails']['partNumber'],
            'mobileOperator' => $data['ticketDetails']['mobileOperator'],
            'selected' => $data['ticketDetails']['selected'],
            'heatMap' => $data['ticketDetails']['heatMap'],
            'agingEquipment' => $data['ticketDetails']['agingEquipment'],
            'productSerialInstall' => $data['ticketDetails']['productSerialInstall'],
            'productSerialUninstall' => $data['ticketDetails']['productSerialUninstall'],
            'immediateDelivery' => $data['ticketDetails']['immediateDelivery'],
            'specialConditions' => $data['ticketDetails']['specialConditions'],
            'freeFieldAdvancedPost' => $data['ticketDetails']['freeFieldAdvancedPost'],
            'motive' => $data['ticketDetails']['motive'],
            'reasonMaintenance' => $data['ticketDetails']['reasonMaintenance'],
            'motiveReschedule' => $data['ticketDetails']['motiveReschedule'],
            'recurrenceInfo' => $data['ticketDetails']['recurrenceInfo'],
            'origin' => $data['ticketDetails']['origin'],
            'scheduled' => $data['ticketDetails']['scheduled'],
            'countSchedule' => $data['ticketDetails']['countSchedule'],
            'totalValue' => $data['ticketDetails']['totalValue'],
            'businessType' => $data['ticketDetails']['businessType'],
            'paymentType' => $data['ticketDetails']['paymentType'],
            'paIdentification' => $data['ticketDetails']['paIdentification'],
            'slaInOut' => $data['ticketDetails']['slaInOut'],
        ],
        'cluster' => [
            'clusterID' => $data['cluster']['clusterID'],
            'agingEquipment' => $data['cluster']['agingEquipment'],
            'additionalInfo' => $data['cluster']['additionalInfo']
        ],
        'merchantDetails' => [
            'merchantID' => $data['merchantDetails']['merchantID'],
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
                'saturday' => $data['merchantDetails']['maintenanceDays']['saturday'],
            ]
        ],
        'originalDataElements' => $data['originalDataElements']
    ];
    if ($post->order($params)) {
        // Após a inserção, recupere o ticketID e sysRetRefNumber
        $ticketID = $params['ticketID'];
        $sysRetRefNumber = $params['ticketID']; // Aqui você deve ajustar para o valor correto
        $originalDataElements = $params['originalDataElements']; // Aqui você deve ajustar para o valor correto
    
        http_response_code(200);
        echo json_encode(['message' => 'Post added successfully', 'ticketID' => $ticketID, 'sysRetRefNumber' => $sysRetRefNumber, 'originalDataElements' => $originalDataElements]);
    } else {
        // Se a ordem falhar, retornar uma mensagem de erro
        http_response_code(500);
        echo json_encode(['message' => 'Failed to add post']);
    }
    
} else {
    echo json_encode(['message' => 'No JSON data received']);
}
