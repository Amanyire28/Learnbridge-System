<?php
$conn = new mysqli('localhost', 'root', '', 'skillsquest');

echo "=== DEBUGGING PAST PAPERS DOWNLOAD ===\n\n";

// Check what's in the database
echo "1. PAPERS IN DATABASE:\n";
$result = $conn->query('SELECT id, title, course_id, paper_file_path FROM past_papers');
while($row = $result->fetch_assoc()) {
    echo "\nPaper ID: " . $row['id'] . "\n";
    echo "Title: " . $row['title'] . "\n";
    echo "Course ID: " . $row['course_id'] . "\n";
    echo "Stored path: " . $row['paper_file_path'] . "\n";
}

echo "\n\n2. CHECKING FILE EXISTENCE:\n";
$result = $conn->query('SELECT id, paper_file_path FROM past_papers LIMIT 1');
if ($row = $result->fetch_assoc()) {
    $stored_path = $row['paper_file_path'];
    echo "Stored path: " . $stored_path . "\n";
    
    // Try different path calculations
    $root = 'c:/xampp/htdocs/learnbridge';
    
    echo "\nTesting path variations:\n";
    $test_paths = [
        'Simple concat: ' . $root . '/' . $stored_path,
        'With backslash convert: ' . $root . '/' . str_replace('\\', '/', $stored_path),
        'Real path approach: ' . realpath('c:/xampp/htdocs/learnbridge') . '/' . $stored_path,
    ];
    
    foreach ($test_paths as $test) {
        echo "  Testing: " . $test . "\n";
        echo "  Exists: " . (file_exists($test) ? "YES ✓" : "NO ✗") . "\n";
    }
}

echo "\n\n3. USER ENROLLMENT CHECK:\n";
echo "Session user_id would be: 1 (assuming logged in)\n";
$user_id = 1;
$paper_id = 1;

$access_query = "
    SELECT pp.* FROM past_papers pp
    JOIN courses c ON pp.course_id = c.course_id
    JOIN completed_courses cc ON c.course_id = cc.course_id
    WHERE pp.id = $paper_id AND cc.user_id = $user_id
";
echo "Query: " . $access_query . "\n";
$result = $conn->query($access_query);
echo "Result rows: " . mysqli_num_rows($result) . "\n";

if (mysqli_num_rows($result) > 0) {
    $paper = $result->fetch_assoc();
    echo "Access GRANTED\n";
    echo "Paper found: " . $paper['title'] . "\n";
    echo "File path: " . $paper['paper_file_path'] . "\n";
} else {
    echo "Access DENIED - user not enrolled\n";
    echo "User enrolled courses:\n";
    $enrolled = $conn->query("SELECT cc.course_id, c.course_title FROM completed_courses cc JOIN courses c ON cc.course_id = c.course_id WHERE cc.user_id = $user_id");
    $count = 0;
    while ($row = $enrolled->fetch_assoc()) {
        $count++;
        echo "  $count. " . $row['course_title'] . " (ID: " . $row['course_id'] . ")\n";
    }
    echo "\nPaper's course ID: " . $paper_id . "\n";
}

$conn->close();
?>
