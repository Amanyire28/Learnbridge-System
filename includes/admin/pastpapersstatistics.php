<?php
/**
 * Admin - Past Papers Statistics Dashboard
 */

include('../connect.php');

// Only start session if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is admin (security check)
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo '<div class="alert alert-danger">Unauthorized access</div>';
    exit();
}

if (isset($_SESSION['role']) && $_SESSION['role'] !== 'Admin') {
    http_response_code(403);
    echo '<div class="alert alert-danger">Admin access required</div>';
    exit();
}

// Get overall statistics
$stats_query = "
    SELECT 
        COUNT(DISTINCT pp.id) as total_papers,
        COUNT(DISTINCT pp.course_id) as courses_with_papers,
        SUM(pp.file_size) as total_storage,
        COUNT(DISTINCT ppa.student_id) as students_accessed
    FROM past_papers pp
    LEFT JOIN past_paper_attempts ppa ON pp.id = ppa.paper_id
";

$stats = mysqli_fetch_assoc(mysqli_query($conn, $stats_query));

// Get papers by course
$papers_by_course = mysqli_query($conn, "
    SELECT 
        c.course_id, 
        c.course_title,
        COUNT(pp.id) as paper_count,
        COUNT(DISTINCT ppa.student_id) as student_downloads,
        COUNT(ppa.id) as total_downloads
    FROM courses c
    LEFT JOIN past_papers pp ON c.course_id = pp.course_id
    LEFT JOIN past_paper_attempts ppa ON pp.id = ppa.paper_id
    GROUP BY c.course_id
    ORDER BY total_downloads DESC
");

// Get most downloaded papers
$popular_papers = mysqli_query($conn, "
    SELECT 
        pp.id,
        pp.title,
        pp.year,
        pp.term,
        c.course_title,
        COUNT(ppa.id) as download_count,
        COUNT(DISTINCT ppa.student_id) as unique_students
    FROM past_papers pp
    JOIN courses c ON pp.course_id = c.course_id
    LEFT JOIN past_paper_attempts ppa ON pp.id = ppa.paper_id
    GROUP BY pp.id
    ORDER BY download_count DESC
    LIMIT 10
");

// Get recent downloads
$recent_downloads = mysqli_query($conn, "
    SELECT 
        ppa.id,
        u.name as student_name,
        pp.title as paper_title,
        pp.year,
        c.course_title,
        ppa.attempted_at,
        ppa.attempt_type
    FROM past_paper_attempts ppa
    JOIN users u ON ppa.student_id = u.id
    JOIN past_papers pp ON ppa.paper_id = pp.id
    JOIN courses c ON pp.course_id = c.course_id
    ORDER BY ppa.attempted_at DESC
    LIMIT 20
");

?>

<div class="container-fluid mt-4">
    <h2>📊 Past Papers Statistics</h2>
    
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Total Papers</h6>
                            <h3><?php echo number_format($stats['total_papers']); ?></h3>
                        </div>
                        <i class="fas fa-file-pdf" style="font-size: 2.5rem; opacity: 0.7;"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Courses</h6>
                            <h3><?php echo number_format($stats['courses_with_papers']); ?></h3>
                        </div>
                        <i class="fas fa-book" style="font-size: 2.5rem; opacity: 0.7;"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Students Accessed</h6>
                            <h3><?php echo number_format($stats['students_accessed'] ?? 0); ?></h3>
                        </div>
                        <i class="fas fa-users" style="font-size: 2.5rem; opacity: 0.7;"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Storage Used</h6>
                            <h3><?php echo number_format(($stats['total_storage'] ?? 0) / 1024 / 1024 / 1024, 2); ?>GB</h3>
                        </div>
                        <i class="fas fa-database" style="font-size: 2.5rem; opacity: 0.7;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Most Downloaded Papers -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5>🔥 Most Downloaded Papers</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Course</th>
                            <th>Year</th>
                            <th>Term</th>
                            <th>Downloads</th>
                            <th>Unique Students</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($paper = mysqli_fetch_assoc($popular_papers)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($paper['title']); ?></td>
                                <td><?php echo htmlspecialchars($paper['course_title']); ?></td>
                                <td><?php echo $paper['year']; ?></td>
                                <td><?php echo htmlspecialchars($paper['term']); ?></td>
                                <td>
                                    <span class="badge bg-info"><?php echo number_format($paper['download_count']); ?></span>
                                </td>
                                <td><?php echo number_format($paper['unique_students'] ?? 0); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Papers by Course -->
    <div class="card mb-4">
        <div class="card-header bg-secondary text-white">
            <h5>📚 Papers by Course</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Course</th>
                            <th>Papers</th>
                            <th>Downloads</th>
                            <th>Unique Students</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($course = mysqli_fetch_assoc($papers_by_course)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($course['course_title']); ?></td>
                                <td>
                                    <span class="badge bg-primary"><?php echo $course['paper_count']; ?></span>
                                </td>
                                <td><?php echo number_format($course['total_downloads'] ?? 0); ?></td>
                                <td><?php echo number_format($course['student_downloads'] ?? 0); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Recent Downloads -->
    <div class="card">
        <div class="card-header bg-info text-white">
            <h5>⏱️ Recent Downloads (Last 20)</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Paper</th>
                            <th>Course</th>
                            <th>Type</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($download = mysqli_fetch_assoc($recent_downloads)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($download['student_name']); ?></td>
                                <td><?php echo htmlspecialchars($download['paper_title']); ?></td>
                                <td><?php echo htmlspecialchars($download['course_title']); ?></td>
                                <td>
                                    <span class="badge bg-secondary">
                                        <?php echo ucfirst($download['attempt_type']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M d, Y H:i', strtotime($download['attempted_at'])); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        border: none;
        margin-bottom: 20px;
    }
    
    .card-header {
        border-bottom: 3px solid rgba(255, 255, 255, 0.2);
    }
    
    h2 {
        margin-bottom: 25px;
        color: #333;
    }
</style>
