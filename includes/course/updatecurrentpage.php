<?php
    require "../connect.php";
    session_start();
    
    // Get user id from session
    $user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
    
    if(!$user_id) {
        echo json_encode(['error' => 'User not logged in']);
        exit;
    }
    
    // Get parameters from POST
    $course_id = isset($_POST['course_id']) ? (int)$_POST['course_id'] : 0;
    $outline_id = isset($_POST['outline_id']) ? (int)$_POST['outline_id'] : 0;
    $module_number = isset($_POST['module_number']) ? (int)$_POST['module_number'] : 0;
    
    if($course_id <= 0 || $outline_id <= 0 || $module_number <= 0) {
        echo json_encode(['error' => 'Missing required parameters']);
        exit;
    }
    
    // Update the current course page (for progress tracking)
    $update_query = "UPDATE current_course_page SET course_id = $course_id, outline_id = $outline_id, module_number = $module_number WHERE user_id = $user_id LIMIT 1";
    
    if(mysqli_query($conn, $update_query)) {
        echo json_encode(['message' => 'Progress updated successfully']);
    } else {
        echo json_encode(['error' => 'Error updating progress: ' . mysqli_error($conn)]);
    }
?>