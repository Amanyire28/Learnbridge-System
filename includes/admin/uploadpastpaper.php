<?php
/**
 * Admin - Upload and Manage Past Papers
 */

include('../connect.php');

// Only start session if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is admin (security check)
if (!isset($_SESSION['user_id'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit();
    }
    // If GET request, redirect to login
    header("Location: ../../index.php");
    exit();
}

if (isset($_SESSION['role']) && $_SESSION['role'] !== 'Admin') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        echo json_encode(['success' => false, 'message' => 'Admin access required']);
        exit();
    }
    header("Location: ../../index.php");
    exit();
}

// Handle form submission for uploading new past paper
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    
    if ($_POST['action'] === 'upload') {
        // Validate inputs
        $course_id = intval($_POST['course_id']);
        $title = mysqli_real_escape_string($conn, $_POST['title']);
        $year = intval($_POST['year']);
        $term = mysqli_real_escape_string($conn, $_POST['term']);
        $subject = mysqli_real_escape_string($conn, $_POST['subject'] ?? '');
        $description = mysqli_real_escape_string($conn, $_POST['description'] ?? '');
        
        // Check if subject exists
        $check_course = mysqli_query($conn, "SELECT course_id FROM courses WHERE course_id = $course_id");
        if (mysqli_num_rows($check_course) == 0) {
            $_SESSION['error'] = "Invalid subject selected";
            echo json_encode(['success' => false, 'message' => 'Invalid subject']);
            exit();
        }
        
        // Create directory if not exists
        $upload_dir = "../../assets/past-papers/course-$course_id/$year";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $uploaded_files = [];
        $errors = [];
        
        // Handle paper file upload
        if (isset($_FILES['paper_file']) && $_FILES['paper_file']['error'] === UPLOAD_ERR_OK) {
            $allowed_types = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
            $file = $_FILES['paper_file'];
            
            // Check file type
            if (!in_array($file['type'], $allowed_types)) {
                $errors[] = "Invalid paper file type. Only PDF and Word documents allowed.";
            } else if ($file['size'] > 10 * 1024 * 1024) { // 10MB limit
                $errors[] = "Paper file too large. Maximum 10MB allowed.";
            } else {
                $filename = time() . "_" . preg_replace('/[^a-zA-Z0-9._-]/', '', $file['name']);
                $paper_path = "$upload_dir/$filename";
                
                if (move_uploaded_file($file['tmp_name'], $paper_path)) {
                    $uploaded_files['paper'] = $filename;
                } else {
                    $errors[] = "Failed to upload paper file";
                }
            }
        } else {
            $errors[] = "No paper file provided";
        }
        
        // Handle solution file upload (optional)
        if (isset($_FILES['solution_file']) && $_FILES['solution_file']['error'] === UPLOAD_ERR_OK) {
            $allowed_types = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
            $file = $_FILES['solution_file'];
            
            if (!in_array($file['type'], $allowed_types)) {
                $errors[] = "Invalid solution file type. Only PDF and Word documents allowed.";
            } else if ($file['size'] > 10 * 1024 * 1024) {
                $errors[] = "Solution file too large. Maximum 10MB allowed.";
            } else {
                $filename = time() . "_solution_" . preg_replace('/[^a-zA-Z0-9._-]/', '', $file['name']);
                $solution_path = "$upload_dir/$filename";
                
                if (move_uploaded_file($file['tmp_name'], $solution_path)) {
                    $uploaded_files['solution'] = $filename;
                } else {
                    $errors[] = "Failed to upload solution file";
                }
            }
        }
        
        // If no errors, save to database
        if (empty($errors) && !empty($uploaded_files['paper'])) {
            $paper_file_path = "assets/past-papers/course-$course_id/$year/" . $uploaded_files['paper'];
            $solution_file_path = isset($uploaded_files['solution']) ? "assets/past-papers/course-$course_id/$year/" . $uploaded_files['solution'] : NULL;
            $file_size = filesize("../../" . $paper_file_path);
            $uploaded_by = $_SESSION['user_id'];
            
            $sql = "INSERT INTO past_papers 
                    (course_id, title, year, term, subject, paper_file_path, solution_file_path, file_size, uploaded_by, description) 
                    VALUES 
                    ($course_id, '$title', $year, '$term', '$subject', '$paper_file_path', " . 
                    ($solution_file_path ? "'$solution_file_path'" : "NULL") . 
                    ", $file_size, $uploaded_by, '$description')";
            
            if (mysqli_query($conn, $sql)) {
                $_SESSION['success'] = "Past paper uploaded successfully!";
                echo json_encode(['success' => true, 'message' => 'Past paper uploaded']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($conn)]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
        }
        exit();
    }
    
    // Handle delete
    else if ($_POST['action'] === 'delete') {
        $paper_id = intval($_POST['paper_id']);
        
        // Get file paths before deleting
        $result = mysqli_query($conn, "SELECT paper_file_path, solution_file_path FROM past_papers WHERE id = $paper_id");
        if ($row = mysqli_fetch_assoc($result)) {
            // Delete files
            @unlink("../../" . $row['paper_file_path']);
            if ($row['solution_file_path']) {
                @unlink("../../" . $row['solution_file_path']);
            }
        }
        
        // Delete from database
        $delete_sql = "DELETE FROM past_papers WHERE id = $paper_id";
        if (mysqli_query($conn, $delete_sql)) {
            $_SESSION['success'] = "Past paper deleted successfully!";
            echo json_encode(['success' => true, 'message' => 'Past paper deleted']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Delete failed']);
        }
        exit();
    }
}

// Fetch subjects for dropdown
$courses = mysqli_query($conn, "SELECT course_id, course_title FROM courses ORDER BY course_title");

// Fetch all past papers
$past_papers = mysqli_query($conn, "
    SELECT pp.*, c.course_title, u.name as uploader_name, COUNT(ppa.id) as download_count
    FROM past_papers pp
    JOIN courses c ON pp.course_id = c.course_id
    JOIN users u ON pp.uploaded_by = u.id
    LEFT JOIN past_paper_attempts ppa ON pp.id = ppa.paper_id
    GROUP BY pp.id
    ORDER BY pp.year DESC, pp.term DESC
");

?>

<div class="container mt-4">
    <h2>📚 Past Papers Management</h2>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <!-- Upload Form -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5>Upload New Past Paper</h5>
        </div>
        <div class="card-body">
            <form id="uploadForm" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="course_id" class="form-label">Subject *</label>
                        <select class="form-select" id="course_id" name="course_id" required>
                            <option value="">Select a subject</option>
                            <?php while ($course = mysqli_fetch_assoc($courses)): ?>
                                <option value="<?php echo $course['course_id']; ?>">
                                    <?php echo htmlspecialchars($course['course_title']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="title" class="form-label">Title *</label>
                        <input type="text" class="form-control" id="title" name="title" 
                               placeholder="e.g., End of Term 1 Exam" required>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="year" class="form-label">Year *</label>
                        <input type="number" class="form-control" id="year" name="year" 
                               min="2000" max="<?php echo date('Y'); ?>" 
                               value="<?php echo date('Y'); ?>" required>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="term" class="form-label">Term *</label>
                        <select class="form-select" id="term" name="term" required>
                            <option value="">Select term</option>
                            <option value="Term 1">Term 1</option>
                            <option value="Term 2">Term 2</option>
                            <option value="Term 3">Term 3</option>
                            <option value="Mid-term">Mid-term</option>
                            <option value="Final">Final</option>
                        </select>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="subject" class="form-label">Subject</label>
                        <input type="text" class="form-control" id="subject" name="subject" 
                               placeholder="e.g., Mathematics">
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="paper_file" class="form-label">Paper File (PDF/Word) *</label>
                        <input type="file" class="form-control" id="paper_file" name="paper_file" 
                               accept=".pdf,.doc,.docx" required>
                        <small class="text-muted">Max 10MB - PDF or Word documents</small>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="solution_file" class="form-label">Solution File (Optional)</label>
                        <input type="file" class="form-control" id="solution_file" name="solution_file" 
                               accept=".pdf,.doc,.docx">
                        <small class="text-muted">Max 10MB - PDF or Word documents</small>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3" 
                              placeholder="Add any notes about this paper..."></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-upload"></i> Upload Past Paper
                </button>
            </form>
        </div>
    </div>
    
    <!-- Past Papers List -->
    <div class="card">
        <div class="card-header bg-secondary text-white">
            <h5>Uploaded Past Papers</h5>
        </div>
        <div class="card-body">
            <?php if (mysqli_num_rows($past_papers) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Course</th>
                                <th>Year</th>
                                <th>Term</th>
                                <th>Subject</th>
                                <th>Downloads</th>
                                <th>Uploaded By</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($paper = mysqli_fetch_assoc($past_papers)): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($paper['title']); ?></strong>
                                        <?php if ($paper['solution_file_path']): ?>
                                            <span class="badge bg-success">Has Solution</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($paper['course_title']); ?></td>
                                    <td><?php echo $paper['year']; ?></td>
                                    <td><?php echo htmlspecialchars($paper['term']); ?></td>
                                    <td><?php echo htmlspecialchars($paper['subject'] ?? '-'); ?></td>
                                    <td>
                                        <span class="badge bg-info">
                                            <i class="fas fa-download"></i> <?php echo $paper['download_count']; ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($paper['uploader_name']); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($paper['upload_date'])); ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-info" onclick="viewDetails(<?php echo $paper['id']; ?>)" 
                                                title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <a href="<?php echo $paper['paper_file_path']; ?>" class="btn btn-sm btn-success" 
                                           download title="Download Paper">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        <button class="btn btn-sm btn-danger" 
                                                onclick="deletePaper(<?php echo $paper['id']; ?>, '<?php echo addslashes($paper['title']); ?>')"
                                                title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No past papers uploaded yet.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.getElementById('uploadForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    formData.append('action', 'upload');
    
    fetch('includes/admin/uploadpastpaper.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ ' + data.message);
            // Reload the admin section instead of full page
            document.querySelector('.admin-nav .pastpapers a').click();
        } else {
            alert('❌ ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred');
    });
});

function deletePaper(id, title) {
    if (confirm(`Are you sure you want to delete "${title}"?`)) {
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('paper_id', id);
        
        fetch('includes/admin/uploadpastpaper.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('✅ ' + data.message);
                // Reload the admin section instead of full page
                document.querySelector('.admin-nav .pastpapers a').click();
            } else {
                alert('❌ ' + data.message);
            }
        });
    }
}

function viewDetails(id) {
    alert('Details view coming soon...');
}
</script>

<style>
.card-header {
    padding: 1rem;
}

.table-responsive {
    margin: 0;
}

.btn-group-sm {
    gap: 5px;
}
</style>
