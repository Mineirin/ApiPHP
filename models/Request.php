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
}