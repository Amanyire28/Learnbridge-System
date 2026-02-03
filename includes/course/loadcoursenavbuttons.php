<?php
    // Load navigation buttons (Previous/Next) from GET parameter
    require "../connect.php";
    
    // Get course_id from GET parameter
    $course_id = isset($_GET['course_id']) ? trim($_GET['course_id']) : '';
    $course_id = !empty($course_id) ? (int)$course_id : 0;
    
    if($course_id <= 0) {
        echo json_encode(['error' => 'Invalid course ID', 'received' => $_GET['course_id'] ?? 'none']);
        exit;
    }
    
    // Get all units for this course
    $units_query = "SELECT outline_id, module_number FROM course_outline WHERE course_id = $course_id ORDER BY module_number ASC";
    $units_result = mysqli_query($conn, $units_query);
    
    $units = [];
    if(mysqli_num_rows($units_result) > 0) {
        while($row = mysqli_fetch_assoc($units_result)) {
            $units[] = [
                'outline_id' => (int)$row["outline_id"],
                'module_number' => (int)$row["module_number"]
            ];
        }
    }
    
    // Return JSON response
    echo json_encode($units);
?>
