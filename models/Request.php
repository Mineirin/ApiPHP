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
}