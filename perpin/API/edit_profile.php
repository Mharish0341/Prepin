<?php
include('db.php');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['email_address'])) {
        echo json_encode(["message" => "Email address is required."]);
        exit();
    }
    
    $email = $_POST['email_address'];
    $name = $_POST['name'] ?? null;
    $nick_name = $_POST['nick_name'] ?? null;
    $phone_number = $_POST['phone_number'] ?? null;
    $country = $_POST['country'] ?? null;
    $genre = $_POST['genre'] ?? null;
    
    $stmt = $conn->prepare("
        UPDATE register 
        SET 
            `name` = ?,
            `nick_name` = ?,
            `phone_number` = ?,
            `country` = ?,
            `genre` = ?
        WHERE `email address` = ?
    ");
    $stmt->bind_param("ssssss", $name, $nick_name, $phone_number, $country, $genre, $email);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Profile updated successfully."]);
    } else {
        echo json_encode(["message" => "Error: " . $stmt->error]);
    }
    $stmt->close();
} else {
    echo json_encode(["message" => "Invalid request method. Please use POST."]);
}

$conn->close();
?>
