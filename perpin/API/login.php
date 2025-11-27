<?php
include('db.php');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['email_address']) && !empty($_POST['password'])) {
        $email = $_POST['email_address'];
        $password = $_POST['password'];

        $stmt = $conn->prepare("SELECT id, `email address`, `password` FROM register WHERE `email address` = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            // Verify the password
            if (password_verify($password, $row['password'])) {
                echo json_encode([
                    "message" => "Login successful.",
                    "user_id" => $row['id'],
                    "email"   => $row['email address']
                ]);
            } else {
                echo json_encode(["message" => "Invalid password."]);
            }
        } else {
            echo json_encode(["message" => "User not found."]);
        }

        $stmt->close();
    } else {
        echo json_encode(["message" => "Email and password are required."]);
    }
}
?>
