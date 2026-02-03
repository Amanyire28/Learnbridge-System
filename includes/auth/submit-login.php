<?php
    require "../connect.php";
    session_start();
    
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    $result = mysqli_query($conn, "SELECT id, name, password, role FROM users WHERE email = '$email'");
    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        if ($password == $row['password']) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_name'] = $row['name'];
            $_SESSION['user_role'] = $row['role'];
            echo "Login successful!";
            header("Location: ../../index.php");
        } else {
            echo "Wrong password.";
        }
    } else {
        echo "No user found.";
    }
    
    mysqli_close($conn);
?>