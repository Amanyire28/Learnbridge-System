<?php
$conn = new mysqli('localhost', 'root', '', 'skillsquest');

echo "=== PAST PAPERS FILE PATHS ===\n";
$result = $conn->query('SELECT id, title, paper_file_path, solution_file_path FROM past_papers LIMIT 5');
while($row = $result->fetch_assoc()) {
    echo "\nID: " . $row['id'] . "\n";
    echo "Title: " . $row['title'] . "\n";
    echo "Paper Path: " . $row['paper_file_path'] . "\n";
    
    // Check if file exists with different path variations
    $paths_to_test = [
        'c:/xampp/htdocs/learnbridge/' . $row['paper_file_path'],
        'c:/xampp/htdocs/learnbridge/../../' . $row['paper_file_path'],
        realpath('c:/xampp/htdocs/learnbridge/includes/course/../../') . '/' . $row['paper_file_path'],
    ];
    
    foreach ($paths_to_test as $test_path) {
        if (file_exists($test_path)) {
            echo "✓ FOUND at: " . $test_path . "\n";
        }
    }
}

$conn->close();
?>
