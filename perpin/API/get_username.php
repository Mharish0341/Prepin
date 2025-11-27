<?php
include('db.php'); // Ensure this sets up your $conn connection
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (empty($_GET['email_address'])) {
        echo json_encode(["message" => "Email address not provided."]);
        exit;
    }
    
    $email = $_GET['email_address'];
    
    // Fetch the "name" column from register, alias it as "username".
    $stmt = $conn->prepare("SELECT `name` AS username FROM register WHERE `email address` = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        echo json_encode(["username" => $row['username']]);
    } else {
        echo json_encode(["message" => "User not found."]);
    }
    
    $stmt->close();
} else {
    echo json_encode(["message" => "Invalid request method. Please use GET."]);
}

$conn->close();
?>
