<?php
    // Load course title from GET parameter
    require "../connect.php";
    
    // Get course_id from GET parameter
    $course_id = isset($_GET['course_id']) ? trim($_GET['course_id']) : '';
    $course_id = !empty($course_id) ? (int)$course_id : 0;
    
    if($course_id <= 0) {
        echo json_encode(['error' => 'Invalid course ID', 'received' => $_GET['course_id'] ?? 'none']);
        exit;
    }
    
    // Query the course title
    $course_title_query = "SELECT course_title FROM courses WHERE course_id = $course_id";
    $course_result = mysqli_query($conn, $course_title_query);
    
    if(mysqli_num_rows($course_result) > 0) {
        $row = mysqli_fetch_assoc($course_result);
        // Return JSON response
        echo json_encode(['title' => $row["course_title"]]);
    } else {
        echo json_encode(['error' => 'Course not found']);
    }
?>