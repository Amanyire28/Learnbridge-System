<?php
    require "../connect.php";
    session_start();
    
    $user_id = $_SESSION['user_id'];
    $course_id = $_POST['course_id'];
    
    // Prevent duplicate enrollment
    
    $check_sql = "SELECT * FROM enrollments WHERE user_id = $user_id AND course_id = $course_id";
    $result = mysqli_query($conn, $check_sql);
    
    if (mysqli_num_rows($result) == 0) {
        $enroll_sql = "INSERT INTO enrollments (user_id, course_id) VALUES ($user_id, $course_id)";
        mysqli_query($conn, $enroll_sql);
        header("Location: ../../courses.php");
    } else {
        header("Location: ../../courses.php");
    }
    
    mysqli_close($conn);
    




?>