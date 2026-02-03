<?php
    // Load course outline (syllabus units) from GET parameter
    require "../connect.php";
    
    // Get course_id from GET parameter
    $course_id = isset($_GET['course_id']) ? trim($_GET['course_id']) : '';
    $course_id = !empty($course_id) ? (int)$course_id : 0;
    
    if($course_id <= 0) {
        echo json_encode(['error' => 'Invalid course ID', 'received' => $_GET['course_id'] ?? 'none']);
        exit;
    }
    
    // Query the course outline
    $course_outline_query = "SELECT outline_id, module_number, module_title, module_link FROM course_outline WHERE course_id = $course_id ORDER BY module_number ASC";
    $course_outline_result = mysqli_query($conn, $course_outline_query);
    
    $units = [];
    if(mysqli_num_rows($course_outline_result) > 0) {
        while($row = mysqli_fetch_assoc($course_outline_result)) {
            $units[] = [
                'outline_id' => (int)$row["outline_id"],
                'module_number' => (int)$row["module_number"],
                'module_title' => $row["module_title"],
                'module_link' => $row["module_link"]
            ];
        }
    }
    
    // Return JSON response
    echo json_encode($units);
?>