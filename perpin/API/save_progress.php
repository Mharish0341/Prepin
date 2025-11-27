<?php
include('db.php');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Expect the email_address to be passed in the POST data.
    if (empty($_POST['email_address'])) {
        echo json_encode(["message" => "Email address not provided."]);
        exit;
    }
    
    $email = $_POST['email_address'];

    // Expect POST parameters: round_type, question_type, score, total_questions, and optionally questions_attended.
    if (!empty($_POST['round_type']) && !empty($_POST['question_type']) && isset($_POST['score']) && !empty($_POST['total_questions'])) {
        $round_type         = $_POST['round_type'];
        $question_type      = $_POST['question_type'];
        $score              = (float) $_POST['score'];
        $total_questions    = (int) $_POST['total_questions'];
        $questions_attended = isset($_POST['questions_attended']) ? (int)$_POST['questions_attended'] : null;
        
        // 1. Fetch existing progress_history for this user based on email.
        $stmt = $conn->prepare("SELECT progress_history FROM register WHERE `email address` = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            $progress_json = $row['progress_history'];
            
            // 2. Decode the JSON or initialize an empty array if it is NULL or empty.
            if (!empty($progress_json)) {
                $progress_array = json_decode($progress_json, true);
                if (!is_array($progress_array)) {
                    $progress_array = [];
                }
            } else {
                $progress_array = [];
            }
            
            // 3. Append new attempt with the provided round details.
            $new_attempt = [
                "round_type"        => $round_type,
                "question_type"     => $question_type,
                "score"             => $score,
                "total_questions"   => $total_questions,
                "questions_attended"=> $questions_attended,
                "timestamp"         => date("Y-m-d H:i:s")
            ];
            $progress_array[] = $new_attempt;
            
            // 4. Re-encode the array to JSON.
            $new_json = json_encode($progress_array);
            
            // 5. Update the user's progress_history in the register table.
            $update_stmt = $conn->prepare("UPDATE register SET progress_history = ? WHERE `email address` = ?");
            $update_stmt->bind_param("ss", $new_json, $email);
            
            if ($update_stmt->execute()) {
                echo json_encode(["message" => "Round progress saved successfully."]);
            } else {
                echo json_encode(["error" => $conn->error]);
            }
            
            $update_stmt->close();
        } else {
            echo json_encode(["message" => "User record not found."]);
        }
        $stmt->close();
    } else {
        echo json_encode(["message" => "Missing round_type, question_type, score, or total_questions in POST data."]);
    }
} else {
    echo json_encode(["message" => "Invalid request method. Please use POST."]);
}

$conn->close();
?>
