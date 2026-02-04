<?php
require 'includes/connect.php';

// Count users
$result = mysqli_query($conn, "SELECT COUNT(*) as total FROM users");
$row = mysqli_fetch_assoc($result);
echo "Total users in database: " . $row['total'] . "\n";

// Get user roles
$roles = mysqli_query($conn, "SELECT role, COUNT(*) as count FROM users GROUP BY role");
echo "\nUsers by role:\n";
while ($row = mysqli_fetch_assoc($roles)) {
    echo "  " . $row['role'] . ": " . $row['count'] . "\n";
}

// Get sample of users
echo "\n\nSample of users:\n";
$users = mysqli_query($conn, "SELECT id, name, email, role FROM users ORDER BY id LIMIT 20");
$count = 0;
while ($row = mysqli_fetch_assoc($users)) {
    $count++;
    echo ($count < 10 ? " " : "") . $count . ". " . $row['name'] . " (" . $row['role'] . ")\n";
}
?>
