<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List</title>
</head>
<body>
    <div class="heading">
        <h2>To-Do List Application</h2>
    </div>

    <form method="post" action="index.php">
        <input type="text" name="task" class="task_input">
        <button type="submit" class="task_btn" name="submit">Add Task</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>N</th>
                <th>Task</th>
                <th>Action</th>
                <th>Progress Status</th>
            </tr>
        </thead>

        <tbody>
            <tr>
                <td>1</td>
                <td class="task">This is the first task placeholder</td>
                <td class="delete">
                    <a href="#">x</a>
                <td class="progress">
                    <select name="progress" id="progress">
                    <option value="IP">In Progress</option>
                    <option value="WO">Waiting On</option>
                    <option value="NYS">Not Yet Started</option>
                    </select>
                </td>
        </tbody>
</body>
</html>

<?php
// No Groups because lazy. At least there's the bare minimum eh?
?>