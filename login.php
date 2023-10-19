<?php
    if(!empty($_POST['username']) && !empty($_POST['password'])){

        $username = $_POST['username'];
        $password = $_POST['password'];

        include "connection.php";

        $query1 = "SELECT * FROM users WHERE username='".$username."'";
        $stmt = $conn->query($query1);
        $result = $stmt->fetch();
        
        if(!empty($result)){
             
            if($result['username']==$username){
                
                die("<center><br><h1 style='color: blue;'>Error:  Account already exist...! <h1/><h2 style='color: blue;'>please try creating account with another username.</h2><br><a href='signup.php'><button style='background-color: blue;border-radius: 20px; width: 80px; height: 30px'>Sign-up</button></a></center>");
            }
        }
        else{
            $insertQuery = "INSERT INTO users(username, password) VALUES('".$username."','".$password."')";
            $conn->query($insertQuery);   
            echo "<center><h1 style='color: blue;'>Account Created Successfully...!</h1></center>" ;
        }
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body align="center">
    <div id="border">
    <h1>Login</h1><br>
    <form action="login-check.php" method="POST">
    <label>Username: </label><input name="username" type="text"><br><br>
        <label>Password: </label><input name="password" type="password"><br><br>
        <input type="submit" id="btn" value="Login">
    </form>
    </div>
</body>