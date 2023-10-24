<?php
if (!empty($_POST['username']) && !empty($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    include "connection.php";

    $query1 = "SELECT * FROM users WHERE username = :username";
    $stmt = $conn->prepare($query1);

    $stmt->bindParam(':username', $username, PDO::PARAM_STR);

    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!empty($result)) {
        if ($result['username'] === $username) {
            die("<center><br><h1 style='color: blue;'>Error: Account already exists...! <h1/><h2 style='color: blue;'>Please try creating an account with another username.</h2><br><a href='signup.php'><button style='background-color: blue;border-radius: 20px; width: 80px; height: 30px'>Sign-up</button></a></center>");
        }
    } else {
        $insertQuery = "INSERT INTO users (username, password) VALUES (:username, :password)";
        $stmt = $conn->prepare($insertQuery);

        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->bindParam(':password', $password, PDO::PARAM_STR);

        $stmt->execute();

        echo "<center><h1 style='color: blue;'>Account Created Successfully...!</h1></center>";
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>
<body align="center">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h1 class="text-center">Login</h1>
                    </div>
                    <div class="card-body">
                        <form action="login-check.php" method="POST">
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" class="form-control" name="username">
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" name="password">
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary mt-4">Login</button>
                            </div>
                        </form>
                    </div>
                    <div class="text-center my-4 card-footer">
                        <a href="signup.php" style="color: black;">No Account? Register Here!</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

