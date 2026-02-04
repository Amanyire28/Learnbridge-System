<?php
/**
 * Past Papers - Student View
 * Allows students to browse and download past papers
 */

include('includes/connect.php');
session_start();

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'] ?? '';

// Get all subjects that have past papers (show all subjects in filter, not just enrolled)
$courses_query = "
    SELECT DISTINCT c.course_id, c.course_title 
    FROM courses c
    JOIN past_papers pp ON c.course_id = pp.course_id
    WHERE pp.is_active = 1
    ORDER BY c.course_title
";

$courses = mysqli_query($conn, $courses_query);

// Get filter parameters
$selected_course = isset($_GET['course']) ? intval($_GET['course']) : 0;
$selected_year = isset($_GET['year']) ? intval($_GET['year']) : 0;
$selected_term = isset($_GET['term']) ? mysqli_real_escape_string($conn, $_GET['term']) : '';
$search_query = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

// Build query for past papers
$where_clause = "WHERE pp.is_active = 1";

if ($selected_course > 0) {
    $where_clause .= " AND pp.course_id = $selected_course";
}

if ($selected_year > 0) {
    $where_clause .= " AND pp.year = $selected_year";
}

if (!empty($selected_term)) {
    $where_clause .= " AND pp.term = '$selected_term'";
}

if (!empty($search_query)) {
    $where_clause .= " AND (pp.title LIKE '%$search_query%' OR pp.subject LIKE '%$search_query%' OR pp.description LIKE '%$search_query%')";
}

$papers_query = "
    SELECT pp.*, c.course_title, 
           (SELECT COUNT(*) FROM past_paper_attempts WHERE paper_id = pp.id AND student_id = $user_id) as user_downloads,
           (SELECT COUNT(*) FROM past_paper_attempts WHERE paper_id = pp.id) as total_downloads
    FROM past_papers pp
    JOIN courses c ON pp.course_id = c.course_id
    $where_clause
    ORDER BY pp.year DESC, pp.term DESC
";

$papers = mysqli_query($conn, $papers_query);

// Get years for filter dropdown
$years_query = "SELECT DISTINCT year FROM past_papers ORDER BY year DESC";
$years = mysqli_query($conn, $years_query);

