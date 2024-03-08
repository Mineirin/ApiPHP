<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require($_SERVER['DOCUMENT_ROOT'] . '/api/vendor/autoload.php');

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

/**
 * @OA\Info(title="Ethos API", version="1.0")
 *      @OA\SecurityScheme(
 *          type="http",
 *          description="Fluxo de Autorização OAuth2 com geração de token JWT",
 *          name="Authorization",
 *          in="header",
 *          scheme="bearer",
 *          bearerFormat="JWT",
 *          securityScheme="bearerToken",
 *     )
 */

class Request
{

    protected $key = '923480239s42@#$@#';
    public $ticketID;
    public $originalDataElements;
    public $sysRetRefNumber;
    public $info;
    public $id;

    //Table ticketDetails
    public $createDate;
    public $institutionNumber;
    public $institutionName;
    public $institutionContractID;
    public $status;
    public $channel;
    public $channelType;
    public $subChannel;
    public $agentName;
    public $type;
    public $ltn;
    public $technology;
    public $serviceProvider;
    public $modal;
    public $connectivity;
    public $accessories;
    public $urgencyCode;
    public $sla;
    public $partNumber;
    public $mobileOperator;
    public $selected;
    public $heatMap;
    public $agingEquipment;
    public $productSerialInstall;
    public $productSerialUninstall;
    public $immediateDelivery;
    public $specialConditions;
    public $freeFieldAdvancedPost;
    public $motive;
    public $reasonMaintenance;
    public $motiveReschedule;
    public $recurrenceInfo;
    public $origin;
    public $scheduled;
    public $countSchedule;
    public $terminalPaymentInfo;
    public $totalValue;
    public $businessType;
    public $paymentType;
    public $paIdentification;
    public $slaInOut;

    //Table cluster
    public $clusterID;
    public $agingEquipment_cluster;
    public $additionalInfo;

    //Table merchantDetails
    public $merchantID;
    public $CNPJ;
    public $name;
    public $tradeName;
    public $address;
    public $complement;
    public $contactName;
    public $phone;
    public $description;
    public $geolocation;
    public $notes;

    //Table workDays
    public $sunday;
    public $monday;
    public $tuesday;
    public $wednesday;
    public $thursday;
    public $friday;
    public $saturday;

    //Table maintenanceDays
    public $md_sunday;
    public $md_monday;
    public $md_tuesday;
    public $md_wednesday;
    public $md_thursday;
    public $md_friday;
    public $md_saturday;
    public $md_workday;

    //Database Data.
    private $connection;

    public function __construct($db)
    {
        $this->connection = $db;
    }

    /**
     * @OA\Post(
     *     path="/api/auth",
     *     summary="Autenticação do usuário",
     *     tags={"Security"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="userName", type="string", example="seu_usuario"),
     *                 @OA\Property(property="password", type="string", example="sua_senha")
     *             )
     *         )
     *     ),
     *     @OA\Response(response="200", description="Token gerado com sucesso"),
     *     @OA\Response(response="401", description="Credenciais inválidas")
     * )
     */
    public function auth()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $jsonData = json_decode(file_get_contents("php://input"));

