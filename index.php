<?php
session_start();

require 'connection.php';

if (isset($_SESSION['globalUser'])) {
    $username = $_SESSION['globalUser'];
    // Query the database to fetch the user ID based on the username
    $query = "SELECT userid FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(1, $username, PDO::PARAM_STR);
    $stmt->execute();
    $userId = $stmt->fetch(PDO::FETCH_COLUMN);
}

if (!empty($_SESSION['globalUser']) && !empty($_SESSION['globalPswd'])) {
    echo '<center><br /> Welcome, ' . $_SESSION['globalUser'] . ' ';
} else {
    die("<center><br><h1>Error: You haven't logged in yet!</h1>");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List</title>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
</head>
<body>
    <div class="heading">
        <h2>To-Do List Application</h2>
    </div>

    <form method="post" action="index.php">
        <input type="text" name="task" class="task_input">
        <button type="submit" class="task_btn" name="submit">Add Task</button>
    </form>

    <?php
    if (isset($_POST['submit'])) {
        $taskDescription = $_POST['task'];

        $query = "SELECT MAX(taskid) FROM tasks WHERE userid = ?";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(1, $userId, PDO::PARAM_INT);
        $stmt->execute();
        $maxTaskId = $stmt->fetchColumn();
        $taskid = ($maxTaskId !== null) ? ($maxTaskId + 1) : 0;
        $duedate = '2023-10-19 07:55:00'; // Corrected the format
        $progressStatus = 'NYS';

        // Insert the task into the database
        $query = "INSERT INTO tasks (taskid, task, duedate, progress_status, userid) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(1, $taskid, PDO::PARAM_INT);
        $stmt->bindParam(2, $taskDescription, PDO::PARAM_STR);
        $stmt->bindParam(3, $duedate, PDO::PARAM_STR); // Set as string
        $stmt->bindParam(4, $progressStatus, PDO::PARAM_STR); // Set as string
        $stmt->bindParam(5, $userId, PDO::PARAM_INT);

        if ($stmt->execute()) {
            header('Location: index.php');
            exit; // Ensure no further code is executed
        } else {
            // Task insertion failed, handle the error or display an error message
            echo "Error adding task.";
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Handle POST requests (updates and deletions)
        if (isset($_POST['taskid'])) {
            $taskId = $_POST['taskid'];
            
            if (isset($_POST['delete'])) {
                // Handle task deletion
                $query = "DELETE FROM tasks WHERE taskid = ? AND userid = ?";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(1, $taskId, PDO::PARAM_INT);
                $stmt->bindParam(2, $userId, PDO::PARAM_INT);

                if ($stmt->execute()) {
                    // Task deleted successfully
                } else {
                    // Handle task deletion error
                    echo "Error deleting task.";
                }
            } elseif (isset($_POST['progress_status'])) {
                // Handle progress status update
                $progressStatus = $_POST['progress_status'];
                $query = "UPDATE tasks SET progress_status = ? WHERE taskid = ? AND userid = ?";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(1, $progressStatus, PDO::PARAM_STR);
                $stmt->bindParam(2, $taskId, PDO::PARAM_INT);
                $stmt->bindParam(3, $userId, PDO::PARAM_INT);

                if ($stmt->execute()) {
                    // Progress status updated successfully
                } else {
                    // Handle progress status update error
                    echo "Error updating progress status.";
                }
            }
        }
    }
    ?>
    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>Task</th>
                <th>Action</th>
                <th>Progress Status</th>
                <th>Due-Date</th>
            </tr>
        </thead>
        <tbody>
            <!-- Sample Table Row-->
            <tr>
                <td>1</td>
                <td class="task" data-taskid="0">This is the first task placeholder</td>
                <td class="delete">
                    <input type="checkbox" name="delete" class="delete-task" data-taskid="0">
                </td>
                <td class="progress">
                    <select name="progress" class="progress_status" data-taskid="0">
                        <option value="IP">In Progress</option>
                        <option value="WO">Waiting On</option>
                        <option value="NYS">Not Yet Started</option>
                    </select>
                </td>
                <td class="duedate">
                    <time datetime="2023-10-19 07:55:00">
                        2023-10-19 07:55
                    </time>
                </td>
            </tr>
            <?php
            try {
                $username = $_SESSION['globalUser'];
                $query = "SELECT userid FROM users WHERE username = :username";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':username', $username, PDO::PARAM_STR);
                $stmt->execute();
                $userId = $stmt->fetch(PDO::FETCH_COLUMN);

                $query = "SELECT * FROM tasks WHERE userID = ?";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(1, $userId, PDO::PARAM_INT);
                $stmt->execute();
                $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (count($tasks) > 0) {
                    // Loop through the tasks and generate table rows
                    foreach ($tasks as $task) {
                        echo '<tr>';
                        echo '<td></td>';
                        echo '<td class="task" data-taskid="' . $task['taskid'] . '">' . $task['task'] . '</td>';
                        echo '<td class="delete">';
                        echo '<input type="checkbox" name="delete" class="delete-task" data-taskid="' . $task['taskid'] . '">';
                        echo '</td>';
                        echo '<td class="progress">';
                        echo '<select name="progress_status" class="progress_status" data-taskid="' . $task['taskid'] . '">';
                        echo '<option value="IP" ' . ($task['progress_status'] == 'IP' ? 'selected' : '') . '>In Progress</option>';
                        echo '<option value="WO" ' . ($task['progress_status'] == 'WO' ? 'selected' : '') . '>Waiting On</option>';
                        echo '<option value="NYS" ' . ($task['progress_status'] == 'NYS' ? 'selected' : '') . '>Not Yet Started</option>';
                        echo '</select>';
                        echo '</td>';
                        echo '<td class="duedate">';
                        echo '<time datetime="' . $task['duedate'] . '">' . $task['duedate'] . '</time>';
                        echo '</td>';
                        echo '</tr>';
                    }
                } else {
                    echo 'No tasks found for the user.';
                }
            } catch (PDOException $e) {
                echo "Database Error: " . $e->getMessage();
            }
            ?>
        </tbody>
    </table>

    <!-- JQuery Script for Updating and Deleting Tasks -->
    <script>
    $(document).ready(function() {
        // Double-click event for task
        $('.task').dblclick(function() {
            var $cell = $(this);
            var currentValue = $cell.text();
            var $input = $('<input>');
            $input.val(currentValue);
            $cell.html($input);

            $input.focus();
            $input.blur(function() {
                var newValue = $input.val();
                $cell.text(newValue);
                updateTask($cell.data('taskid'), 'task', newValue);
            });
        });

        // Change event for progress status dropdown
        $('.progress_status').change(function() {
            var $select = $(this);
            var newValue = $select.val();
            var $cell = $select.closest('td');

            // Update the selected option without overwriting the dropdown
            $cell.find('select').val(newValue);

            // Use AJAX to update the value in the database
            updateTask($cell.data('taskid'), 'progress_status', newValue);


        });

        // Click event for due date cell
        $('.duedate').click(function() {
            var $cell = $(this);
            var currentDueDate = $cell.text();
            var newDueDate = prompt('Enter a new due date (YYYY-MM-DD HH:MM):', currentDueDate);
            if (newDueDate !== null) {
                if (isValidDatetime(newDueDate)) {
                    $cell.text(newDueDate);
                    updateTask($cell.data('taskid'), 'duedate', newDueDate);
                } else {
                    alert('Invalid datetime format. Please use YYYY-MM-DD HH:MM.');
                }
            }
        });

        function isValidDatetime(datetime) {
            // Basic validation for "YYYY-MM-DD HH:MM" format
            var regex = /^\d{4}-\d{2}-\d{2} \d{2}:\d{2}$/;
            return regex.test(datetime);
        }

        // Function to update a task using Ajax
        function updateTask(taskId, field, newValue) {
            $.ajax({
                url: 'index.php',
                method: 'POST',
                data: {
                    taskid: taskId,
                    field: field,
                    newvalue: newValue
                },
                success: function(response) {
                    // Handle the response, e.g., show a success message
                    alert(response); // You can customize this alert
                    console.log(newValue);
                },
                error: function() {
                    // Handle errors
                }
            });
        }
    });

        // Click event for Delete checkboxes
        $('.delete-task').change(function() {
            var $checkbox = $(this);
            var taskId = $checkbox.data('taskid');
            if ($checkbox.is(':checked')) {
                if (confirm('Are you sure you want to delete this task?')) {
                    deleteTask(taskId, function() {
                        // Remove the task row from the table on successful deletion
                        $checkbox.closest('tr').remove();
                    });
                } else {
                    $checkbox.prop('checked', false); // Uncheck the checkbox if deletion is canceled
                }
            }
        });

        // Function to delete a task using AJAX
        function deleteTask(taskId, successCallback) {
            $.ajax({
                url: 'index.php',
                method: 'POST',
                data: {
                    taskid: taskId,
                    delete: 1 // This signals the server to delete the task
                },
                success: function(response) {
                    if (response === 'success.') {
                        successCallback();
                    }
                },
                error: function() {
                    alert('Error deleting task.');
                }
            });
        }
    </script>

    <!-- Function to validate datetime (adjust this as needed) -->
    <script>
    function isValidDatetime(datetime) {
        // Basic validation for "YYYY-MM-DD HH:MM" format
        var regex = /^\d{4}-\d{2}-\d{2} \d{2}:\d{2}$/;
        return regex.test(datetime);
    }
    </script>
</body>
</html>
