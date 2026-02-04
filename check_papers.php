<?php
$conn = new mysqli('localhost', 'root', '', 'skillsquest');

echo "=== PAST PAPERS IN DATABASE ===\n\n";
$result = $conn->query('SELECT id, title, paper_file_path, solution_file_path FROM past_papers ORDER BY id DESC LIMIT 10');
while($row = $result->fetch_assoc()) {
    echo "ID: " . $row['id'] . "\n";
    echo "Title: " . $row['title'] . "\n";
    echo "Paper Path: " . $row['paper_file_path'] . "\n";
    
    // Check if the file actually exists
    $full_path = 'C:/xampp/htdocs/learnbridge/' . $row['paper_file_path'];
    echo "Full path: " . $full_path . "\n";
    echo "Exists: " . (file_exists($full_path) ? "YES ✓" : "NO ✗") . "\n";
    
    // Check directory
    $dir = dirname($full_path);
    echo "Directory: " . $dir . "\n";
    echo "Dir exists: " . (is_dir($dir) ? "YES ✓" : "NO ✗") . "\n";
    if (is_dir($dir)) {
        $files = scandir($dir);
        echo "Files in directory: " . count($files) . " files\n";
        foreach ($files as $f) {
            if ($f !== '.' && $f !== '..') {
                echo "  - " . $f . "\n";
            }
        }
    }
    echo "\n---\n\n";
}

$conn->close();
?>
