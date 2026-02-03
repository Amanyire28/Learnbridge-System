<?php
    require "../connect.php";
    
    // Start the session
    session_start();
    
    // Get user id
    $user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
    
    // Get course_id from POST parameter
    $course_id = isset($_POST['course_id']) ? (int)$_POST['course_id'] : 0;
    
    if(!$user_id || !$course_id) {
        echo json_encode(['error' => 'Missing user ID or course ID']);
        exit;
    }
    
    // Check if the user already completed the course
    $check_query = "SELECT * FROM completed_courses WHERE user_id = $user_id AND course_id = $course_id";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        echo json_encode(['message' => 'You have already completed this course.']);
    } else {
        // Insert course completion
        $insert_query = "INSERT INTO completed_courses (user_id, course_id, completion_date) 
                        VALUES ($user_id, $course_id, NOW())";
        
        if (mysqli_query($conn, $insert_query)) {
            echo json_encode(['message' => 'Course marked as completed successfully!']);
        } else {
            echo json_encode(['error' => 'Failed to mark course as completed: ' . mysqli_error($conn)]);
        }
    }
    
    // Close the connection
    mysqli_close($conn);
?>