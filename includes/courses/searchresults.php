<?php
    header('Content-Type: text/html; charset=UTF-8');
    require "../connect.php";
    // Get the search query from the GET request
    $query = isset($_GET['query']) ? mysqli_real_escape_string($conn, $_GET['query']) : '';

    // If there's a query, search the database
    if (!empty($query)) {
        // Search query
        $sql = "SELECT * FROM courses WHERE course_title LIKE '%$query%' LIMIT 5"; // Limit to 5 results
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            // Output results as dropdown items
            while ($row = mysqli_fetch_assoc($result)) {
                if(isset($_SESSION["user_id"])){
                    echo '<li><a class="suggestion-item dropdown-item text-white" href="courses.php#coursecard'. htmlspecialchars($row['course_id']) .'">' . htmlspecialchars($row['course_title']) . '</a></li>';
                }
                else{
                    echo '<li><a class="suggestion-item dropdown-item text-white" data-bs-toggle="modal" data-bs-target="#loginModal">' . htmlspecialchars($row['course_title']) . '</a></li>';
            
                }
            }
        } else {
            echo '<li class="suggestion-item">No results found</li>';
        }
    }
?>
