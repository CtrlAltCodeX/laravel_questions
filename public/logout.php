<?php
// Allowed domains list
$allowed_domains = [
    "https://experiment-level-1.blogspot.com",
  	"https://front.online2study.in",
  	"http://localhost:4321"
];

// Get the Origin of the request
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

// Check if the origin is in the allowed list
if (in_array($origin, $allowed_domains)) {
    header("Access-Control-Allow-Origin: $origin");
}

// Additional headers for CORS
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Connect to your database
$servername = "localhost"; // Your DB server
$username = "admin-database"; // Your DB username
$password = "BauUgNM3uXDXt4BexY07"; // Your DB password
$dbname = "admin-database"; // Your DB name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle POST request (user login session creation)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize the input
    $input = json_decode(file_get_contents('php://input'), true);
    if (isset($input['session_id'])) { 
      $sessionId = $input['session_id'];

      // Prepare the DELETE statement
      $stmt = $conn->prepare('DELETE FROM user_sessions WHERE session_id = ?');
      $stmt->bind_param('s', $sessionId);

      // Execute the prepared statement
      if ($stmt->execute()) {
          echo json_encode([
              'success' => true,
              'message' => 'Session deleted successfully.'
          ]);
      } else {
          echo json_encode([
              'success' => false,
              'message' => 'Error deleting session.'
          ]);
      }

      $stmt->close();
  } else {
      echo json_encode([
          'success' => false,
          'message' => 'Session ID not provided.'
      ]);
  }

}


// Close the database connection
$conn->close();
?>