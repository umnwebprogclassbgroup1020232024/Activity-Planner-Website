<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign-Up</title>
</head>
<body align="center">
    <div id="border">
        <h1>Signup</h1><br />
        <form action="login.php" method="POST">
            <label>Username </label><input name="username" type="text" required><br /><br />
            <label>Password </label><input name="password" type="password" required><br /><br />
            <input type="submit" id="btn" value="Sign-Up">
        </form>
        <div style="margin: 15px; padding: 15px;">
            <a href="login.php">Already have an account? Login Here!</a>
        </div>
    </div>
</body>
</html>