                if ($jsonData && isset($jsonData->userName, $jsonData->password)) {
                    $userName = $jsonData->userName;
                    $password = $jsonData->password;

                    $userId = $this->authenticateUser($userName, $password);

                    if ($userId !== null) {
                        $issueDate = time();
                        $expirationDate = $issueDate + 3600;
                        $notBeforeDate = $issueDate;

                        $payload = [
                            "iss" => "http://localhost/api/",
                            "aud" => "http://localhost",
                            "iat" => $issueDate,
                            "nbf" => $notBeforeDate,
                            "exp" => $expirationDate,
                            "userName" => $userName,
                            "userId" => $userId,
                        ];

                        $jwtGeneratedToken = JWT::encode($payload, $this->key, 'HS256');

                        echo json_encode([
                            'token' => $jwtGeneratedToken,
                            'expires' => $expirationDate
                        ]);
                        exit;
                    } else {
                        http_response_code(401);
                        echo json_encode([
                            'error' => 'Credenciais inválidas'
                        ]);
                        exit;
                    }
                }
            }

            http_response_code(400);
            echo json_encode([
                'error' => 'Formato de dados inválido'
            ]);
            exit;
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }

    private function authenticateUser($userName, $password)
    {
        try {
            // Substitua este bloco com sua lógica real de consulta ao banco de dados
            $query = 'SELECT id FROM usuarios WHERE usuario = :userName AND senha = :password';
            $requestTicketDetails = $this->connection->prepare($query);
            $requestTicketDetails->bindParam(':userName', $userName, PDO::PARAM_STR);
            $requestTicketDetails->bindParam(':password', $password, PDO::PARAM_STR);
            $requestTicketDetails->execute();

            $result = $requestTicketDetails->fetch(PDO::FETCH_ASSOC);

            return $result ? $result['id'] : null;
        } catch (PDOException $e) {
            // Lida com erros de consulta ao banco de dados
            echo 'Error: ' . $e->getMessage();
            return null;
        }
    }

    /**
     * @OA\Post(
     *     path="/api/Order",
     *      summary="Inserir dados",
     *      tags={"Posts"},
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *             @OA\Property(property="ticketID", type="string"),
     *             @OA\Property(property="ticketDetails", type="object",
     *                 @OA\Property(property="createDate", type="string"),
     *                 @OA\Property(property="institution", type="object",
     *                     @OA\Property(property="institutionNumber", type="string"),
     *                     @OA\Property(property="institutionName", type="string"),
     *                     @OA\Property(property="institutionContractID", type="string"),
     *                 ),
     *                 @OA\Property(property="status", type="string"),
     *                 @OA\Property(property="channel", type="string"),
     *                 @OA\Property(property="channelType", type="string"),
     *                 @OA\Property(property="subChannel", type="string"),
     *                 @OA\Property(property="agentName", type="string"),
     *                 @OA\Property(property="type", type="string"),
     *                 @OA\Property(property="ltn", type="string"),
     *                 @OA\Property(property="technology", type="string"),
     *                 @OA\Property(property="serviceProvider", type="string"),
     *                 @OA\Property(property="modal", type="string"),
     *                 @OA\Property(property="connectivity", type="string"),
     *                 @OA\Property(property="accessories", type="string"),
     *                 @OA\Property(property="urgencyCode", type="string"),
     *                 @OA\Property(property="sla", type="string"),
     *                 @OA\Property(property="partNumber", type="string"),
     *                 @OA\Property(property="mobileOperator", type="string"),
     *                 @OA\Property(property="selected", type="string"),
     *                 @OA\Property(property="heatMap", type="string"),
     *                 @OA\Property(property="agingEquipment", type="string"),
     *                 @OA\Property(property="productSerialInstall", type="string"),
     *                 @OA\Property(property="productSerialUninstall", type="string"),
     *                 @OA\Property(property="immediateDelivery", type="string"),
     *                 @OA\Property(property="specialConditions", type="string"),
     *                 @OA\Property(property="freeFieldAdvancedPost", type="string"),
     *                 @OA\Property(property="motive", type="string"),
     *                 @OA\Property(property="reasonMaintenance", type="string"),
     *                 @OA\Property(property="motiveReschedule", type="string"),
     *                 @OA\Property(property="recurrenceInfo", type="string"),
     *                 @OA\Property(property="origin", type="string"),
     *                 @OA\Property(property="scheduled", type="string"),
     *                 @OA\Property(property="countSchedule", type="string"),
     *                 @OA\Property(property="terminalPaymentInfo", type="string"),
     *                 @OA\Property(property="totalValue", type="string"),
     *                 @OA\Property(property="businessType", type="string"),
     *                 @OA\Property(property="paymentType", type="string"),
     *                 @OA\Property(property="paIdentification", type="string"),
     *                 @OA\Property(property="slaInOut", type="string"),
     *             ),
     *             @OA\Property(property="cluster", type="object",
     *                 @OA\Property(property="clusterID", type="string"),
     *                 @OA\Property(property="agingEquipment", type="string"),
     *                 @OA\Property(property="additionalInfo", type="string"),
     *             ),
     *             @OA\Property(property="merchantDetails", type="object",
     *                 @OA\Property(property="merchantID", type="string"),
     *                 @OA\Property(property="CNPJ", type="string"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="tradeName", type="string"),
     *                 @OA\Property(property="address", type="string"),
     *                 @OA\Property(property="complement", type="string"),
     *                 @OA\Property(property="contactName", type="string"),
     *                 @OA\Property(property="phone", type="string"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(property="geolocation", type="string"),
     *                 @OA\Property(property="notes", type="string"),
     *                 @OA\Property(property="workDays", type="object",
     *                     @OA\Property(property="sunday", type="string"),
     *                     @OA\Property(property="monday", type="string"),
     *                     @OA\Property(property="tuesday", type="string"),
     *                     @OA\Property(property="wednesday", type="string"),
     *                     @OA\Property(property="thursday", type="string"),
     *                     @OA\Property(property="friday", type="string"),
     *                     @OA\Property(property="saturday", type="string"),
     *                 ),
     *                 @OA\Property(property="maintenanceDays", type="object",
     *                     @OA\Property(property="sunday", type="string"),
     *                     @OA\Property(property="monday", type="string"),
     *                     @OA\Property(property="tuesday", type="string"),
     *                     @OA\Property(property="wednesday", type="string"),
     *                     @OA\Property(property="thursday", type="string"),
     *                     @OA\Property(property="friday", type="string"),
     *                     @OA\Property(property="saturday", type="string"),
     *                 ),
     *             ),
     *             @OA\Property(property="originalDataElements", type="string"),
     *         ),
     *     ),
     *     ),
     *    @OA\Response(response="200", description="The data"),
     *    @OA\Response(response="404", description="Not Found"),
     *    security={ {"bearerToken": {}}}
     *  ),
     */
    public function order($params)
    {
        try {
            $headers = getallheaders();

            if (isset($headers['Authorization'])) {
                $token = trim(str_ireplace('Bearer', '', $headers['Authorization']));
                try {
                    $decoded = JWT::decode($token, new Key($this->key, 'HS256'));
                    if (isset($decoded->userName)) {
                        $this->connection->beginTransaction();

                        $this->ticketID = $params['ticketID'];
                        $this->originalDataElements = $params['originalDataElements'];

                        $query = 'INSERT INTO ticket SET ticketID = :ticketID, originalDataElements = :originalDataElements, sysRetRefNumber = :sysRetRefNumber';
                        $ticketPost = $this->connection->prepare($query);
                        $ticketPost->bindValue('ticketID', $this->ticketID);
                        $ticketPost->bindValue('originalDataElements', $this->originalDataElements);
                        $ticketPost->bindValue('sysRetRefNumber', $this->ticketID);
                        $ticketPost->execute();

                        $ticket_id = $this->connection->lastInsertId();

                        $this->institutionNumber = $params['ticketDetails']['institution']['institutionNumber'];
                        $this->institutionName = $params['ticketDetails']['institution']['institutionName'];
                        $this->institutionContractID = $params['ticketDetails']['institution']['institutionContractID'];

                        $queryInst = 'INSERT INTO institution SET ticket_id = :ticket_id, institutionNumber = :institutionNumber, institutionName = :institutionName, institutionContractID = :institutionContractID';
                        $instPost = $this->connection->prepare($queryInst);
                        $instPost->bindValue('ticket_id', $ticket_id);
                        $instPost->bindValue('institutionNumber', $this->institutionNumber);
                        $instPost->bindValue('institutionName', $this->institutionName);
                        $instPost->bindValue('institutionContractID', $this->institutionContractID);
                        $instPost->execute();

                        $institution_id = $this->connection->lastInsertId();

                        $this->createDate = $params['ticketDetails']['createDate'];
                        $this->status = $params['ticketDetails']['status'];
                        $this->channel = $params['ticketDetails']['channel'];
                        $this->channelType = $params['ticketDetails']['channelType'];
                        $this->subChannel = $params['ticketDetails']['subChannel'];
                        $this->agentName = $params['ticketDetails']['agentName'];
                        $this->type = $params['ticketDetails']['type'];
                        $this->ltn = $params['ticketDetails']['ltn'];
                        $this->technology = $params['ticketDetails']['technology'];
                        $this->serviceProvider = $params['ticketDetails']['serviceProvider'];
                        $this->modal = $params['ticketDetails']['modal'];
                        $this->connectivity = $params['ticketDetails']['connectivity'];
                        $this->accessories = $params['ticketDetails']['accessories'];
                        $this->urgencyCode = $params['ticketDetails']['urgencyCode'];
                        $this->sla = $params['ticketDetails']['sla'];
                        $this->partNumber = $params['ticketDetails']['partNumber'];
                        $this->mobileOperator = $params['ticketDetails']['mobileOperator'];
                        $this->selected = $params['ticketDetails']['selected'];
                        $this->heatMap = $params['ticketDetails']['heatMap'];
                        $this->agingEquipment = $params['ticketDetails']['agingEquipment'];
                        $this->productSerialInstall = $params['ticketDetails']['productSerialInstall'];
                        $this->productSerialUninstall = $params['ticketDetails']['productSerialUninstall'];
                        $this->immediateDelivery = $params['ticketDetails']['immediateDelivery'];
                        $this->specialConditions = $params['ticketDetails']['specialConditions'];
                        $this->freeFieldAdvancedPost = $params['ticketDetails']['freeFieldAdvancedPost'];
                        $this->motive = $params['ticketDetails']['motive'];
                        $this->reasonMaintenance = $params['ticketDetails']['reasonMaintenance'];
                        $this->motiveReschedule = $params['ticketDetails']['motiveReschedule'];
                        $this->recurrenceInfo = $params['ticketDetails']['recurrenceInfo'];
                        $this->origin = $params['ticketDetails']['origin'];
                        $this->scheduled = $params['ticketDetails']['scheduled'];
                        $this->countSchedule = $params['ticketDetails']['countSchedule'];
                        $this->terminalPaymentInfo = $params['ticketDetails']['terminalPaymentInfo'];
                        $this->totalValue = $params['ticketDetails']['totalValue'];
                        $this->businessType = $params['ticketDetails']['businessType'];
                        $this->paymentType = $params['ticketDetails']['paymentType'];
                        $this->paIdentification = $params['ticketDetails']['paIdentification'];
                        $this->slaInOut = $params['ticketDetails']['slaInOut'];

                        $queryTD = 'INSERT INTO ticketdetails SET ticket_id = :ticket_id, institution_id = :institution_id, createDate = :createDate, status = :status, 
                                  channel = :channel, channelType = :channelType, subChannel = :subChannel, 
                                  agentName = :agentName, type = :type, ltn = :ltn, technology = :technology, 
                                  serviceProvider = :serviceProvider, modal = :modal, connectivity = :connectivity, 
                                  accessories = :accessories, urgencyCode = :urgencyCode, sla = :sla, partNumber = :partNumber, 
                                  mobileOperator = :mobileOperator, selected = :selected, heatMap = :heatMap, 
                                  agingEquipment = :agingEquipment, productSerialInstall = :productSerialInstall, 
                                  productSerialUninstall = :productSerialUninstall, immediateDelivery = :immediateDelivery, 
                                  specialConditions = :specialConditions, freeFieldAdvancedPost = :freeFieldAdvancedPost, 
                                  motive = :motive, reasonMaintenance = :reasonMaintenance, motiveReschedule = :motiveReschedule, 
                                  recurrenceInfo = :recurrenceInfo, origin = :origin, scheduled = :scheduled, countSchedule = :countSchedule, 
                                  terminalPaymentInfo = :terminalPaymentInfo, totalValue = :totalValue, businessType = :businessType, 
                                  paymentType = :paymentType, paIdentification = :paIdentification, slaInOut = :slaInOut';

                        $tdPost = $this->connection->prepare($queryTD);
                        $tdPost->bindValue('ticket_id', $ticket_id);
                        $tdPost->bindValue('institution_id', $institution_id);
                        $tdPost->bindValue('createDate', $this->createDate);
                        $tdPost->bindValue('status', $this->status);
                        $tdPost->bindValue('channel', $this->channel);
                        $tdPost->bindValue('channelType', $this->channelType);
                        $tdPost->bindValue('subChannel', $this->subChannel);
                        $tdPost->bindValue('agentName', $this->agentName);
                        $tdPost->bindValue('type', $this->type);
                        $tdPost->bindValue('ltn', $this->ltn);
                        $tdPost->bindValue('technology', $this->technology);
                        $tdPost->bindValue('serviceProvider', $this->serviceProvider);
                        $tdPost->bindValue('modal', $this->modal);
                        $tdPost->bindValue('connectivity', $this->connectivity);
                        $tdPost->bindValue('accessories', $this->accessories);
                        $tdPost->bindValue('urgencyCode', $this->urgencyCode);
                        $tdPost->bindValue('sla', $this->sla);
                        $tdPost->bindValue('partNumber', $this->partNumber);
                        $tdPost->bindValue('mobileOperator', $this->mobileOperator);
                        $tdPost->bindValue('selected', $this->selected);
                        $tdPost->bindValue('heatMap', $this->heatMap);
                        $tdPost->bindValue('agingEquipment', $this->agingEquipment);
                        $tdPost->bindValue('productSerialInstall', $this->productSerialInstall);
                        $tdPost->bindValue('productSerialUninstall', $this->productSerialUninstall);
                        $tdPost->bindValue('immediateDelivery', $this->immediateDelivery);
                        $tdPost->bindValue('specialConditions', $this->specialConditions);
                        $tdPost->bindValue('freeFieldAdvancedPost', $this->freeFieldAdvancedPost);
                        $tdPost->bindValue('motive', $this->motive);
                        $tdPost->bindValue('reasonMaintenance', $this->reasonMaintenance);
                        $tdPost->bindValue('motiveReschedule', $this->motiveReschedule);
                        $tdPost->bindValue('recurrenceInfo', $this->recurrenceInfo);
                        $tdPost->bindValue('origin', $this->origin);
                        $tdPost->bindValue('scheduled', $this->scheduled);
                        $tdPost->bindValue('countSchedule', $this->countSchedule);
                        $tdPost->bindValue('terminalPaymentInfo', $this->terminalPaymentInfo);
                        $tdPost->bindValue('totalValue', $this->totalValue);
                        $tdPost->bindValue('businessType', $this->businessType);
                        $tdPost->bindValue('paymentType', $this->paymentType);
                        $tdPost->bindValue('paIdentification', $this->paIdentification);
                        $tdPost->bindValue('slaInOut', $this->slaInOut);

                        $tdPost->execute();

                        $this->clusterID = $params['cluster']['clusterID'];
                        $this->agingEquipment_cluster = $params['cluster']['agingEquipment'];
                        $this->additionalInfo = $params['cluster']['additionalInfo'];

                        $queryCluster = 'INSERT INTO cluster SET clusterID = :clusterID, ticket_id = :ticket_id, agingEquipment = :agingEquipment, additionalInfo = :additionalInfo';
                        $clusterPost = $this->connection->prepare($queryCluster);
                        $clusterPost->bindValue('ticket_id', $ticket_id);
                        $clusterPost->bindValue('clusterID', $this->clusterID);
                        $clusterPost->bindValue('agingEquipment', $this->agingEquipment_cluster);
                        $clusterPost->bindValue('additionalInfo', $this->additionalInfo);
                        $clusterPost->execute();


                        $this->merchantID = $params['merchantDetails']['merchantID'];
                        $this->CNPJ = $params['merchantDetails']['CNPJ'];
                        $this->name = $params['merchantDetails']['name'];
                        $this->tradeName = $params['merchantDetails']['tradeName'];
                        $this->address = $params['merchantDetails']['address'];
                        $this->complement = $params['merchantDetails']['complement'];
                        $this->contactName = $params['merchantDetails']['contactName'];
                        $this->phone = $params['merchantDetails']['phone'];
                        $this->description = $params['merchantDetails']['description'];
                        $this->geolocation = $params['merchantDetails']['geolocation'];
                        $this->notes = $params['merchantDetails']['notes'];

                        $queryMD = 'INSERT INTO merchantdetails SET merchantID = :merchantID, ticket_id = :ticket_id, CNPJ = :CNPJ, 
                                name = :name, tradeName = :tradeName, address = :address, 
                                complement = :complement, contactName = :contactName, phone = :phone, 
                                description = :description, geolocation = :geolocation, notes = :notes';

                        $mdPost = $this->connection->prepare($queryMD);
                        $mdPost->bindValue('ticket_id', $ticket_id);
                        $mdPost->bindValue('merchantID', $this->merchantID);
                        $mdPost->bindValue('CNPJ', $this->CNPJ);
                        $mdPost->bindValue('name', $this->name);
                        $mdPost->bindValue('tradeName', $this->tradeName);
                        $mdPost->bindValue('address', $this->address);
                        $mdPost->bindValue('complement', $this->complement);
                        $mdPost->bindValue('contactName', $this->contactName);
                        $mdPost->bindValue('phone', $this->phone);
                        $mdPost->bindValue('description', $this->description);
                        $mdPost->bindValue('geolocation', $this->geolocation);
                        $mdPost->bindValue('notes', $this->notes);
                        $mdPost->execute();

                        $merchant_id = $this->connection->lastInsertId();

                        $this->sunday = $params['merchantDetails']['workDays']['sunday'];
                        $this->monday = $params['merchantDetails']['workDays']['monday'];
                        $this->tuesday = $params['merchantDetails']['workDays']['tuesday'];
                        $this->wednesday = $params['merchantDetails']['workDays']['wednesday'];
                        $this->thursday = $params['merchantDetails']['workDays']['thursday'];
                        $this->friday = $params['merchantDetails']['workDays']['friday'];
                        $this->saturday = $params['merchantDetails']['workDays']['saturday'];

                        $queryWorkDays = 'INSERT INTO workdays SET merchant_id = :merchant_id, sunday = :sunday, monday = :monday, 
                            tuesday = :tuesday, wednesday = :wednesday, thursday = :thursday, 
                            friday = :friday, saturday = :saturday';
                        $wdPost = $this->connection->prepare($queryWorkDays);

                        $wdPost->bindValue('merchant_id', $merchant_id);
                        $wdPost->bindValue('sunday', $this->sunday);
                        $wdPost->bindValue('monday', $this->monday);
                        $wdPost->bindValue('tuesday', $this->tuesday);
                        $wdPost->bindValue('wednesday', $this->wednesday);
                        $wdPost->bindValue('thursday', $this->thursday);
                        $wdPost->bindValue('friday', $this->friday);
                        $wdPost->bindValue('saturday', $this->saturday);
                        $wdPost->execute();

                        $this->md_sunday = $params['merchantDetails']['maintenanceDays']['sunday'];
                        $this->md_monday = $params['merchantDetails']['maintenanceDays']['monday'];
                        $this->md_tuesday = $params['merchantDetails']['maintenanceDays']['tuesday'];
                        $this->md_wednesday = $params['merchantDetails']['maintenanceDays']['wednesday'];
                        $this->md_thursday = $params['merchantDetails']['maintenanceDays']['thursday'];
                        $this->md_friday = $params['merchantDetails']['maintenanceDays']['friday'];
                        $this->md_saturday = $params['merchantDetails']['maintenanceDays']['saturday'];

                        $queryMainDays = 'INSERT INTO maintenancedays SET merchant_id = :merchant_id, sunday = :sunday, monday = :monday, 
                            tuesday = :tuesday, wednesday = :wednesday, thursday = :thursday, 
                            friday = :friday, saturday = :saturday';

                        $mainDaysPost = $this->connection->prepare($queryMainDays);
                        $mainDaysPost->bindValue('merchant_id', $merchant_id);
                        $mainDaysPost->bindValue('sunday', $this->md_sunday);
                        $mainDaysPost->bindValue('monday', $this->md_monday);
                        $mainDaysPost->bindValue('tuesday', $this->md_tuesday);
                        $mainDaysPost->bindValue('wednesday', $this->md_wednesday);
                        $mainDaysPost->bindValue('thursday', $this->md_thursday);
                        $mainDaysPost->bindValue('friday', $this->md_friday);
                        $mainDaysPost->bindValue('saturday', $this->md_saturday);
                        $mainDaysPost->execute();
                        $this->connection->commit();

                        return true;
                    } else {
                        return false;
                    }
                } catch (Exception $e) {
                    echo 'Erro ao decodificar o token: ' . $e->getMessage();
                    return false;
                }
            } else {
                http_response_code(401);
                echo 'Credenciais Inválidas';
                return false;
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }

    /**
     * @OA\Put(
     *     path="/api/Update_order",
     *      summary="Editar dados",
     *      tags={"Posts"},
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *             @OA\Property(property="ticketID", type="string"),
     *             @OA\Property(property="ticketDetails", type="object",
     *                 @OA\Property(property="serviceProvider", type="string"),
     *                 @OA\Property(property="serialNumberOld", type="string"),
     *                 @OA\Property(property="partNumber", type="string"),
     *                 @OA\Property(property="ltn", type="string"),
     *                 @OA\Property(property="urgencyCode", type="string"),
     *                 @OA\Property(property="scheduled", type="string"),
     *             ),
     *             @OA\Property(property="merchantDetails", type="object",
     *                 @OA\Property(property="CNPJ", type="string"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="tradeName", type="string"),
     *                 @OA\Property(property="address", type="string"),
     *                 @OA\Property(property="complement", type="string"),
     *                 @OA\Property(property="contactName", type="string"),
     *                 @OA\Property(property="phone", type="string"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(property="geolocation", type="string"),
     *                 @OA\Property(property="notes", type="string"),
     *                 @OA\Property(property="workDays", type="object",
     *                     @OA\Property(property="sunday", type="string"),
     *                     @OA\Property(property="monday", type="string"),
     *                     @OA\Property(property="tuesday", type="string"),
     *                     @OA\Property(property="wednesday", type="string"),
     *                     @OA\Property(property="thursday", type="string"),
     *                     @OA\Property(property="friday", type="string"),
     *                     @OA\Property(property="saturday", type="string"),
     *                 ),
     *                 @OA\Property(property="maintenanceDays", type="object",
     *                     @OA\Property(property="sunday", type="string"),
     *                     @OA\Property(property="monday", type="string"),
     *                     @OA\Property(property="tuesday", type="string"),
     *                     @OA\Property(property="wednesday", type="string"),
     *                     @OA\Property(property="thursday", type="string"),
     *                     @OA\Property(property="friday", type="string"),
     *                     @OA\Property(property="saturday", type="string"),
     *                     @OA\Property(property="workday", type="string"),
     *                 ),
     *             ),
     *             @OA\Property(property="originalDataElements", type="string"),
     *             @OA\Property(property="sysRetRefNumber", type="string"),
     *             @OA\Property(property="info", type="string"),
     *         ),
     *     ),
     *     ),
     *    @OA\Response(response="200", description="The data"),
     *    @OA\Response(response="404", description="Not Found"),
     *    security={ {"bearerToken": {}}}
     *  ),
     */
    public function update_order($params)
    {
        try {
            $headers = apache_request_headers();

            if (isset($headers['Authorization'])) {
                $token = trim(str_ireplace('Bearer', '', $headers['Authorization']));
                $decoded = JWT::decode($token, new Key($this->key, 'HS256'));

                if (isset($decoded->userName)) {
                    $this->ticketID = $params['ticketID'];
                    $this->originalDataElements = $params['originalDataElements'];

                    $query = 'UPDATE ticket SET originalDataElements = :originalDataElements WHERE ticketID = :ticketID';
                    $ticketPost = $this->connection->prepare($query);
                    $ticketPost->bindValue('ticketID', $this->ticketID);
                    $ticketPost->bindValue('originalDataElements', $this->originalDataElements);
                    $ticketPost->execute();

                    $this->institutionNumber = $params['ticketDetails']['institution']['institutionNumber'];
                    $this->institutionName = $params['ticketDetails']['institution']['institutionName'];
                    $this->institutionContractID = $params['ticketDetails']['institution']['institutionContractID'];

                    $queryInst = 'UPDATE institution SET institutionNumber = :institutionNumber, institutionName = :institutionName, institutionContractID = :institutionContractID WHERE ticket_id = :ticket_id';
                    $instPost = $this->connection->prepare($queryInst);
                    $instPost->bindValue('ticket_id', $this->ticketID);
                    $instPost->bindValue('institutionNumber', $this->institutionNumber);
                    $instPost->bindValue('institutionName', $this->institutionName);
                    $instPost->bindValue('institutionContractID', $this->institutionContractID);
                    $instPost->execute();

                    $this->createDate = $params['ticketDetails']['createDate'];
                    $this->status = $params['ticketDetails']['status'];
                    $this->channel = $params['ticketDetails']['channel'];
                    $this->channelType = $params['ticketDetails']['channelType'];
                    $this->subChannel = $params['ticketDetails']['subChannel'];
                    $this->agentName = $params['ticketDetails']['agentName'];
                    $this->type = $params['ticketDetails']['type'];
                    $this->ltn = $params['ticketDetails']['ltn'];
                    $this->technology = $params['ticketDetails']['technology'];
                    $this->serviceProvider = $params['ticketDetails']['serviceProvider'];
                    $this->modal = $params['ticketDetails']['modal'];
                    $this->connectivity = $params['ticketDetails']['connectivity'];
                    $this->accessories = $params['ticketDetails']['accessories'];
                    $this->urgencyCode = $params['ticketDetails']['urgencyCode'];
                    $this->sla = $params['ticketDetails']['sla'];
                    $this->partNumber = $params['ticketDetails']['partNumber'];
                    $this->mobileOperator = $params['ticketDetails']['mobileOperator'];
                    $this->selected = $params['ticketDetails']['selected'];
                    $this->heatMap = $params['ticketDetails']['heatMap'];
                    $this->agingEquipment = $params['ticketDetails']['agingEquipment'];
                    $this->productSerialInstall = $params['ticketDetails']['productSerialInstall'];
                    $this->productSerialUninstall = $params['ticketDetails']['productSerialUninstall'];
                    $this->immediateDelivery = $params['ticketDetails']['immediateDelivery'];
                    $this->specialConditions = $params['ticketDetails']['specialConditions'];
                    $this->freeFieldAdvancedPost = $params['ticketDetails']['freeFieldAdvancedPost'];
                    $this->motive = $params['ticketDetails']['motive'];
                    $this->reasonMaintenance = $params['ticketDetails']['reasonMaintenance'];
                    $this->motiveReschedule = $params['ticketDetails']['motiveReschedule'];
                    $this->recurrenceInfo = $params['ticketDetails']['recurrenceInfo'];
                    $this->origin = $params['ticketDetails']['origin'];
                    $this->scheduled = $params['ticketDetails']['scheduled'];
                    $this->countSchedule = $params['ticketDetails']['countSchedule'];
                    $this->terminalPaymentInfo = $params['ticketDetails']['terminalPaymentInfo'];
                    $this->totalValue = $params['ticketDetails']['totalValue'];
                    $this->businessType = $params['ticketDetails']['businessType'];
                    $this->paymentType = $params['ticketDetails']['paymentType'];
                    $this->paIdentification = $params['ticketDetails']['paIdentification'];
                    $this->slaInOut = $params['ticketDetails']['slaInOut'];

                    $queryTD = 'UPDATE ticketdetails SET createDate = :createDate, status = :status, 
                              channel = :channel, channelType = :channelType, subChannel = :subChannel, 
                              agentName = :agentName, type = :type, ltn = :ltn, technology = :technology, 
                              serviceProvider = :serviceProvider, modal = :modal, connectivity = :connectivity, 
                              accessories = :accessories, urgencyCode = :urgencyCode, sla = :sla, partNumber = :partNumber, 
                              mobileOperator = :mobileOperator, selected = :selected, heatMap = :heatMap, 
                              agingEquipment = :agingEquipment, productSerialInstall = :productSerialInstall, 
                              productSerialUninstall = :productSerialUninstall, immediateDelivery = :immediateDelivery, 
                              specialConditions = :specialConditions, freeFieldAdvancedPost = :freeFieldAdvancedPost, 
                              motive = :motive, reasonMaintenance = :reasonMaintenance, motiveReschedule = :motiveReschedule, 
                              recurrenceInfo = :recurrenceInfo, origin = :origin, scheduled = :scheduled, countSchedule = :countSchedule, 
                              terminalPaymentInfo = :terminalPaymentInfo, totalValue = :totalValue, businessType = :businessType, 
                              paymentType = :paymentType, paIdentification = :paIdentification, slaInOut = :slaInOut WHERE ticket_id = :ticket_id';

                    $tdPost = $this->connection->prepare($queryTD);
                    $tdPost->bindValue('ticket_id', $this->ticketID);
                    $tdPost->bindValue('createDate', $this->createDate);
                    $tdPost->bindValue('status', $this->status);
                    $tdPost->bindValue('channel', $this->channel);
                    $tdPost->bindValue('channelType', $this->channelType);
                    $tdPost->bindValue('subChannel', $this->subChannel);
                    $tdPost->bindValue('agentName', $this->agentName);
                    $tdPost->bindValue('type', $this->type);
                    $tdPost->bindValue('ltn', $this->ltn);
                    $tdPost->bindValue('technology', $this->technology);
                    $tdPost->bindValue('serviceProvider', $this->serviceProvider);
                    $tdPost->bindValue('modal', $this->modal);
                    $tdPost->bindValue('connectivity', $this->connectivity);
                    $tdPost->bindValue('accessories', $this->accessories);
                    $tdPost->bindValue('urgencyCode', $this->urgencyCode);
                    $tdPost->bindValue('sla', $this->sla);
                    $tdPost->bindValue('partNumber', $this->partNumber);
                    $tdPost->bindValue('mobileOperator', $this->mobileOperator);
                    $tdPost->bindValue('selected', $this->selected);
                    $tdPost->bindValue('heatMap', $this->heatMap);
                    $tdPost->bindValue('agingEquipment', $this->agingEquipment);
                    $tdPost->bindValue('productSerialInstall', $this->productSerialInstall);
                    $tdPost->bindValue('productSerialUninstall', $this->productSerialUninstall);
                    $tdPost->bindValue('immediateDelivery', $this->immediateDelivery);
                    $tdPost->bindValue('specialConditions', $this->specialConditions);
                    $tdPost->bindValue('freeFieldAdvancedPost', $this->freeFieldAdvancedPost);
                    $tdPost->bindValue('motive', $this->motive);
                    $tdPost->bindValue('reasonMaintenance', $this->reasonMaintenance);
                    $tdPost->bindValue('motiveReschedule', $this->motiveReschedule);
                    $tdPost->bindValue('recurrenceInfo', $this->recurrenceInfo);
                    $tdPost->bindValue('origin', $this->origin);
                    $tdPost->bindValue('scheduled', $this->scheduled);
                    $tdPost->bindValue('countSchedule', $this->countSchedule);
                    $tdPost->bindValue('terminalPaymentInfo', $this->terminalPaymentInfo);
                    $tdPost->bindValue('totalValue', $this->totalValue);
                    $tdPost->bindValue('businessType', $this->businessType);
                    $tdPost->bindValue('paymentType', $this->paymentType);
                    $tdPost->bindValue('paIdentification', $this->paIdentification);
                    $tdPost->bindValue('slaInOut', $this->slaInOut);
                    $tdPost->execute();

                    $this->clusterID = $params['cluster']['clusterID'];
                    $this->agingEquipment_cluster = $params['cluster']['agingEquipment'];
                    $this->additionalInfo = $params['cluster']['additionalInfo'];

                    $queryCluster = 'UPDATE cluster SET clusterID = :clusterID, agingEquipment = :agingEquipment, additionalInfo = :additionalInfo WHERE ticket_id = :ticket_id';
                    $clusterPost = $this->connection->prepare($queryCluster);
                    $clusterPost->bindValue('ticket_id', $this->ticketID);
                    $clusterPost->bindValue('clusterID', $this->clusterID);
                    $clusterPost->bindValue('agingEquipment', $this->agingEquipment_cluster);
                    $clusterPost->bindValue('additionalInfo', $this->additionalInfo);
                    $clusterPost->execute();


                    $this->merchantID = $params['merchantDetails']['merchantID'];
                    $this->CNPJ = $params['merchantDetails']['CNPJ'];
                    $this->name = $params['merchantDetails']['name'];
                    $this->tradeName = $params['merchantDetails']['tradeName'];
                    $this->address = $params['merchantDetails']['address'];
                    $this->complement = $params['merchantDetails']['complement'];
                    $this->contactName = $params['merchantDetails']['contactName'];
                    $this->phone = $params['merchantDetails']['phone'];
                    $this->description = $params['merchantDetails']['description'];
                    $this->geolocation = $params['merchantDetails']['geolocation'];
                    $this->notes = $params['merchantDetails']['notes'];

                    $queryMD = 'UPDATE merchantdetails SET merchantID = :merchantID, CNPJ = :CNPJ, 
                            name = :name, tradeName = :tradeName, address = :address, 
                            complement = :complement, contactName = :contactName, phone = :phone, 
                            description = :description, geolocation = :geolocation, notes = :notes WHERE ticket_id = :ticket_id';

                    $mdPost = $this->connection->prepare($queryMD);
                    $mdPost->bindValue('ticket_id', $this->ticketID);
                    $mdPost->bindValue('merchantID', $this->merchantID);
                    $mdPost->bindValue('CNPJ', $this->CNPJ);
                    $mdPost->bindValue('name', $this->name);
                    $mdPost->bindValue('tradeName', $this->tradeName);
                    $mdPost->bindValue('address', $this->address);
                    $mdPost->bindValue('complement', $this->complement);
                    $mdPost->bindValue('contactName', $this->contactName);
                    $mdPost->bindValue('phone', $this->phone);
                    $mdPost->bindValue('description', $this->description);
                    $mdPost->bindValue('geolocation', $this->geolocation);
                    $mdPost->bindValue('notes', $this->notes);
                    $mdPost->execute();

                    $merchant_id = $this->ticketID;

                    $this->sunday = $params['merchantDetails']['workDays']['sunday'];
                    $this->monday = $params['merchantDetails']['workDays']['monday'];
                    $this->tuesday = $params['merchantDetails']['workDays']['tuesday'];
                    $this->wednesday = $params['merchantDetails']['workDays']['wednesday'];
                    $this->thursday = $params['merchantDetails']['workDays']['thursday'];
                    $this->friday = $params['merchantDetails']['workDays']['friday'];
                    $this->saturday = $params['merchantDetails']['workDays']['saturday'];

                    $queryWorkDays = 'UPDATE workdays SET sunday = :sunday, monday = :monday, 
                            tuesday = :tuesday, wednesday = :wednesday, thursday = :thursday, 
                            friday = :friday, saturday = :saturday WHERE merchant_id = :merchant_id';
                    $wdPost = $this->connection->prepare($queryWorkDays);

                    $wdPost->bindValue('merchant_id', $merchant_id);
                    $wdPost->bindValue('sunday', $this->sunday);
                    $wdPost->bindValue('monday', $this->monday);
                    $wdPost->bindValue('tuesday', $this->tuesday);
                    $wdPost->bindValue('wednesday', $this->wednesday);
                    $wdPost->bindValue('thursday', $this->thursday);
                    $wdPost->bindValue('friday', $this->friday);
                    $wdPost->bindValue('saturday', $this->saturday);
                    $wdPost->execute();

                    $this->md_sunday = $params['merchantDetails']['maintenanceDays']['sunday'];
                    $this->md_monday = $params['merchantDetails']['maintenanceDays']['monday'];
                    $this->md_tuesday = $params['merchantDetails']['maintenanceDays']['tuesday'];
                    $this->md_wednesday = $params['merchantDetails']['maintenanceDays']['wednesday'];
                    $this->md_thursday = $params['merchantDetails']['maintenanceDays']['thursday'];
                    $this->md_friday = $params['merchantDetails']['maintenanceDays']['friday'];
                    $this->md_saturday = $params['merchantDetails']['maintenanceDays']['saturday'];
                    $this->md_workday = $params['merchantDetails']['maintenanceDays']['workday'];

                    $queryMainDays = 'UPDATE maintenancedays SET sunday = :sunday, monday = :monday, 
                            tuesday = :tuesday, wednesday = :wednesday, thursday = :thursday, 
                            friday = :friday, saturday = :saturday, workday = :workday WHERE merchant_id = :merchant_id';

                    $mainDaysPost = $this->connection->prepare($queryMainDays);
                    $mainDaysPost->bindValue('merchant_id', $merchant_id);
                    $mainDaysPost->bindValue('sunday', $this->md_sunday);
                    $mainDaysPost->bindValue('monday', $this->md_monday);
                    $mainDaysPost->bindValue('tuesday', $this->md_tuesday);
                    $mainDaysPost->bindValue('wednesday', $this->md_wednesday);
                    $mainDaysPost->bindValue('thursday', $this->md_thursday);
                    $mainDaysPost->bindValue('friday', $this->md_friday);
                    $mainDaysPost->bindValue('saturday', $this->md_saturday);
                    $mainDaysPost->bindValue('workday', $this->md_workday);
                    $mainDaysPost->execute();

                    return true;
                } else {
                    return false;
                }
            } else {
                http_response_code(401);
                echo 'Credenciais Inválidas';
                return false;
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }

     /**
     * @OA\Post(
     *     path="/api/Cancel",
     *     summary="Cancela tickets",
     *     tags={"Posts"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="ticketID", type="string"),
     *                 @OA\Property(property="dateTimeCancelTransmission", type="string"),
     *                 @OA\Property(property="dateTimeTransaction", type="string"),
     *                 @OA\Property(property="originalDataElements", type="string"),
     *                 @OA\Property(property="sysRetRefNumber", type="string"),
     *                 @OA\Property(property="idMotiveCancel", type="string"),
     *                 @OA\Property(property="descriptionMotiveCancel", type="string"),
     *             )
     *         )
     *     ),
     *     @OA\Response(response="200", description="The data"),
     *     @OA\Response(response="404", description="Not Found"),
     *     security={ {"bearerToken": {}}}
     * )
     */
    public function cancel($params)
    {
        try {
            $headers = getallheaders();

            if (isset($headers['Authorization'])) {
                $token = trim(str_ireplace('Bearer', '', $headers['Authorization']));
                try {
                    $decoded = JWT::decode($token, new Key($this->key, 'HS256'));
                    if (isset($decoded->userName)) {
                        $this->connection->beginTransaction();

                        $sql_ticket = "UPDATE ticket SET 
                                    dateTimeCancelTransmission = :dateTimeCancelTransmission,
                                    dateTimeTransaction = :dateTimeTransaction,
                                    originalDataElements = :originalDataElements,
                                    sysRetRefNumber = :sysRetRefNumber,
                                    idMotiveCancel = :idMotiveCancel,
                                    descriptionMotiveCancel = :descriptionMotiveCancel
                                    WHERE ticketID = :ticketID";

                        $stmt_ticket = $this->connection->prepare($sql_ticket);
                        $success_ticket = $stmt_ticket->execute([
                            ':dateTimeCancelTransmission' => $params['dateTimeCancelTransmission'],
                            ':dateTimeTransaction' => $params['dateTimeTransaction'],
                            ':originalDataElements' => $params['originalDataElements'],
                            ':sysRetRefNumber' => $params['sysRetRefNumber'],
                            ':idMotiveCancel' => $params['idMotiveCancel'],
                            ':descriptionMotiveCancel' => $params['descriptionMotiveCancel'],
                            ':ticketID' => $params['ticketID']
                        ]);

                        $sql_ticketdetails = "UPDATE ticketdetails SET status = 'CANCEL' WHERE ticket_id = :ticket_id";

                        $stmt_ticketdetails = $this->connection->prepare($sql_ticketdetails);
                        $success_ticketdetails = $stmt_ticketdetails->execute([
                            ':ticket_id' => $params['ticketID'] // Alterei de 'ticket_id' para 'ticketID'
                        ]);

                        if ($success_ticket && $success_ticketdetails) {
                            $this->connection->commit();
                            return true;
                        } else {
                            $this->connection->rollBack();
                            return false;
                        }
                    } else {
                        return false;
                    }
                } catch (Exception $e) {
                    echo 'Erro ao decodificar o token: ' . $e->getMessage();
                    return false;
                }
            } else {
                http_response_code(401);
                echo 'Credenciais Inválidas';
                return false;
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }

    /**
     * @OA\Post(
     *     path="/api/Upgrade",
     *     summary="Upgrade ticket",
     *     tags={"Posts"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="ticketID", type="string"),
     *                 @OA\Property(property="partNumber", type="string"),
     *                 @OA\Property(property="urgencyCode", type="string"),
     *                 @OA\Property(property="geolocation", type="string"),
     *                 @OA\Property(property="sysRetRefNumber", type="string"),
     *                 @OA\Property(property="originalDataElements", type="string"),
     *             )
     *         )
     *     ),
     *     @OA\Response(response="200", description="The data"),
     *     @OA\Response(response="404", description="Not Found"),
     *     security={ {"bearerToken": {}}}
     * )
     */
    public function upgrade($params)
    {
        try {
            $headers = getallheaders();

            if (isset($headers['Authorization'])) {
                $token = trim(str_ireplace('Bearer', '', $headers['Authorization']));
                try {
                    $decoded = JWT::decode($token, new Key($this->key, 'HS256'));
                    if (isset($decoded->userName)) {
                        $this->connection->beginTransaction();

                        $sql_ticket = "UPDATE ticket SET 
                        originalDataElements = :originalDataElements,
                        sysRetRefNumber = :sysRetRefNumber,
                        geolocation = :geolocation
                        WHERE ticketID = :ticketID";

                        $stmt_ticket = $this->connection->prepare($sql_ticket);
                        $stmt_ticket->bindValue(':originalDataElements', $params['originalDataElements']);
                        $stmt_ticket->bindValue(':sysRetRefNumber', $params['sysRetRefNumber']);
                        $stmt_ticket->bindValue(':geolocation', $params['geolocation']);
                        $stmt_ticket->bindValue(':ticketID', $params['ticketID']);
                        $stmt_ticket->execute();

                        $sql_ticketdetails = "UPDATE ticketdetails SET 
                                        partNumber = :partNumber,
                                        urgencyCode = :urgencyCode
                                        WHERE ticket_id = :ticket_id";

                        $stmt_ticketdetails = $this->connection->prepare($sql_ticketdetails);
                        $stmt_ticketdetails->bindValue(':partNumber', $params['partNumber']);
                        $stmt_ticketdetails->bindValue(':urgencyCode', $params['urgencyCode']);
                        $stmt_ticketdetails->bindValue(':ticket_id', $params['ticketID']);
                        $stmt_ticketdetails->execute();
                        var_dump($stmt_ticketdetails->rowCount());

                        $this->connection->commit();
                        return true;
                    } else {
                        return false;
                    }
                } catch (Exception $e) {
                    echo 'Erro ao decodificar o token: ' . $e->getMessage();
                    return false;
                }
            } else {
                http_response_code(401);
                echo 'Credenciais Inválidas';
                return false;
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }
}