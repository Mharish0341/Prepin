<?php
include('db.php');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // If a card_id is passed, fetch only the questions for that card.
    if (!empty($_GET['card_id'])) {
        $card_id = $_GET['card_id'];
        $stmt = $conn->prepare("SELECT * FROM questions WHERE card_id = ?");
        $stmt->bind_param("i", $card_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $allQuestions = [];
            while ($row = $result->fetch_assoc()) {
                $allQuestions[] = $row;
            }
            echo json_encode($allQuestions);
        } else {
            echo json_encode(["message" => "No questions found for card_id = $card_id"]);
        }
        $stmt->close();

    // If an id is passed, fetch only that one question
    } else if (!empty($_GET['id'])) {
        $id = $_GET['id'];
        $stmt = $conn->prepare("SELECT * FROM questions WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo json_encode($result->fetch_assoc());
        } else {
            echo json_encode(["message" => "No record found with ID: $id"]);
        }
        $stmt->close();

    // Otherwise, fetch all questions
    } else {
        $sql = "SELECT * FROM questions";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $allQuestions = [];
            while ($row = $result->fetch_assoc()) {
                $allQuestions[] = $row;
            }
            echo json_encode($allQuestions);
        } else {
            echo json_encode(["message" => "No records found."]);
        }
    }
} else {
    echo json_encode(["message" => "Invalid request method. Please use GET."]);
}

$conn->close();
?>
