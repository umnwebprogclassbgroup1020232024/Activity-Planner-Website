<?php

    try{
        $dsn = "mysql: dbname=todolistapp; host=localhost";
        $user = "root";
        $pswd = "";

        $conn = new PDO($dsn, $user, $pswd);

        $conn->query("USE todolistapp");
    }
    catch(PDOException $e){
        die("Error Connecting: " . $e->getMessage());
    }

?>