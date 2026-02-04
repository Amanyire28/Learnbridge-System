<?php
/**
 * Download Handler for Past Papers
 * Tracks downloads and serves files with low-bandwidth support
 */

include('../connect.php');

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

// Allow all users to download any active past paper
$access_query = "SELECT * FROM past_papers WHERE id = $paper_id AND is_active = 1";

$result = mysqli_query($conn, $access_query);

if (!$result || mysqli_num_rows($result) == 0) {
    http_response_code(404);
    die("Paper not found or inactive");
}

$paper = mysqli_fetch_assoc($result);

// Determine which file to serve
$file_path = ($type === 'solution' && $paper['solution_file_path']) 
    ? $paper['solution_file_path'] 
    : $paper['paper_file_path'];

// Get absolute path - stored paths are relative to document root
// __DIR__ = C:\xampp\htdocs\learnbridge\includes\course
// Go up 2 levels to reach document root: C:\xampp\htdocs\learnbridge
$abs_file_path = realpath(__DIR__ . '/../../') . '/' . str_replace('\\', '/', $file_path);

// Check if file exists
if (!file_exists($abs_file_path)) {
    http_response_code(404);
    die("File not found: " . $abs_file_path);
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

// Determine MIME type (mime_content_type is deprecated)
$file_ext = strtolower(pathinfo($abs_file_path, PATHINFO_EXTENSION));
$mime_types = [
    'pdf' => 'application/pdf',
    'doc' => 'application/msword',
    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'xls' => 'application/vnd.ms-excel',
    'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    'txt' => 'text/plain',
    'jpg' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'png' => 'image/png',
];
$mime_type = $mime_types[$file_ext] ?? 'application/octet-stream';

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
