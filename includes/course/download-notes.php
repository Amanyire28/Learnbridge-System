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
        // Generate simple HTML that can be printed to PDF
        generateHTMLForPDF($unit_title, $notes_result, $filename, $timestamp);
    } else {
        // Plain text download
        generateTXT($unit_title, $notes_result, $filename, $timestamp);
    }
    
    function generateHTMLForPDF($unit_title, $notes_result, $filename, $timestamp) {
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>' . htmlspecialchars($unit_title) . '</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            line-height: 1.6;
            color: #333;
        }
        h1 {
            color: #111161;
            border-bottom: 3px solid #111161;
            padding-bottom: 10px;
            margin-bottom: 30px;
        }
        h2 {
            color: #2a3d8c;
            margin-top: 30px;
            margin-bottom: 15px;
            border-left: 5px solid #2a3d8c;
            padding-left: 15px;
        }
        p {
            text-align: justify;
            margin-bottom: 15px;
        }
        .section {
            page-break-inside: avoid;
            margin-bottom: 25px;
        }
        @media print {
            body { margin: 0; }
            h1, h2 { page-break-after: avoid; }
            .section { page-break-inside: avoid; }
        }
    </style>
</head>
<body>
    <h1>' . htmlspecialchars($unit_title) . '</h1>
    <p><em>Generated on: ' . date('F d, Y') . '</em></p>';
        
        while ($note = mysqli_fetch_assoc($notes_result)) {
            $html .= '<div class="section">';
            $html .= '<h2>' . htmlspecialchars($note['section_title']) . '</h2>';
            $html .= '<p>' . nl2br(htmlspecialchars($note['section_content'])) . '</p>';
            $html .= '</div>';
        }
        
        $html .= '
</body>
</html>';
        
        header('Content-Type: application/pdf; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '_' . $timestamp . '.pdf"');
        header('Content-Length: ' . strlen($html));
        echo $html;
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
    
    function generatePDF($unit_title, $notes_result, $filename, $timestamp) {
        // Generate clean HTML that can be saved as PDF by browser
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>' . htmlspecialchars($unit_title) . '</title>
    <style>
        body {
            font-family: "Arial", sans-serif;
            margin: 40px;
            line-height: 1.8;
            color: #333;
            background: white;
        }
        h1 {
            color: #111161;
            border-bottom: 3px solid #111161;
            padding-bottom: 15px;
            margin-bottom: 20px;
            font-size: 28px;
        }
        .date {
            color: #666;
            font-style: italic;
            margin-bottom: 30px;
            font-size: 12px;
        }
        h2 {
            color: #2a3d8c;
            margin-top: 25px;
            margin-bottom: 12px;
            border-left: 5px solid #ffc107;
            padding-left: 15px;
            font-size: 18px;
        }
        p {
            text-align: justify;
            margin-bottom: 12px;
            font-size: 14px;
        }
        .section {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        @page {
            margin: 40px;
        }
    </style>
</head>
<body>
    <h1>' . htmlspecialchars($unit_title) . '</h1>
    <div class="date">Generated on: ' . date('F d, Y \a\t H:i') . '</div>';
        
        while ($note = mysqli_fetch_assoc($notes_result)) {
            $html .= '<div class="section">';
            $html .= '<h2>' . htmlspecialchars($note['section_title']) . '</h2>';
            $html .= '<p>' . str_replace("\n", "</p><p>", htmlspecialchars($note['section_content'])) . '</p>';
            $html .= '</div>';
        }
        
        $html .= '</body></html>';
        
        // Output as HTML (browser can print/save to PDF)
        header('Content-Type: text/html; charset=utf-8');
        header('Content-Disposition: inline; filename="' . $filename . '_' . $timestamp . '.pdf.html"');
        echo $html;
    }
    
    function sanitize($input) {
        return htmlspecialchars(strip_tags($input), ENT_QUOTES, 'UTF-8');
    }
?>

