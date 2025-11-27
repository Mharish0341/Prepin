<?php
// 1. Include your database connection file
include('db.php');

// 2. Set the response type to JSON
header('Content-Type: application/json');

// 3. Ensure we're using a GET request
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // 4. Check if 'card_id' is provided
    if (!empty($_GET['card_id'])) {
        $card_id = $_GET['card_id'];


        $stmt = $conn->prepare("SELECT * FROM questions WHERE card_id = ?");
        $stmt->bind_param("i", $card_id);
        $stmt->execute();
        
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $questions = [];
            while ($row = $result->fetch_assoc()) {
                $questions[] = $row;
            }
            echo json_encode($questions);
        } else {
            echo json_encode(["message" => "No questions found for card_id = $card_id"]);
        }

        $stmt->close();
    } else {
        echo json_encode(["message" => "Missing 'card_id' in query parameters."]);
    }
} else {
    echo json_encode(["message" => "Invalid request method. Please use GET."]);
}

$conn->close();
?>
