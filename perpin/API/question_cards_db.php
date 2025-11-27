<?php
// Disable errors so that warnings don't pollute the JSON response
error_reporting(0);
ini_set('display_errors','0');

include('db.php'); // Ensure $conn is defined here
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category = $_POST['category'] ?? '';
    $questionType = $_POST['question_type'] ?? '';

    if (empty($category)) {
        echo json_encode(["message" => "Missing 'category' in POST data."]);
        exit();
    }

    if ($category === 'Technical' && !empty($questionType)) {
        $stmt = $conn->prepare("
            SELECT card_title, question_type 
            FROM question_cards 
            WHERE category = ? AND question_type = ?
        ");
        $stmt->bind_param("ss", $category, $questionType);
    } else {
        $stmt = $conn->prepare("
            SELECT card_title, question_type 
            FROM question_cards 
            WHERE category = ?
        ");
        $stmt->bind_param("s", $category);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $cards = [];
        while ($row = $result->fetch_assoc()) {
            $cards[] = $row;
        }
        echo json_encode($cards);
    } else {
        echo json_encode(["message" => "No card titles found for this category."]);
    }
    $stmt->close();
} else {
    echo json_encode(["message" => "Invalid request method. Please use POST."]);
}

$conn->close();
?>
