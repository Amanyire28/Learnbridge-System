<?php
    require "../connect.php";
    $name = $_POST["name"];
    $email = $_POST["email"];
    $message = $_POST["message"];

    $query = "INSERT INTO messages(name, email, message) VALUES('$name', '$email', '$message')";

    if(mysqli_query($conn, $query)){
        header("Location: ../../contact_us.php");
    }else{
        echo "Error occured while sending message" . mysqli_error($conn);
    }




?>