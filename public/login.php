<?php

$allowed_domains = [
    "https://experiment-level-1.blogspot.com",
    "https://cbt.ncvtonline.com",
  	"https://front.online2study.in",
  	"http://localhost:4321"
];

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $allowed_domains)) {
    header("Access-Control-Allow-Origin: $origin");
}
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");

$servername = "localhost";
$username   = "admin-database";
$password   = "BauUgNM3uXDXt4BexY07";
$dbname     = "admin-database";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

$input = json_decode(file_get_contents("php://input"), true);
if (!isset($input['uid'], $input['email'], $input['name'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    $conn->close();
    exit;
}

$email = $input['email'];
$name  = $input['name'];

try {
    // 1. Check if user exists
    $stmt = $conn->prepare("SELECT id FROM google_users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($googleUserId);
        $stmt->fetch();
    } else {
       $stmt->close();
      	
      $welcomeStmt = $conn->prepare("SELECT welcome_coin FROM settings LIMIT 1");
      $welcomeStmt->execute();
      $welcomeStmt->bind_result($welcomeCoin);
      $welcomeStmt->fetch();
      $welcomeStmt->close();
	
      // Make sure $name is not null or empty
      if (!empty($name)) {
          $prefix = substr(strtolower($name), 0, 5); // first 4 letters, lowercase
      } else {
          $prefix = 'user'; // fallback prefix
      }

      $randomNumber = mt_rand(10000, 99999); // 5-digit random number
      $referralCode = strtoupper($prefix . $randomNumber); // Combine and uppercase
      
      $stmt = $conn->prepare("
           INSERT INTO google_users (name, email, login_type, referral_code, coins, created_at, updated_at) 
           VALUES (?, ?, 'google', ?, ?, NOW(), NOW())
      ");
      $stmt->bind_param("sssi", $name, $email, $referralCode, $welcomeCoin);
      $stmt->execute();
      $googleUserId = $stmt->insert_id;
    }
  
    $stmt->close();

    // 2. Generate new session
    $sessionId = bin2hex(random_bytes(16));
    $stmt = $conn->prepare("INSERT INTO user_sessions (google_users_id, session_id, created_at, updated_at) VALUES (?, ?, NOW(), NOW())");
    $stmt->bind_param("is", $googleUserId, $sessionId);
    $success = $stmt->execute();
    $stmt->close();

    if ($success) {
        echo json_encode([
            'success' => true,
            'sessionId' => $sessionId,
            'id' => $googleUserId
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Session creation failed']);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

$conn->close();
