<?php
include('db.php');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['email_address'])) {
        $email = $_POST['email_address'];
        
        $stmt = $conn->prepare("DELETE FROM register WHERE `email address` = ?");
        $stmt->bind_param("s", $email);
        
        if ($stmt->execute()) {
            echo json_encode(["message" => "User deleted successfully."]);
        } else {
            echo json_encode(["message" => "Error: " . $stmt->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(["message" => "Email address is required to delete a user."]);
    }
} else {
    echo json_encode(["message" => "Invalid request method. Please use POST."]);
}

$conn->close();
?>
