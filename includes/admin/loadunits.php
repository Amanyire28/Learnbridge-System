<?php
    /**
     * Load Units Management Interface
     * Displays all courses with their units and allows add/edit/delete operations
     */
    require "../connect.php";
    
    // Handle AJAX requests for operations
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = isset($_POST['action']) ? $_POST['action'] : '';
        
        if ($action === 'add_unit') {
            // Add new unit
            $course_id = isset($_POST['course_id']) ? intval($_POST['course_id']) : 0;
            $module_number = isset($_POST['module_number']) ? intval($_POST['module_number']) : 0;
            $module_title = isset($_POST['module_title']) ? $_POST['module_title'] : '';
            $module_link = isset($_POST['module_link']) ? $_POST['module_link'] : '';
            
            if ($course_id && $module_number && $module_title) {
                $link = strtolower(str_replace(' ', '-', $module_title));
                if (!$module_link) {
                    $module_link = $link;
                }
                
                $sql = "INSERT INTO course_outline (course_id, module_number, module_title, module_link) 
                        VALUES ('$course_id', '$module_number', '$module_title', '$module_link')";
                
                if (mysqli_query($conn, $sql)) {
                    echo json_encode(['success' => true, 'message' => 'Unit added successfully']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error adding unit: ' . mysqli_error($conn)]);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Please fill all required fields']);
            }
            exit;
        }
        
        if ($action === 'delete_unit') {
            // Delete unit
            $outline_id = isset($_POST['outline_id']) ? intval($_POST['outline_id']) : 0;
            
            if ($outline_id) {
                // First delete related notes
                $sql_notes = "DELETE FROM notes WHERE outline_id = '$outline_id'";
                mysqli_query($conn, $sql_notes);
                
                // Then delete the unit
                $sql = "DELETE FROM course_outline WHERE outline_id = '$outline_id'";
                
                if (mysqli_query($conn, $sql)) {
                    echo json_encode(['success' => true, 'message' => 'Unit deleted successfully']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error deleting unit: ' . mysqli_error($conn)]);
                }
            }
            exit;
        }
        
        if ($action === 'edit_unit') {
            // Update unit
            $outline_id = isset($_POST['outline_id']) ? intval($_POST['outline_id']) : 0;
            $module_title = isset($_POST['module_title']) ? $_POST['module_title'] : '';
            $module_link = isset($_POST['module_link']) ? $_POST['module_link'] : '';
            $module_number = isset($_POST['module_number']) ? intval($_POST['module_number']) : 0;
            
            if ($outline_id && $module_title && $module_number) {
                if (!$module_link) {
                    $module_link = strtolower(str_replace(' ', '-', $module_title));
                }
                
                $sql = "UPDATE course_outline 
                        SET module_title = '$module_title', 
                            module_link = '$module_link',
                            module_number = '$module_number'
                        WHERE outline_id = '$outline_id'";
                
                if (mysqli_query($conn, $sql)) {
                    echo json_encode(['success' => true, 'message' => 'Unit updated successfully']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error updating unit: ' . mysqli_error($conn)]);
                }
            }
            exit;
        }
        
        if ($action === 'get_content') {
            // Get content for a unit
            $outline_id = isset($_POST['outline_id']) ? intval($_POST['outline_id']) : 0;
            
            if ($outline_id) {
                $sql = "SELECT note_id, section_title, section_content, content_type FROM notes WHERE outline_id = '$outline_id' ORDER BY note_id";
                $result = mysqli_query($conn, $sql);
                $content = [];
                
                while($row = mysqli_fetch_assoc($result)) {
                    $content[] = $row;
                }
                
                echo json_encode(['success' => true, 'content' => $content]);
            }
            exit;
        }
        
        if ($action === 'add_content') {
            // Add content/note to unit
            $outline_id = isset($_POST['outline_id']) ? intval($_POST['outline_id']) : 0;
            $course_id = isset($_POST['course_id']) ? intval($_POST['course_id']) : 0;
            $section_title = isset($_POST['section_title']) ? $_POST['section_title'] : '';
            $section_content = isset($_POST['section_content']) ? $_POST['section_content'] : '';
            $content_type = isset($_POST['content_type']) ? $_POST['content_type'] : 'lesson';
            
            if ($outline_id && $section_title) {
                $sql = "INSERT INTO notes (course_id, outline_id, section_title, section_content, content_type) 
                        VALUES ('$course_id', '$outline_id', '$section_title', '$section_content', '$content_type')";
                
                if (mysqli_query($conn, $sql)) {
                    echo json_encode(['success' => true, 'message' => 'Content added successfully']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error adding content: ' . mysqli_error($conn)]);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Please fill in title']);
            }
            exit;
        }
        
        if ($action === 'delete_content') {
            // Delete content/note
            $note_id = isset($_POST['note_id']) ? intval($_POST['note_id']) : 0;
            
            if ($note_id) {
                $sql = "DELETE FROM notes WHERE note_id = '$note_id'";
                
                if (mysqli_query($conn, $sql)) {
                    echo json_encode(['success' => true, 'message' => 'Content deleted successfully']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error deleting content: ' . mysqli_error($conn)]);
                }
            }
            exit;
        }
    }

?>

<div class="row">
    <h3 class="lead fs-4 mb-4">Course Units Management</h3>
    
    <!-- Add New Unit Form -->
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">➕ Add New Unit</h5>
            </div>
            <div class="card-body">
                <form id="addUnitForm" class="row g-3">
                    <div class="col-md-4">
                        <label for="courseSelect" class="form-label">Select Course</label>
                        <select class="form-control" id="courseSelect" name="course_id" required>
                            <option value="">-- Select a Course --</option>
                            <?php
                                $sql = "SELECT course_id, course_title FROM courses ORDER BY course_title";
                                $result = mysqli_query($conn, $sql);
                                if ($result && mysqli_num_rows($result) > 0) {
                                    while($row = mysqli_fetch_assoc($result)) {
                                        echo '<option value="' . $row["course_id"] . '">' . htmlspecialchars($row["course_title"]) . '</option>';
                                    }
                                }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="moduleNumber" class="form-label">Unit Number</label>
                        <input type="number" class="form-control" id="moduleNumber" name="module_number" min="1" required>
                    </div>
                    <div class="col-md-4">
                        <label for="moduleTitle" class="form-label">Unit Title</label>
                        <input type="text" class="form-control" id="moduleTitle" name="module_title" placeholder="e.g., Unit 1: Introduction" required>
                    </div>
                    <div class="col-md-12">
                        <label for="moduleLink" class="form-label">Module Link (optional)</label>
                        <input type="text" class="form-control" id="moduleLink" name="module_link" placeholder="Auto-generated if left empty">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-success">Add Unit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Courses and Units List -->
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">📚 All Courses and Their Units</h5>
            </div>
            <div class="card-body">
                <div class="accordion" id="coursesAccordion">
                    <?php
                        $sql = "SELECT c.course_id, c.course_title, c.course_description, 
                                COUNT(co.outline_id) as unit_count
                                FROM courses c
                                LEFT JOIN course_outline co ON c.course_id = co.course_id
                                GROUP BY c.course_id, c.course_title, c.course_description
                                ORDER BY c.course_title";
                        
                        $result = mysqli_query($conn, $sql);
                        
                        if (!$result) {
                            echo '<div class="alert alert-danger">Error loading courses: ' . mysqli_error($conn) . '</div>';
                        } else if (mysqli_num_rows($result) == 0) {
                            echo '<div class="alert alert-warning">No courses found in database</div>';
                        }
                        
                        $course_number = 0;
                        
                        while($course = mysqli_fetch_assoc($result)) {
                            $course_number++;
                            $course_id = $course["course_id"];
                            $unit_count = $course["unit_count"];
                    ?>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading<?php echo $course_id; ?>">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                    data-bs-target="#collapse<?php echo $course_id; ?>" aria-expanded="false">
                                <strong><?php echo htmlspecialchars($course["course_title"]); ?></strong>
                                <span class="badge bg-secondary ms-2"><?php echo $unit_count; ?> Units</span>
                            </button>
                        </h2>
                        <div id="collapse<?php echo $course_id; ?>" class="accordion-collapse collapse" 
                             data-bs-parent="#coursesAccordion">
                            <div class="accordion-body">
                                <p class="text-muted mb-3"><strong>Description:</strong> <?php echo htmlspecialchars($course["course_description"]); ?></p>
                                
                                <!-- Units Table for this Course -->
                                <div class="table-responsive">
                                    <table class="table table-hover table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Unit Number</th>
                                                <th>Unit Title</th>
                                                <th>Module Link</th>
                                                <th>Content Count</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $unit_sql = "SELECT co.outline_id, co.module_number, co.module_title, co.module_link,
                                                            COUNT(n.note_id) as content_count
                                                            FROM course_outline co
                                                            LEFT JOIN notes n ON co.outline_id = n.outline_id
                                                            WHERE co.course_id = '$course_id'
                                                            GROUP BY co.outline_id
                                                            ORDER BY co.module_number";
                                                
                                                $unit_result = mysqli_query($conn, $unit_sql);
                                                $unit_number = 0;
                                                
                                                if (mysqli_num_rows($unit_result) > 0) {
                                                    while($unit = mysqli_fetch_assoc($unit_result)) {
                                                        $unit_number++;
                                                        $outline_id = $unit["outline_id"];
                                                        $content_count = $unit["content_count"];
                                                ?>
                                                <tr>
                                                    <td><?php echo $unit_number; ?></td>
                                                    <td><span class="badge bg-primary"><?php echo $unit["module_number"]; ?></span></td>
                                                    <td><?php echo htmlspecialchars($unit["module_title"]); ?></td>
                                                    <td><code><?php echo htmlspecialchars($unit["module_link"]); ?></code></td>
                                                    <td>
                                                        <span class="badge bg-success"><?php echo $content_count; ?> items</span>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-sm btn-info manage-content" 
                                                                data-course-id="<?php echo $course_id; ?>"
                                                                data-outline-id="<?php echo $outline_id; ?>"
                                                                data-module-title="<?php echo htmlspecialchars($unit["module_title"]); ?>"
                                                                title="Manage Content">📚 Content</button>
                                                        <button class="btn btn-sm btn-warning edit-unit" 
                                                                data-outline-id="<?php echo $outline_id; ?>"
                                                                data-module-number="<?php echo $unit["module_number"]; ?>"
                                                                data-module-title="<?php echo htmlspecialchars($unit["module_title"]); ?>"
                                                                data-module-link="<?php echo htmlspecialchars($unit["module_link"]); ?>"
                                                                title="Edit">✏️ Edit</button>
                                                        <button class="btn btn-sm btn-danger delete-unit" 
                                                                data-outline-id="<?php echo $outline_id; ?>"
                                                                data-module-title="<?php echo htmlspecialchars($unit["module_title"]); ?>"
                                                                title="Delete">🗑️ Delete</button>
                                                    </td>
                                                </tr>
                                                <?php
                                                    }
                                                } else {
                                                    echo '<tr><td colspan="6" class="text-center text-muted">No units for this course yet</td></tr>';
                                                }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Unit Modal -->
<div class="modal fade" id="editUnitModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Unit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editUnitForm">
                <div class="modal-body">
                    <input type="hidden" id="editOutlineId" name="outline_id">
                    <div class="mb-3">
                        <label for="editModuleNumber" class="form-label">Unit Number</label>
                        <input type="number" class="form-control" id="editModuleNumber" name="module_number" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label for="editModuleTitle" class="form-label">Unit Title</label>
                        <input type="text" class="form-control" id="editModuleTitle" name="module_title" required>
                    </div>
                    <div class="mb-3">
                        <label for="editModuleLink" class="form-label">Module Link (optional)</label>
                        <input type="text" class="form-control" id="editModuleLink" name="module_link">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Manage Content Modal -->
<div class="modal fade" id="manageContentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Manage Content for <span id="contentUnitTitle"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="contentOutlineId">
                <input type="hidden" id="contentCourseId">
                
                <!-- Add New Content Form -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0">Add New Content</h6>
                    </div>
                    <div class="card-body">
                        <form id="addContentForm">
                            <div class="mb-3">
                                <label for="contentTitle" class="form-label">Content Title</label>
                                <input type="text" class="form-control" id="contentTitle" placeholder="e.g., Introduction to Variables" required>
                            </div>
                            <div class="mb-3">
                                <label for="contentBody" class="form-label">Content/Description</label>
                                <textarea class="form-control" id="contentBody" rows="4" placeholder="Enter lesson content here..."></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="contentType" class="form-label">Content Type</label>
                                <select class="form-select" id="contentType">
                                    <option value="lesson">Lesson</option>
                                    <option value="quiz">Quiz</option>
                                    <option value="assignment">Assignment</option>
                                    <option value="video">Video</option>
                                    <option value="resource">Resource</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-success">Add Content</button>
                        </form>
                    </div>
                </div>
                
                <!-- Existing Content List -->
                <div class="card">
                    <div class="card-header bg-secondary text-white">
                        <h6 class="mb-0">Existing Content (<span id="contentCount">0</span> items)</h6>
                    </div>
                    <div class="card-body" id="contentList">
                        <p class="text-muted">No content yet. Add some above!</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Add Unit Form Submission
    document.getElementById('addUnitForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        formData.append('action', 'add_unit');
        
        fetch('includes/admin/loadunits.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => console.error('Error:', error));
    });
    
    // Delete Unit Button - Event Delegation
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('delete-unit')) {
            if (confirm('Are you sure you want to delete this unit: ' + e.target.dataset.moduleTitle + '?\nThis will also delete all content in this unit.')) {
                const formData = new FormData();
                formData.append('action', 'delete_unit');
                formData.append('outline_id', e.target.dataset.outlineId);
                
                fetch('includes/admin/loadunits.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        }
    });
    
    // Edit Unit Button - Event Delegation
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('edit-unit')) {
            document.getElementById('editOutlineId').value = e.target.dataset.outlineId;
            document.getElementById('editModuleNumber').value = e.target.dataset.moduleNumber;
            document.getElementById('editModuleTitle').value = e.target.dataset.moduleTitle;
            document.getElementById('editModuleLink').value = e.target.dataset.moduleLink;
            
            const modal = new bootstrap.Modal(document.getElementById('editUnitModal'));
            modal.show();
        }
    });
    
    // Edit Unit Form Submission
    document.getElementById('editUnitForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        formData.append('action', 'edit_unit');
        
        fetch('includes/admin/loadunits.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                bootstrap.Modal.getInstance(document.getElementById('editUnitModal')).hide();
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => console.error('Error:', error));
    });
    
    // Manage Content Button - Event Delegation
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('manage-content')) {
            const courseId = e.target.dataset.courseId;
            const outlineId = e.target.dataset.outlineId;
            const moduleTitle = e.target.dataset.moduleTitle;
            
            document.getElementById('contentCourseId').value = courseId;
            document.getElementById('contentOutlineId').value = outlineId;
            document.getElementById('contentUnitTitle').textContent = moduleTitle;
            
            // Load existing content
            const formData = new FormData();
            formData.append('action', 'get_content');
            formData.append('outline_id', outlineId);
            
            fetch('includes/admin/loadunits.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayContent(data.content);
                }
            })
            .catch(error => console.error('Error:', error));
            
            // Show modal
            new bootstrap.Modal(document.getElementById('manageContentModal')).show();
        }
    });
    
    // Display Content
    function displayContent(content) {
        const contentList = document.getElementById('contentList');
        const contentCount = document.getElementById('contentCount');
        
        contentCount.textContent = content.length;
        
        if (content.length === 0) {
            contentList.innerHTML = '<p class="text-muted">No content yet. Add some above!</p>';
            return;
        }
        
        let html = '';
        content.forEach(item => {
            html += `
                <div class="card mb-2" style="border-left: 4px solid #0d6efd;">
                    <div class="card-body p-2">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">${item.section_title}</h6>
                                <small class="text-muted">${item.content_type}</small>
                                <p class="mb-0 mt-1" style="font-size: 0.85rem;">${item.section_content.substring(0, 100)}${item.section_content.length > 100 ? '...' : ''}</p>
                            </div>
                            <button class="btn btn-sm btn-danger delete-content" data-note-id="${item.note_id}">Delete</button>
                        </div>
                    </div>
                </div>
            `;
        });
        
        contentList.innerHTML = html;
    }
    
    // Add Content Form Submission - Use event delegation
    document.addEventListener('submit', function(e) {
        if (e.target && e.target.id === 'addContentForm') {
            e.preventDefault();
            
            const formData = new FormData();
            formData.append('action', 'add_content');
            formData.append('outline_id', document.getElementById('contentOutlineId').value);
            formData.append('course_id', document.getElementById('contentCourseId').value || 1);
            formData.append('section_title', document.getElementById('contentTitle').value);
            formData.append('section_content', document.getElementById('contentBody').value);
            formData.append('content_type', document.getElementById('contentType').value);
            
            fetch('includes/admin/loadunits.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Clear form
                    document.getElementById('addContentForm').reset();
                    
                    // Reload content list
                    const outlineId = document.getElementById('contentOutlineId').value;
                    const fd = new FormData();
                    fd.append('action', 'get_content');
                    fd.append('outline_id', outlineId);
                    
                    fetch('includes/admin/loadunits.php', {
                        method: 'POST',
                        body: fd
                    })
                    .then(r => r.json())
                    .then(d => {
                        if (d.success) displayContent(d.content);
                    });
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error adding content. Please check console for details.');
            });
        }
    });
    
    // Delete Content Button - Event Delegation
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('delete-content')) {
            if (confirm('Delete this content?')) {
                const noteId = e.target.dataset.noteId;
                const formData = new FormData();
                formData.append('action', 'delete_content');
                formData.append('note_id', noteId);
                
                fetch('includes/admin/loadunits.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Reload content list
                        const outlineId = document.getElementById('contentOutlineId').value;
                        const fd = new FormData();
                        fd.append('action', 'get_content');
                        fd.append('outline_id', outlineId);
                        
                        fetch('includes/admin/loadunits.php', {
                            method: 'POST',
                            body: fd
                        })
                        .then(r => r.json())
                        .then(d => {
                            if (d.success) displayContent(d.content);
                        });
                    } else {
                        alert('Error: ' + data.message);
                    }
                });
            }
        }
    });
</script>
