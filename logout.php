<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout</title>
</head>
<body>
    
</body>
</html>

<?php
    session_start();
    if(empty($_SESSION['globalUser']) && empty($_SESSION['globalPswd'])){
        die("<center><br><h1 style='color: limegreen;'>Error: You haven't logged in yet...! <h1/><h2 style='color: lime;'>Head up towards the login page.</h2><br><a href='login.php'><button style='background-color: lime;border-radius: 20px; width: 80px; height: 30px'>Login</button></a></center>");
    }
    session_destroy();
    die("<center><br><h1 style='color: limegreen;'>Logged out successfully...!<h1/><h2 style='color: lime;'>In case you wanna login or signup again, no worries. Just hit the button below.</h2><br><a href='login.php'><button style='background-color: lime;border-radius: 20px; width: 80px; height: 30px; margin: 10px;'>Login</button></a><a href='signup.php'><button style='background-color: lime;border-radius: 20px; width: 80px; height: 30px; margin: 10px;'>Sign-Up</button></a></center>");
?>