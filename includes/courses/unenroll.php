<?php
    require "../connect.php";
    session_start();
    
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../../courses.php?error=not_logged_in");
        exit;
    }
    
    $user_id = $_SESSION['user_id'];
    $course_id = isset($_POST['course_id']) ? (int)$_POST['course_id'] : 0;
    
    if ($course_id <= 0) {
        header("Location: ../../courses.php?error=invalid_course");
        exit;
    }
    
    // Delete the enrollment
    $unenroll_sql = "DELETE FROM enrollments WHERE user_id = $user_id AND course_id = $course_id";
    $result = mysqli_query($conn, $unenroll_sql);
    
    if ($result) {
        header("Location: ../../courses.php?success=unenrolled");
    } else {
        header("Location: ../../courses.php?error=unenroll_failed");
    }
    
    mysqli_close($conn);
?>
