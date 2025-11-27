<?php
include('db.php');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if all required fields are present
    if (!empty($_POST['email_address']) && !empty($_POST['password']) && !empty($_POST['confirm_password'])) {
        $email = $_POST['email_address'];
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        // Extract name from email (before '@')
        $name = substr($email, 0, strpos($email, '@'));

        // Check if passwords match
        if ($password !== $confirm_password) {
            echo json_encode(["message" => "Passwords do not match."]);
            exit();
        }

        // Check if email already exists
        $checkQuery = $conn->prepare("SELECT id FROM register WHERE `email address` = ?");
        $checkQuery->bind_param("s", $email);
        $checkQuery->execute();
        $result = $checkQuery->get_result();

        if ($result->num_rows > 0) {
            echo json_encode(["message" => "User already exists."]);
            exit();
        }
        $checkQuery->close();

        // Hash the password before storing it
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Prepare SQL statement to prevent SQL injection
        $stmt = $conn->prepare("INSERT INTO register (`name`, `email address`, `password`) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $hashed_password);

        if ($stmt->execute()) {
            // Get the last inserted user
            $last_id = $stmt->insert_id;
            $stmt->close();

            $query = $conn->prepare("SELECT id, name, `email address` FROM register WHERE id = ?");
            $query->bind_param("i", $last_id);
            $query->execute();
            $result = $query->get_result();

            if ($row = $result->fetch_assoc()) {
                echo json_encode(["message" => "User registered successfully.", "data" => $row]);
            } else {
                echo json_encode(["message" => "Error fetching user details."]);
            }
            $query->close();
        } else {
            echo json_encode(["message" => "Error: " . $stmt->error]);
        }
    } else {
        echo json_encode(["message" => "All fields are required."]);
    }
}
?>
