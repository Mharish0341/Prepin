<?php
// Disable error reporting so warnings don't break JSON output
error_reporting(0);
ini_set('display_errors','0');

include('db.php');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category    = $_POST['category'] ?? '';
    $cardTitle   = $_POST['card_title'] ?? '';
    $questionType = $_POST['question_type'] ?? '';

    if (empty($category) || empty($cardTitle)) {
        echo json_encode(["message" => "Missing 'category' or 'card_title' in POST data."]);
        exit();
    }

    // We'll SELECT q.* which includes q.answer (the Q/A answer) 
    $sql = "
        SELECT q.*
        FROM questions q
        JOIN question_cards c ON q.card_id = c.id
        WHERE c.category = ? 
          AND c.card_title = ?
    ";

    // If category=Technical and questionType is not empty, filter by question_type
    if ($category === 'Technical' && !empty($questionType)) {
        $sql .= " AND c.question_type = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $category, $cardTitle, $questionType);
    } else {
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $category, $cardTitle);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $questions = [];
        while ($row = $result->fetch_assoc()) {
            // $row now contains 'answer' (for Q/A), 'correct_answer', etc.
            $questions[] = $row;
        }
        echo json_encode($questions);
    } else {
        echo json_encode(["message" => "No questions found."]);
    }
    $stmt->close();
} else {
    echo json_encode(["message" => "Invalid request method. Please use POST."]);
}

$conn->close();
?>
