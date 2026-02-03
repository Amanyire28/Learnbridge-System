<?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "skillquest";

    $conn = mysqli_connect($servername, $username, $password, $dbname);

    if(!$conn){
        die("Connection Unsuccessful" . mysqli_connect_error($conn));
    }

?>

