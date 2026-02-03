<?php
    // Load module/unit title from GET parameter
    require "../connect.php";
    
    // Get outline_id from GET parameter
    $outline_id = isset($_GET['outline_id']) ? trim($_GET['outline_id']) : '';
    $outline_id = !empty($outline_id) ? (int)$outline_id : 0;
    
    if($outline_id <= 0) {
        echo json_encode(['error' => 'Invalid unit ID', 'received' => $_GET['outline_id'] ?? 'none']);
        exit;
    }
    
    // Query the module/unit title
    $module_title_query = "SELECT module_title FROM course_outline WHERE outline_id = $outline_id";
    $module_title_result = mysqli_query($conn, $module_title_query);
    
    if(mysqli_num_rows($module_title_result) > 0) {
        $row = mysqli_fetch_assoc($module_title_result);
        // Return JSON response
        echo json_encode(['title' => $row["module_title"]]);
    } else {
        echo json_encode(['error' => 'Unit not found']);
    }
?>