<?php
$conn = new mysqli('localhost', 'root', '', 'skillsquest');

echo "=== COURSES/SUBJECTS ===\n";
$result = $conn->query('SELECT course_id, course_title FROM courses ORDER BY course_title');
$count = 0;
while($row = $result->fetch_assoc()) {
    $count++;
    echo $count . ". " . $row['course_title'] . " (ID: " . $row['course_id'] . ")\n";
}

echo "\n=== PAST PAPERS ===\n";
$result = $conn->query('SELECT id, title, course_id, year, term FROM past_papers ORDER BY year DESC, term DESC');
$count = 0;
while($row = $result->fetch_assoc()) {
    $count++;
    echo $count . ". " . $row['title'] . " (Course ID: " . $row['course_id'] . ", Year: " . $row['year'] . ", Term: " . $row['term'] . ")\n";
}

echo "\n=== USER 1 ENROLLMENTS ===\n";
$result = $conn->query('SELECT c.course_id, c.course_title FROM completed_courses cc JOIN courses c ON cc.course_id = c.course_id WHERE cc.user_id = 1');
$count = 0;
while($row = $result->fetch_assoc()) {
    $count++;
    echo $count . ". " . $row['course_title'] . " (ID: " . $row['course_id'] . ")\n";
}

echo "\n=== TEST FILTER QUERY ===\n";
$user_id = 1;
$papers_query = "
    SELECT pp.id, pp.title, c.course_title, pp.year, pp.term
    FROM past_papers pp
    JOIN courses c ON pp.course_id = c.course_id
    JOIN completed_courses cc ON c.course_id = cc.course_id AND cc.user_id = $user_id
    WHERE pp.is_active = 1
    ORDER BY pp.year DESC, pp.term DESC
";
echo "Query: " . $papers_query . "\n\n";
$result = $conn->query($papers_query);
$count = 0;
while($row = $result->fetch_assoc()) {
    $count++;
    echo $count . ". " . $row['title'] . " - " . $row['course_title'] . "\n";
}
echo "Total papers found: " . $count . "\n";

$conn->close();
?>
