<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
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
    echo '';
} else {
    echo("<center><h1>Error: You haven't logged in yet!</h1>");
    echo("<h5 class='my-2'>Either Login or Sign-Up yeah?</h5>");
    echo('<div class="d-flex justify-content-center">
    <a href="login.php" class="btn btn-primary mx-3">Login</a>
    <a href="signup.php" class="btn btn-primary mx-3">Sign-Up</a>
    </div>');
    die();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link href="style.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
</head>
<body>

<nav class="navbar bg-primary rounded-md">
  <div class="container-fluid">
    <h2 class="text-start" style="color: white;">
      To-Do List Application
    </h2>
    <p>
      <img id="profile_pic" src="assets\DefUser.png" alt="Logo" width="64" height="64" class="mt-4 d-inline-block align-text-top">
    </p>
  </div>
</nav>
    
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header text-center justify-content-center">
                <h5 class="modal-title" id="modalLabel"><?php echo $username; ?></h5>
            </div>
            <div class="modal-body text-center">
                <p>Hello, <?php echo $username; ?>! Do you want to logout?</p>
            </div>
            <div class="modal-footer text-center justify-content-center">
                <div class="d-flex justify-content-center">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="window.location.href='logout.php'">Logout</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        // When the image with ID "profile_pic" is clicked, show the modal
        $("#profile_pic").on("click", function () {
            $("#myModal").modal("show");
        });
    });
</script>

    <div class="container mt-5">
        <form method="post" action="index.php">
            <div class="input-group mb-3">
                <input type="text" name="task" class="form-control" placeholder="Add a task">
                <div class="input-group-append mx-4">
                    <button type="submit" class="btn btn-primary" name="submit">Add Task</button>
                </div>
            </div>
        </form>
    <?php
    // Insertion Function Script
    if (isset($_POST['submit'])) {
        $taskDescription = $_POST['task'];

        $query = "SELECT MAX(taskid) FROM tasks WHERE userid = ?";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(1, $userId, PDO::PARAM_INT);
        $stmt->execute();
        $maxTaskId = $stmt->fetchColumn();
        $taskid = ($maxTaskId !== null) ? ($maxTaskId + 1) : 0;
        $duedate = new DateTime();  // Create a DateTime object with the current date and time
        $duedate->add(new DateInterval('P7D'));  // Add 7 days
        $duedate->setTime(23, 59, 59);  // Set the time to 23:59:59
        $duedate_str = $duedate->format('Y-m-d H:i:s');  // Format as 'YYYY-MM-DD HH:MM:SS'
        $progressStatus = 'NYS';

        // Insert the task into the database
        $query = "INSERT INTO tasks (taskid, task, duedate, progress_status, userid) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(1, $taskid, PDO::PARAM_INT);
        $stmt->bindParam(2, $taskDescription, PDO::PARAM_STR);
        $stmt->bindParam(3, $duedate_str, PDO::PARAM_STR); // Set as string
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

    // Delete Function Script
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
            } 
        }
    }
    
    // Updating Function Script
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve POST data
    $taskId = $_POST['taskid'];
    $field = $_POST['field'];
    $newValue = $_POST['newvalue'];

    // Update the task in the database
    $query = "UPDATE tasks SET $field = :newValue WHERE taskid = :taskId";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':newValue', $newValue, PDO::PARAM_STR);
    $stmt->bindParam(':taskId', $taskId, PDO::PARAM_INT);
    $stmt->execute(); // Execute the statement once
    $result = $stmt->rowCount(); // Check the number of affected rows if needed

    if ($result) {
        echo "Task updated successfully";
    } else {
        echo "Error updating task";
    }
}
    ?>

    <div class="table-responsive">
        <table class="table table-bordered">
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
                    $i = 1;

                    if (count($tasks) > 0) {
                        // Loop through the tasks and generate table rows
                        foreach ($tasks as $task) {
                            echo '<tr>';
                            echo '<td>'. $i . '.' . '</td>';
                            echo '<td class="task" data-taskid="' . $task['taskid'] . '">' . $task['task'] . '</td>';
                            echo '<td class="delete">';
                            echo '<input type="checkbox" name="delete" class="delete-task" data-taskid="' . $task['taskid'] . '">';
                            echo '</td>';
                            echo '<td class="progress" style="height: 60px;">';
                            echo '<select name="progress_status" class="form-control progress_status" data-taskid="' . $task['taskid'] . '">';
                            echo '<option value="IP" ' . ($task['progress_status'] == 'IP' ? 'selected' : '') . '>In Progress</option>';
                            echo '<option value="WO" ' . ($task['progress_status'] == 'WO' ? 'selected' : '') . '>Waiting On</option>';
                            echo '<option value="NYS" ' . ($task['progress_status'] == 'NYS' ? 'selected' : '') . '>Not Yet Started</option>';
                            echo '</select>';
                            echo '</td>';
                            echo '<td class="duedate">';
                            echo '<time datetime="' . $task['duedate'] . '">' . $task['duedate'] . '</time>';
                            echo '</td>';
                            echo '</tr>';
                            $i++;
                        }
                    } else {
                        echo "<p class='text-center my-2'><strong>No tasks found for the user " . $username .".</strong></p>";
                    }
                } catch (PDOException $e) {
                    echo "Database Error: " . $e->getMessage();
                }                         
                ?>
                </tbody>
        </table>
    </div>
    </div>
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
                if (newValue !== currentValue) {
                    $cell.text(newValue);
                    updateTask($cell.data('taskid'), 'task', newValue);
                } else {
                    alert("Task not updated, same values!");
                    $cell.text(currentValue);
                }
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
                if (newDueDate !== currentDueDate) {
                    $cell.text(newDueDate);
                    updateTask($cell.data('taskid'), 'duedate', newDueDate);
                } else {
                    alert("Task not updated, same values!");
                    updateTask($cell.data('taskid'), 'duedate', currentDueDate);
                }
            }
        });

        function isValidDatetime(datetime) {
            // Basic validation for "YYYY-MM-DD HH:MM" format. Shout-out to all the dosen Automata out there!!!!
            var regex = /^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/;
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
                    console.log(newValue);
                    alert("Task updated sucessfully."); // You can customize this alert
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
                        successCallback("Good job!");
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
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
</body>
</html>
