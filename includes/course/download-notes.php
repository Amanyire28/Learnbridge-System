<?php
    // Start session first
    session_start();
    
    require "../connect.php";
    
    // Check if user is logged in
    if (!isset($_SESSION["user_id"])) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }
    
    // Get parameters
    $outline_id = isset($_GET['outline_id']) ? (int)$_GET['outline_id'] : 0;
    $format = isset($_GET['format']) ? sanitize($_GET['format']) : 'txt'; // txt or pdf
    
    if ($outline_id <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid outline ID']);
        exit;
    }
    
    // Get unit title for filename
    $unit_query = "SELECT module_title FROM course_outline WHERE outline_id = $outline_id";
    $unit_result = mysqli_query($conn, $unit_query);
    
    if (mysqli_num_rows($unit_result) == 0) {
        http_response_code(404);
        echo json_encode(['error' => 'Unit not found']);
        exit;
    }
    
    $unit = mysqli_fetch_assoc($unit_result);
    $unit_title = $unit['module_title'];
    
    // Get all notes for this unit
    $notes_query = "SELECT section_title, section_content 
                    FROM notes 
                    WHERE outline_id = $outline_id 
                    ORDER BY note_id ASC";
    $notes_result = mysqli_query($conn, $notes_query);
    
    if (mysqli_num_rows($notes_result) == 0) {
        http_response_code(404);
        echo json_encode(['error' => 'No notes found for this unit']);
        exit;
    }
    
    // Generate filename
    $filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $unit_title);
    $filename = substr($filename, 0, 50); // Limit filename length
    $timestamp = date('Y-m-d');
    
    if ($format === 'pdf') {
        generatePDF($unit_title, $notes_result, $filename, $timestamp);
    } else {
        // Plain text download
        generateTXT($unit_title, $notes_result, $filename, $timestamp);
    }
    
    function generatePDF($unit_title, $notes_result, $filename, $timestamp) {
        // Create a simple PDF
        $pdf_content = "%PDF-1.4\n";
        $pdf_content .= "1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n";
        $pdf_content .= "2 0 obj\n<< /Type /Pages /Kids [3 0 R] /Count 1 >>\nendobj\n";
        $pdf_content .= "3 0 obj\n<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >>\nendobj\n";
        
        // Build content stream
        $stream = "BT\n/F1 16 Tf\n50 750 Td\n(" . addslashes($unit_title) . ") Tj\n/F1 10 Tf\n0 -20 Td\n";
        
        while ($note = mysqli_fetch_assoc($notes_result)) {
            $stream .= "/F1 12 Tf\n(" . addslashes($note['section_title']) . ") Tj\n";
            $stream .= "/F1 10 Tf\n0 -15 Td\n";
            // Truncate long content
            $content = substr($note['section_content'], 0, 500);
            $stream .= "(" . addslashes($content) . ") Tj\n0 -10 Td\n";
        }
        
        $stream .= "ET\n";
        
        $pdf_content .= "4 0 obj\n<< /Length " . strlen($stream) . " >>\nstream\n" . $stream . "endstream\nendobj\n";
        $pdf_content .= "5 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>\nendobj\n";
        $pdf_content .= "xref\n0 6\n";
        $pdf_content .= "0000000000 65535 f\n";
        $pdf_content .= "0000000009 00000 n\n";
        $pdf_content .= "0000000058 00000 n\n";
        $pdf_content .= "0000000115 00000 n\n";
        $pdf_content .= "0000000267 00000 n\n";
        $pdf_content .= "0000000400 00000 n\n";
        $pdf_content .= "trailer\n<< /Size 6 /Root 1 0 R >>\nstartxref\n500\n%%EOF\n";
        
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '_' . $timestamp . '.pdf"');
        header('Content-Length: ' . strlen($pdf_content));
        echo $pdf_content;
    }
    
    function generateTXT($unit_title, $notes_result, $filename, $timestamp) {
        // Build content
        $content = "Unit: " . $unit_title . "\n";
        $content .= str_repeat("=", 80) . "\n\n";
        
        while ($note = mysqli_fetch_assoc($notes_result)) {
            $content .= $note['section_title'] . "\n";
            $content .= str_repeat("-", 40) . "\n";
            $content .= $note['section_content'] . "\n\n";
        }
        
        header('Content-Type: text/plain; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '_' . $timestamp . '.txt"');
        echo $content;
    }
    
    function sanitize($input) {
        return htmlspecialchars(strip_tags($input), ENT_QUOTES, 'UTF-8');
    }
?>

