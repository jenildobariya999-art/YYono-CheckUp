<?php 

// Database Connection
$servername = "localhost";
$username = "lottersi_selimxpro";
$password = "lottersi_selimxpro";
$database = "lottersi_selimxpro";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die(json_encode(["code" => 500, "msg" => "Database connection failed"]));
}

// Whitelisted Domains
$whitelist = [
    "https://82lotter9281.site",
 
];

// Check Origin or Referer
$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';

$allowed = false;

foreach ($whitelist as $allowed_domain) {
    if (strpos($origin, $allowed_domain) === 0 || strpos($referer, $allowed_domain) === 0) {
        $allowed = true;
        break;
    }
}

if (!$allowed) {
    http_response_code(403);
    echo json_encode(["code" => 403, "msg" => "Access denied. Unauthorized domain."]);
    exit;
}

$shonubody = file_get_contents("php://input");
$shonupost = json_decode($shonubody, true);

if ($_SERVER['REQUEST_METHOD'] != 'GET') {
    // Handle Captcha Display
    if (!isset($shonupost['captchaId']) && !isset($shonupost['userPositionx'])) {
        $query = "SELECT captchaId, backgroundImage, sliderImage, correctPositionx FROM captcha_data ORDER BY RAND() LIMIT 1";
        $result = $conn->query($query);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();

            $res['data'] = [
                'captchaId' => $row['captchaId'],
                'backgroundImage' => $row['backgroundImage'],
                'sliderImage' => $row['sliderImage']
            ];
            $res['code'] = 0;
            $res['msg'] = 'success';
            $res['msgCode'] = 0;
            http_response_code(200);
            echo json_encode($res);
            exit;
        } else {
            $res['code'] = 8;
            $res['msg'] = 'No captcha data found';
            $res['msgCode'] = 9;
            http_response_code(404);
            echo json_encode($res);
            exit;
        }
    }

    // Invalid Parameters
    $res['code'] = 7;
    $res['msg'] = 'Invalid request parameters';
    $res['msgCode'] = 6;
    http_response_code(400);
    echo json_encode($res);
    exit;
} else {
    // Method Not Allowed
    http_response_code(405);
    echo json_encode(["code" => 405, "msg" => "Method Not Allowed"]);
    exit;
}
?>
