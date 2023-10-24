<?php

    if(empty($_POST['username']) && empty($_POST['password'])){
        echo("<center><br><h1 style='color: blue;'>You haven't logged in yet...! </h1><h2 style='color: blue;'>Head to the login page!</h2>");
        die("<center><br><a href='login.php'>Login here!</a>");
    }
    else{
        $username = $_POST['username'];
        $password = $_POST['password'];

        include "connection.php";

        $query1 = "SELECT * FROM users WHERE username = '".$username."'";
        $stmt = $conn->query($query1);
        $result = $stmt->fetch();

        if(empty($result)){
            die("<center><br><h1 style='color: blue;'>Error: Username doesn't exist! </h1><h2 style='color: blue;'>Have you created your account?</h2>");
        }
        else{
            if($result['password'] == $password){

                session_start();
                // After successful login, set session variables
                $_SESSION['globalUser'] = $username; // Set the username
                $_SESSION['globalPswd'] = $password; // Set the password or a hashed password


                echo "<center><br /><h1 style='color: blue;'>You have logged in successfully!";
                usleep(3000000);
                header("Location: index.php");
            }
            else{
                die("<center><br /><h1 style='color: blue;'>Error Invalid Password!");
            }
        }
    }
