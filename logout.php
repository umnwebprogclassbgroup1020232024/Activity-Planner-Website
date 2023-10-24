<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/style.css">
    <title>Logout</title>
</head>

<body>

</body>

</html>

<?php
session_start();
if (empty($_SESSION['globalUser']) && empty($_SESSION['globalPswd'])) {
    header("Location: login.php"); // Mengarahkan pengguna ke halaman login.php
    exit;
}
session_destroy();
header("Location: login.php"); // Mengarahkan pengguna ke halaman login.php
exit;
?>