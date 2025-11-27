<?php
include('db.php'); // Ensure this file sets up your $conn connection
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (empty($_GET['email_address'])) {
        echo json_encode(["message" => "Email address not provided."]);
        exit;
    }
    
    $email = $_GET['email_address'];
    
    // Retrieve the progress_history column for the user using the provided email address.
    $stmt = $conn->prepare("SELECT progress_history FROM register WHERE `email address` = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $progress_json = $row['progress_history'];
        // If progress_history is not empty, return it; otherwise, return an empty array.
        if (!empty($progress_json)) {
            echo $progress_json;
        } else {
            echo json_encode([]);
        }
    } else {
        echo json_encode(["message" => "User record not found."]);
    }
    
    $stmt->close();
} else {
    echo json_encode(["message" => "Invalid request method. Please use GET."]);
}

$conn->close();
?>
