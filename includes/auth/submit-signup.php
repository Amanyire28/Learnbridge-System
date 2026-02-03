<?php
    require "../connect.php";
    
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Check if email exists
    $checkquery = "SELECT id FROM users WHERE email = '$email'";
    $check = mysqli_query($conn, $checkquery);
    if (mysqli_num_rows($check) > 0) {
        echo "Email already exists.";
    } else {
        $insert = "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$password')";
        if (mysqli_query($conn, $insert)) {
            header("Location: ../../index.php");
        } else {
            echo "Error.";
        }
    }
    
    mysqli_close($conn);
    
?>