<?php
    // Load lesson notes, past papers, and practice quizzes from GET parameters
    require "../connect.php";
    
    // Get course_id and outline_id from GET parameters
    $course_id = isset($_GET['course_id']) ? trim($_GET['course_id']) : '';
    $outline_id = isset($_GET['outline_id']) ? trim($_GET['outline_id']) : '';
    $course_id = !empty($course_id) ? (int)$course_id : 0;
    $outline_id = !empty($outline_id) ? (int)$outline_id : 0;
    
    if($course_id <= 0 || $outline_id <= 0) {
        echo json_encode(['error' => 'Invalid course or unit ID', 'course_id' => $_GET['course_id'] ?? 'none', 'outline_id' => $_GET['outline_id'] ?? 'none']);
        exit;
    }
    
    // Query the notes with content type support
    // Use outline_id only so that notes always match the unit the admin manages,
    // even if course_id was stored incorrectly for older data.
    $course_notes_query = "SELECT note_id, section_title, section_content, content_type 
                           FROM notes 
                           WHERE outline_id = $outline_id 
                           ORDER BY note_id ASC";
    $course_notes_result = mysqli_query($conn, $course_notes_query);
    
    $notes = [];
    if(mysqli_num_rows($course_notes_result) > 0) {
        while($row = mysqli_fetch_assoc($course_notes_result)) {
            $notes[] = [
                'note_id' => (int)$row["note_id"],
                'section_title' => $row["section_title"],
                'section_content' => $row["section_content"],
                'content_type' => isset($row["content_type"]) ? $row["content_type"] : 'lesson'
            ];
        }
    }
    
    // Return JSON response
    echo json_encode($notes);
?>