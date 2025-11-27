<?php
include('db.php');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (empty($_GET['email_address'])) {
        echo json_encode(["message" => "Email address not provided."]);
        exit;
    }
    
    $email = $_GET['email_address'];
    
    // Fetch name, email address, phone number and avatar from register table.
    $stmt = $conn->prepare("SELECT `name` AS username, `email address` AS email_address, phone_number, avatar FROM register WHERE `email address` = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        echo json_encode($row);
    } else {
        echo json_encode(["message" => "User not found."]);
    }
    
    $stmt->close();
} else {
    echo json_encode(["message" => "Invalid request method. Please use GET."]);
}

$conn->close();
?>