// Get terms for filter dropdown
$terms_query = "SELECT DISTINCT term FROM past_papers ORDER BY term";
$terms = mysqli_query($conn, $terms_query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Past Papers - LearnBridge</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/custom.css">
    <style>
        .paper-card {
            transition: all 0.3s ease;
            border-left: 4px solid #007bff;
        }
        
        .paper-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transform: translateY(-2px);
        }
        
        .filter-section {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .badge-custom {
            font-size: 0.85rem;
            padding: 0.4rem 0.8rem;
        }
        
        .papers-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .paper-item {
            background: white;
            border-radius: 8px;
            padding: 20px;
            border: 1px solid #e0e0e0;
            transition: all 0.3s ease;
        }
        
        .paper-item:hover {
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border-color: #007bff;
        }
        
        .paper-year-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #007bff;
            color: white;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        
        .stats-mini {
            display: flex;
            gap: 15px;
            margin-top: 10px;
            font-size: 0.9rem;
            color: #666;
        }
        
        .stats-mini i {
            color: #007bff;
            margin-right: 5px;
        }
        
        .download-btn {
            width: 100%;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <?php include('includes/header.php'); ?>
    
    <div class="container mt-4">
        <h1 class="mb-4">
            <i class="fas fa-file-pdf"></i> Past Papers
        </h1>
        
        <!-- Filter Section -->
        <div class="filter-section">
            <h5 class="mb-3">
                <i class="fas fa-filter"></i> Filter Papers
            </h5>
            
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="course" class="form-label">Subject</label>
                    <select class="form-select" name="course" id="course">
                        <option value="">All Subjects</option>
                        <?php 
                        mysqli_data_seek($courses, 0);
                        while ($course = mysqli_fetch_assoc($courses)): 
                        ?>
                            <option value="<?php echo $course['course_id']; ?>" 
                                    <?php echo ($selected_course == $course['course_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($course['course_title']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label for="year" class="form-label">Year</label>
                    <select class="form-select" name="year" id="year">
                        <option value="">All Years</option>
                        <?php 
                        mysqli_data_seek($years, 0);
                        while ($year = mysqli_fetch_assoc($years)): 
                        ?>
                            <option value="<?php echo $year['year']; ?>" 
                                    <?php echo ($selected_year == $year['year']) ? 'selected' : ''; ?>>
                                <?php echo $year['year']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label for="term" class="form-label">Term</label>
                    <select class="form-select" name="term" id="term">
                        <option value="">All Terms</option>
                        <?php 
                        mysqli_data_seek($terms, 0);
                        while ($term = mysqli_fetch_assoc($terms)): 
                        ?>
                            <option value="<?php echo htmlspecialchars($term['term']); ?>" 
                                    <?php echo ($selected_term == $term['term']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($term['term']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" name="search" id="search" 
                           placeholder="Search by title or subject..." 
                           value="<?php echo htmlspecialchars($search_query); ?>">
                </div>
                
                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        <i class="fas fa-search"></i> Search
                    </button>
                    <a href="past-papers.php" class="btn btn-secondary">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </form>
        </div>
        
        <!-- Past Papers Display -->
        <?php if (mysqli_num_rows($papers) > 0): ?>
            <div class="papers-grid">
                <?php while ($paper = mysqli_fetch_assoc($papers)): ?>
                    <div class="paper-item">
                        <div style="position: relative;">
                            <div class="paper-year-badge">
                                <?php echo $paper['year']; ?>
                            </div>
                            
                            <h5><?php echo htmlspecialchars($paper['title']); ?></h5>
                            
                            <div class="mb-2">
                                <span class="badge bg-info"><?php echo htmlspecialchars($paper['course_title']); ?></span>
                                <span class="badge bg-secondary"><?php echo htmlspecialchars($paper['term']); ?></span>
                                <?php if (!empty($paper['subject'])): ?>
                                    <span class="badge bg-light text-dark"><?php echo htmlspecialchars($paper['subject']); ?></span>
                                <?php endif; ?>
                                <?php if ($paper['solution_file_path']): ?>
                                    <span class="badge bg-success">Has Solution</span>
                                <?php endif; ?>
                            </div>
                            
                            <?php if (!empty($paper['description'])): ?>
                                <p class="text-muted small mb-2">
                                    <?php echo htmlspecialchars(substr($paper['description'], 0, 100)); ?>...
                                </p>
                            <?php endif; ?>
                            
                            <div class="stats-mini">
                                <div>
                                    <i class="fas fa-download"></i>
                                    <span><?php echo $paper['total_downloads']; ?> downloads</span>
                                </div>
                                <div>
                                    <i class="fas fa-file"></i>
                                    <span><?php echo round($paper['file_size'] / 1024 / 1024, 2); ?>MB</span>
                                </div>
                            </div>
                            
                            <div class="btn-group w-100 mt-3" role="group">
                                <a href="includes/course/downloadpaper.php?id=<?php echo $paper['id']; ?>&type=paper" 
                                   class="btn btn-sm btn-primary" download>
                                    <i class="fas fa-download"></i> Paper
                                </a>
                                <?php if ($paper['solution_file_path']): ?>
                                    <a href="includes/course/downloadpaper.php?id=<?php echo $paper['id']; ?>&type=solution" 
                                       class="btn btn-sm btn-success" download>
                                        <i class="fas fa-download"></i> Solution
                                    </a>
                                <?php endif; ?>
                                <button class="btn btn-sm btn-outline-secondary" onclick="viewDetails(<?php echo $paper['id']; ?>)">
                                    <i class="fas fa-info-circle"></i>
                                </button>
                            </div>
                            
                            <?php if ($paper['user_downloads'] > 0): ?>
                                <div class="alert alert-info mt-3 mb-0 small">
                                    <i class="fas fa-check-circle"></i> You downloaded this <?php echo $paper['user_downloads']; ?> time(s)
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center py-5">
                <i class="fas fa-inbox" style="font-size: 3rem;"></i>
                <h4 class="mt-3">No Past Papers Found</h4>
                <p>Try adjusting your filters or check back later.</p>
            </div>
        <?php endif; ?>
    </div>
    
    <?php include('includes/footer.php'); ?>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
    
    <script>
        function viewDetails(paperId) {
            // Show a modal or redirect to details page
            alert('Details for paper #' + paperId);
        }
        
        // Auto-submit form on filter change
        document.querySelectorAll('.filter-section select').forEach(select => {
            select.addEventListener('change', function() {
                this.form.submit();
            });
        });
    </script>
</body>
</html>
