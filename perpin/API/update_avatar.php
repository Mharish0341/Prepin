<?php
include('db.php');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['email_address'])) {
        echo json_encode(["message" => "Email address not provided."]);
        exit;
    }
    
    $email = $_POST['email_address'];
    
    if (!isset($_FILES['avatar'])) {
        echo json_encode(["message" => "No file uploaded."]);
        exit;
    }
    
    $avatar = $_FILES['avatar'];
    $targetDir = "uploads/";
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    // Create a unique file name
    $targetFile = $targetDir . time() . "_" . basename($avatar["name"]);
    
    // Allowed file types
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    $allowedTypes = array("jpg", "jpeg", "png", "gif");
    
    if (!in_array($imageFileType, $allowedTypes)) {
        echo json_encode(["message" => "Invalid file type."]);
        exit;
    }
    
    if (move_uploaded_file($avatar["tmp_name"], $targetFile)) {
        // Update the avatar field in the register table
        $stmt = $conn->prepare("UPDATE register SET avatar = ? WHERE `email address` = ?");
        $stmt->bind_param("ss", $targetFile, $email);
        if ($stmt->execute()) {
            echo json_encode([
                "message" => "Avatar updated successfully.",
                "avatar" => $targetFile
            ]);
        } else {
            echo json_encode(["message" => "Database update failed: " . $stmt->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(["message" => "Failed to upload file."]);
    }
} else {
    echo json_encode(["message" => "Invalid request method. Please use POST."]);
}

$conn->close();
?>
