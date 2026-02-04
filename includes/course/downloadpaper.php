<?php
/**
 * Download Handler for Past Papers
 * Tracks downloads and serves files with low-bandwidth support
 */

include('../../connect.php');

// Only start session if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is authenticated
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    die("Unauthorized");
}

$user_id = $_SESSION['user_id'];
$paper_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$type = isset($_GET['type']) ? $_GET['type'] : 'paper'; // 'paper' or 'solution'

if ($paper_id <= 0) {
    http_response_code(400);
    die("Invalid paper ID");
}

// Check if user has access to this paper
$user_role = $_SESSION['role'] ?? '';

if ($user_role === 'Admin') {
    // Admins can download any paper
    $access_query = "SELECT * FROM past_papers WHERE id = $paper_id";
} else {
    // Students can only download papers from their enrolled courses
    $access_query = "
        SELECT pp.* FROM past_papers pp
        JOIN courses c ON pp.course_id = c.course_id
        JOIN completed_courses cc ON c.course_id = cc.course_id
        WHERE pp.id = $paper_id AND cc.user_id = $user_id
    ";
}

$result = mysqli_query($conn, $access_query);

if (!$result || mysqli_num_rows($result) == 0) {
    http_response_code(403);
    die("Access denied");
}

$paper = mysqli_fetch_assoc($result);

// Determine which file to serve
$file_path = ($type === 'solution' && $paper['solution_file_path']) 
    ? $paper['solution_file_path'] 
    : $paper['paper_file_path'];

// Get absolute path
$abs_file_path = "../../" . $file_path;

// Check if file exists
if (!file_exists($abs_file_path)) {
    http_response_code(404);
    die("File not found");
}

// Log download attempt
$ip_address = $_SERVER['REMOTE_ADDR'] ?? '';
$user_agent = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255);
$attempt_type = isset($_GET['preview']) ? 'preview' : 'download';

$log_query = "
    INSERT INTO past_paper_attempts (paper_id, student_id, attempt_type, ip_address, user_agent)
    VALUES ($paper_id, $user_id, '$attempt_type', '$ip_address', '" . mysqli_real_escape_string($conn, $user_agent) . "')
";

mysqli_query($conn, $log_query);

// Get file info
$file_size = filesize($abs_file_path);
$file_name = basename($abs_file_path);
$mime_type = mime_content_type($abs_file_path);

// For low-bandwidth mode, compress the file if possible
$low_bandwidth = isset($_GET['compress']) && $_GET['compress'] == '1';

if ($low_bandwidth && ($mime_type === 'application/pdf' || strpos($mime_type, 'word') !== false)) {
    // In production, you would use a service like ImageMagick or Ghostscript to compress
    // For now, we'll just serve it normally but the frontend can handle compression
}

// Set headers for download
header('Content-Type: ' . $mime_type);
header('Content-Disposition: attachment; filename="' . basename($file_name) . '"');
header('Content-Length: ' . $file_size);
header('Cache-Control: public, must-revalidate, max-age=0');
header('Pragma: public');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');

// Stream the file
readfile($abs_file_path);

// Mark as completed
$update_query = "UPDATE past_paper_attempts SET attempt_type = 'downloaded' WHERE paper_id = $paper_id AND student_id = $user_id ORDER BY attempted_at DESC LIMIT 1";
mysqli_query($conn, $update_query);

exit();
?>
