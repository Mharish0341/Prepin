<?php
include('db.php');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category = $_POST['category'] ?? '';
    $questionType = $_POST['question_type'] ?? '';
    $subCategory = $_POST['sub_category'] ?? '';

    // Build the base SQL
    $sql = "SELECT * FROM questions WHERE category = ? AND question_type = ?";

    // If sub_category is not empty, add that to the WHERE clause
    if (!empty($subCategory)) {
        $sql .= " AND sub_category = ?";
    }

    // Prepare the statement
    if (!empty($subCategory)) {
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $category, $questionType, $subCategory);
    } else {
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $category, $questionType);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $questions = [];
        while ($row = $result->fetch_assoc()) {
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
