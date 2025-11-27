<?php
include('db.php');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['category']) && !empty($_POST['question_type'])) {
        $category = $_POST['category'];
        $questionType = $_POST['question_type'];

        // Fetch rows from question_cards that match category + question_type
        $stmt = $conn->prepare("SELECT * FROM question_cards WHERE category = ? AND question_type = ?");
        $stmt->bind_param("ss", $category, $questionType);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $cards = [];
            while ($row = $result->fetch_assoc()) {
                $cards[] = $row;
            }
            echo json_encode($cards);
        } else {
            echo json_encode(["message" => "No records found for the given category and question type."]);
        }
        $stmt->close();
    } else {
        echo json_encode(["message" => "Missing 'category' or 'question_type' in POST data."]);
    }
} else {
    echo json_encode(["message" => "Invalid request method. Please use POST."]);
}

$conn->close();
?>
