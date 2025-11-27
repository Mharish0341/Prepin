<?php
include('db.php');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ensure both OTP and email_address are provided.
    if (!empty($_POST['otp']) && !empty($_POST['email_address'])) {
        $otp = $_POST['otp'];
        $email = $_POST['email_address'];
        
        // Find the record with the given email and OTP.
        $stmt = $conn->prepare("SELECT `email address` FROM register WHERE `email address` = ? AND otp_code = ?");
        $stmt->bind_param("si", $email, $otp);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            // Clear OTP from database
            $clear_stmt = $conn->prepare("UPDATE register SET otp_code = NULL WHERE `email address` = ?");
            $clear_stmt->bind_param("s", $email);
            $clear_stmt->execute();
            $clear_stmt->close();

            echo json_encode(["message" => "OTP verified successfully. Proceed to reset password."]);
        } else {
            echo json_encode(["message" => "Invalid OTP. Please try again."]);
        }
        $stmt->close();
    } else {
        echo json_encode(["message" => "OTP and email are required."]);
    }
} else {
    echo json_encode(["message" => "Invalid request method. Please use POST."]);
}
$conn->close();
?>
