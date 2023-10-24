<?php
session_start();
if (!empty($_SESSION['globalUser']) && !empty($_SESSION['globalPswd'])) {
    echo '<center><br /> Welcome, ' . $_SESSION['globalUser'] . ' ';
} else {
    die("<center><br><h1>You haven't logged in yet!</h1>");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account</title>
</head>

<body>

</body>

</html>