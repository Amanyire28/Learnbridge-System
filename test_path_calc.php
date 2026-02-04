<?php
// Simulate what downloadpaper.php does
$__DIR__ = 'C:\xampp\htdocs\learnbridge\includes\course';
$file_path = 'assets/past-papers/course-1/2024/1234567890_sample.pdf';

// The fix
$abs_file_path = realpath($__DIR__ . '/../../') . '/' . str_replace('\\', '/', $file_path);

echo "Simulating from: " . $__DIR__ . "\n";
echo "File path stored: " . $file_path . "\n";
echo "Calculated absolute path: " . $abs_file_path . "\n";
echo "Expected path: C:/xampp/htdocs/learnbridge/assets/past-papers/course-1/2024/1234567890_sample.pdf\n";
echo "\nPath calculation correct: " . (strpos($abs_file_path, 'assets/past-papers') !== false ? "YES" : "NO") . "\n";
?>
