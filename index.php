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
    echo '<center><br /><a href="logout.php">Click here to logout.</a>';
} else {
    echo("<center><br><h1>Error: You haven't logged in yet!</h1>");
    echo "<a href='login.php'>Click here to login!</a>";
    die();

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List</title>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js" integrity="sha256-xLD7nhI62fcsEZK2/v8LsBcb4lG7dgULkuXoXB/j91c=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.css">
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
        $duedate = '0001-01-01T00:00';
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
        <tbody>
             <!--
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
            </tr> -->
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
                        echo '<thead>
                        <tr>
                            <th></th>
                            <th>Task</th>
                            <th>Action</th>
                            <th>Progress Status</th>
                            <th>Due-Date</th>
                        </tr>
                    </thead>';
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
                    echo 'No Tasks? Go touch grass or something.';
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
            handleTaskUpdate($(this));
        });

        // Change event for progress status dropdown
        $('.progress_status').change(function() {
            handleProgressStatusUpdate($(this));
        });

        // Click event for due date cell
        $('.duedate').click(function() {
            handleDueDateUpdate($(this));
        });

        function isValidDatetime(datetime) {
            // Basic validation for "YYYY-MM-DD HH:MM" format
            var regex = /^\d{4}-\d{2}-\d{2} \d{2}:\d{2}$/;
            return regex.test(datetime);
        }

        // Function to update a task
        function handleTaskUpdate($cell) {
            var taskId = $cell.data('taskid');
            var currentValue = $cell.text();
            var newValue = prompt('Enter a new task:', currentValue);

            if (newValue !== null) {
                $cell.text(newValue);
                updateTask('task', taskId, newValue);
            }
        }

        // Function to update progress status
        function handleProgressStatusUpdate($select) {
            var taskId = $select.data('taskid');
            var newValue = $select.val();
            updateTask('progress_status', taskId, newValue);
        }
        // Function to update due date
        function handleDueDateUpdate($cell) {
            var taskId = $cell.data('taskid');
            var newValue = '';
            var currentDueDate = $cell.text();

            // Create an input field
            var $input = $('<input>');
            $input.val(currentDueDate);
            $input.attr('type', 'text'); // Change the input type to text
            $cell.html($input);

            // Initialize the datepicker with timepicker
            $input.datetimepicker({
                dateFormat: 'yy-mm-dd',
                timeFormat: 'HH:mm:ss',
                showTimepicker: true,
                onClose: function () {
                    $input.focus();
                }
            });

            $input.focus();

            $input.on('keydown', function (event) {
                if (event.key === "Enter") {
                    $input.datetimepicker('destroy'); // Remove the picker
                    var newDueDate = $input.val();

                    if (isValidDatetime(newDueDate)) {
                        $cell.text(newDueDate);
                        var newValue = newDueDate;
                        updateTime(taskId, newValue);
                    } else {
                        alert('Invalid datetime format. Please use YYYY-MM-DD HH:MM:SS.');
                    }
                }
            });

            // Function to validate the datetime format
            function isValidDatetime(datetime) {
                var regex = /^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/;
                return regex.test(datetime);
            }
        }
        // Function to update a task using Ajax
        function updateTask(field, taskId, newValue) {
            $.ajax({
                url: 'index.php', // Update the URL to the appropriate script for handling updates
                method: 'POST',
                data: {
                    taskid: taskId,
                    field: field,
                    newvalue: newValue
                },
                success: function(response) {
                    // Handle the response, e.g., show a success message
                    alert("Task edited successfully."); // You can customize this alert
                    console.log(newValue);
                },
                error: function(response) {
                    alert("Task was not updated!");
                }
            });
        }
    });

    function updateTime(taskId, newValue) {
        $.ajax({
            url: 'index.php',
            method: 'POST',
            data: {
                taskId: taskId,
                newvalue: newValue
            },
            success: function (response) {
                alert("Time updated successfully.");
                console.log(newValue);
            },
            error: function (response) {
                alert("Time was not updated!");
            }
        });
    }



    // Click event for Delete checkboxes
    $('.delete-task').change(function() {
        var $checkbox = $(this);
        var taskId = $checkbox.data('taskid');
        if ($checkbox.is(':checked')) {
            if (confirm('Are you sure you want to finish task?')) {
                deleteTask(taskId, function() {
                    // Remove the task row from the table on successful deletion
                    $checkbox.closest('tr').remove();
                    location.reload(); // Refresh the page
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
                    if (response === 'Deleted successfully.') {
                        successCallback();
                    }
                },
                error: function() {
                    alert('Error deleting task.');
                }
            });
        }
    </script>
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Handle POST requests (updates)
        if (isset($_POST['taskid']) && isset($_POST['field']) && isset($_POST['newvalue'])) {
            $taskId = $_POST['taskid'];
            $field = $_POST['field'];
            $newValue = $_POST['newvalue'];

            // Define the field name to update in the SQL query
            $fieldToUpdate = NULL;
            if ($field === 'task') {
                $fieldToUpdate = 'task';
            } 
            elseif ($field === 'progress_status') {
                // Handle the 'completed' field
                $fieldToUpdate = 'progress_status';
            } 
            elseif ($field === 'duedate') {
                // Handle the 'duedate' field differently
                $fieldToUpdate = 'duedate';
                if (!isValidDatetime($newValue)) {
                    echo "Invalid datetime format.";
                    return;
                }
            }


            // Update the task in the database (you should add error handling)
            $updateQuery = "UPDATE tasks SET $fieldToUpdate = :newvalue WHERE taskid = :taskid";
            $stmt = $conn->prepare($updateQuery);
            $stmt->bindParam(':newvalue', $newValue, PDO::PARAM_STR); // Treat newValue as a string
            $stmt->bindParam(':taskid', $taskId, PDO::PARAM_INT);
            
            var_dump($newValue);

            if ($stmt->execute()) {
                // Success message (you can customize this)
                echo "Task updated successfully.";
                var_dump($stmt->execute());
            } else {
                // Error message (you can customize this)
                echo "Error updating task.";
            }
        } elseif (isset($_POST['taskid']) && isset($_POST['newValue'])){
            $taskId = $_POST['taskid'];
            $newValue = $_POST['newvalue'];

            $updateQuery = "UPDATE tasks SET duedate = :newvalue WHERE taskid = :taskid";
            $stmt = $conn->prepare($updateQuery);
            $stmt->bindParam(':newvalue', $newValue, PDO::PARAM_STR); // Treat date as as string
            $stmt->bindParam(':taskid', $taskId, PDO::PARAM_INT);

            if ($stmt->execute()) {
                echo "Task duedate updated successfully.";
                echo "SQL Error: " . print_r($stmt->errorInfo(), true);
            }
        }
    }
    ?>
</body>
</html>