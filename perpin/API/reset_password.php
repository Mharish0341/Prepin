<?php
include('db.php');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['new_password']) && !empty($_POST['confirm_password']) && !empty($_POST['email_address'])) {
        $email = $_POST['email_address'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        // Check if passwords match
        if ($new_password !== $confirm_password) {
            echo json_encode(["message" => "Passwords do not match."]);
            exit();
        }

        // Hash the new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Update the password in the database
        $update_stmt = $conn->prepare("UPDATE register SET password = ? WHERE `email address` = ?");
        $update_stmt->bind_param("ss", $hashed_password, $email);

        if ($update_stmt->execute()) {
            echo json_encode(["message" => "Password reset successful. You can now log in."]);
        } else {
            echo json_encode(["message" => "Error resetting password. Please try again."]);
        }

        $update_stmt->close();
    } else {
        echo json_encode(["message" => "Email, new password and confirm password are required."]);
    }
}
$conn->close();
?>
