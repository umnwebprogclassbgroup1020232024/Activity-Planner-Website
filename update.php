<?php
require 'connection.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve POST data
    $taskId = $_POST['taskid'];
    $field = $_POST['field'];
    $newValue = $_POST['newvalue'];

    // Update the task in the database
    $query("UPDATE tasks SET $field = :newValue WHERE taskid = :taskId");
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':newValue', $newValue, PDO::PARAM_STR);
    $stmt->bindParam(':taskId', $taskId, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->execute();

    if ($result) {
        echo "Task updated successfully";
    } else {
        echo "Error updating task";
    }
} else {
    echo "Invalid request method";
}
?>