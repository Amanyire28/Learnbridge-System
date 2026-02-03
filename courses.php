<?php
  require "includes/header.php";
  require "includes/connect.php";

  function renderCourseCard($course_id, $title, $desc, $img, $btnClass, $btnText, $btnAction = '', $showModal = false, $showUnenroll = false) {
    // Validate and set default image if empty
    if (empty($img) || trim($img) == '') {
        $img = 'assets/images/intro(1).jpg'; // Default fallback image
    }
    // Ensure image path doesn't have leading/trailing spaces
    $img = trim($img);
    
    echo "<div class='col-12 col-md-6 col-lg-3 mb-4'>
            <div class='card card-height' id='coursecard$course_id'>
                <img src='$img' class='card-img-top card-img-height' alt='" . htmlspecialchars($title, ENT_QUOTES) . "' onerror=\"this.src='assets/images/intro(1).jpg'\">
                <div class='card-body'>
                    <h5 class='card-title'>$title</h5>
                    <div class='card-text mb-3'>
                        <p>$desc</p>";

    // Buttons container
    echo "<div class='d-flex flex-column gap-2'>";
    
    // Main button
    if ($showModal) {
        echo "<a class='$btnClass' data-bs-toggle='modal' data-bs-target='#enrollModal$course_id' onclick='event.stopPropagation();'>$btnText</a>";
    } else {
        // Use onclick handler for navigation - ensure it executes
        if (!empty($btnAction)) {
            $escaped_action = htmlspecialchars($btnAction, ENT_QUOTES, 'UTF-8');
            echo "<a class='$btnClass' href='javascript:void(0);' onclick=\"$escaped_action\" id='$course_id'>$btnText</a>";
        } else {
            echo "<a class='$btnClass' href='#' id='$course_id'>$btnText</a>";
        }
    }
    
    // Unenroll button (if enabled)
    if ($showUnenroll) {
        echo "<button type='button' class='btn btn-outline-danger btn-sm unenroll-btn' data-bs-toggle='modal' data-bs-target='#unenrollModal$course_id'>Remove Course</button>";
    }
    
    echo "</div>"; // End buttons container

    echo        "</div>
                </div>
            </div>
          </div>";

    // Modal for enrollment
    if ($showModal) {
        $escaped_title = htmlspecialchars($title, ENT_QUOTES);
        echo "
        <div class='modal fade' id='enrollModal$course_id' tabindex='-1' aria-labelledby='enrollModalLabel$course_id' aria-hidden='true'>
          <div class='modal-dialog modal-dialog-centered'>
            <div class='modal-content'>
              <div class='modal-header'>
                <h5 class='modal-title' id='enrollModalLabel$course_id'>Enroll in $escaped_title</h5>
                <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
              </div>
              <div class='modal-body'>
                Are you sure you want to enroll in <strong>$escaped_title</strong>?
              </div>
              <div class='modal-footer'>
                <form action='includes/courses/enroll.php' method='POST'>
                  <input type='hidden' name='course_id' value='$course_id'>
                  <button type='submit' class='btn btn-warning text-white'>Yes, Enroll</button>
                  <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cancel</button>
                </form>
              </div>
            </div>
          </div>
        </div>";
    }
    
    // Modal for unenrollment
    if ($showUnenroll) {
        $escaped_title = htmlspecialchars($title, ENT_QUOTES);
        echo "
        <div class='modal fade' id='unenrollModal$course_id' tabindex='-1' aria-labelledby='unenrollModalLabel$course_id' aria-hidden='true'>
          <div class='modal-dialog modal-dialog-centered'>
            <div class='modal-content' style='background-color: white;'>
              <div class='modal-header'>
                <h5 class='modal-title' id='unenrollModalLabel$course_id'>Remove Course</h5>
                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
              </div>
              <div class='modal-body'>
                Are you sure you want to remove <strong>$escaped_title</strong> from your enrolled courses?<br>
                <small class='text-muted'>You can always re-enroll later if you change your mind.</small>
              </div>
              <div class='modal-footer'>
                <form action='includes/courses/unenroll.php' method='POST'>
                  <input type='hidden' name='course_id' value='$course_id'>
                  <button type='submit' class='btn btn-danger'>Yes, Remove</button>
                  <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cancel</button>
                </form>
              </div>
            </div>
          </div>
        </div>";
    }
  }

?>


<div class="container-fluid" style="padding: 1rem;">
    <?php
    // Display success/error messages
    if (isset($_GET['success']) && $_GET['success'] == 'unenrolled') {
        echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                <strong>Success!</strong> Course has been removed from your enrolled courses.
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
              </div>";
    }
    if (isset($_GET['error'])) {
        $error_msg = "An error occurred. Please try again.";
        if ($_GET['error'] == 'not_logged_in') {
            $error_msg = "You must be logged in to perform this action.";
        } elseif ($_GET['error'] == 'invalid_course') {
            $error_msg = "Invalid course selected.";
        } elseif ($_GET['error'] == 'unenroll_failed') {
            $error_msg = "Failed to remove course. Please try again.";
        }
        echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                <strong>Error!</strong> $error_msg
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
              </div>";
    }
    ?>
    <ul class="nav nav-pills w-100" id="pills-tab" role="tablist" style="margin-bottom: 0;">
          <li class="nav-item w-25" role="presentation">
              <button class="nav-link active w-100" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="true">All Subjects</button>
          </li>
          <li class="nav-item w-25" role="presentation">
              <button class="nav-link w-100" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">Enrolled Subjects</button>
          </li>
          <li class="nav-item w-25" role="presentation">
              <button class="nav-link w-100" id="pills-disabled-tab" data-bs-toggle="pill" data-bs-target="#pills-disabled" type="button" role="tab" aria-controls="pills-disabled" aria-selected="false">Completed Subjects</button>
          </li>
        </ul>

      <div class="tab-content" id="pills-tabContent" style="padding: 0; margin: 0;">
        <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab" tabindex="0">
        <?php
          $userid = $_SESSION['user_id'];

          // Get all courses
          $courses_query = "SELECT * FROM courses ORDER BY language, course_title";
          $courses_result = mysqli_query($conn, $courses_query);

          //  Get course IDs the user is enrolled in
          $enrolled_ids = array();
          $enrolled_query = "SELECT course_id FROM enrollments WHERE user_id = $userid";
          $enrolled_result = mysqli_query($conn, $enrolled_query);
          while ($row = mysqli_fetch_assoc($enrolled_result)) {
              $enrolled_ids[] = $row['course_id'];
          }

          // Step 3: Loop through all courses and render grouped by level
          echo "<div class='container'>
                  <div class='row'>
                      <div class='col'>
                          <h1 class='text-center'>Uganda School Curriculum Subjects</h1>
                          <p class='text-center text-muted'>Aligned with Uganda National Curriculum Framework</p>
                      </div>
                  </div>";
          
          // Group courses by level
          $courses_by_level = [];
          $temp_result = mysqli_query($conn, $courses_query);
          while ($row = mysqli_fetch_assoc($temp_result)) {
              $level = $row['language']; // 'language' field now stores education level
              if (!isset($courses_by_level[$level])) {
                  $courses_by_level[$level] = [];
              }
              $courses_by_level[$level][] = $row;
          }
          
          // Display courses grouped by level
          foreach ($courses_by_level as $level => $level_courses) {
              echo "<div class='row mb-1'>
                      <div class='col'>
                          <h3 class='text-primary'>$level</h3>
                          <hr>
                      </div>
                  </div>
                  <div class='row'>";
              
              foreach ($level_courses as $course) {
                  $course_id = $course['course_id'];
                  $title = htmlspecialchars($course['course_title']);
                  $desc = htmlspecialchars($course['course_description']);
                  $img = htmlspecialchars($course['course_image_url']);
                  
                  $is_enrolled = in_array($course_id, $enrolled_ids);
                  if ($is_enrolled) {
                      $btn_class = "btn btn-success";
                      $btn_text = "Continue Learning";
                      $btn_action = "window.location.href='course.php?course_id=$course_id'";
                      renderCourseCard($course_id, $title, $desc, $img, $btn_class, $btn_text, $btn_action, false, true);
                  } else {
                      $btn_class = "btn btn-warning text-white";
                      $btn_text = "Enroll Now";
                      renderCourseCard($course_id, $title, $desc, $img, $btn_class, $btn_text, '', true, false);
                  }
              }
              echo "</div>";
          }
          echo "</div></div>";
          ?>


        </div>
        <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab" tabindex="0">
            <?php
              $userid = $_SESSION["user_id"];
              $enrolled_query = "SELECT * FROM enrollments JOIN courses ON enrollments.course_id = courses.course_id WHERE enrollments.user_id = $userid";
              $result = mysqli_query($conn, $enrolled_query);
              if (!$result) {
                  echo "MySQL Error: " . mysqli_error($conn);
              }
              echo "<div class='container'>
                      <div class='row'>
                          <div class='col'>
                              <h1 class='text-center'>Enrolled Subjects</h1>
                          </div>
                      </div>
                      <div class='row'>";

              while ($row = mysqli_fetch_assoc($result)) {
                  renderCourseCard(
                      $row["course_id"],
                      htmlspecialchars($row["course_title"]),
                      htmlspecialchars($row["course_description"]),
                      htmlspecialchars($row["course_image_url"]),
                      'btn btn-outline-warning',
                      'Continue Course',
                      "window.location.href='course.php?course_id=" . $row["course_id"] . "'",
                      false,
                      true
                  );
              }
              echo "</div></div>";
            ?>
        </div>
        <div class="tab-pane fade" id="pills-disabled" role="tabpanel" aria-labelledby="pills-disabled-tab" tabindex="0">
          <?php
            $userid = $_SESSION["user_id"];
            $completed_query = "SELECT * FROM completed_courses JOIN courses ON completed_courses.course_id = courses.course_id WHERE completed_courses.user_id = $userid";
            $result = mysqli_query($conn, $completed_query);
            if (!$result) {
                echo "MySQL Error: " . mysqli_error($conn);
            }
            echo "<div class='container'>
                    <div class='row'>
                        <div class='col'>
                            <h1 class='text-center'>Completed Subjects</h1>
                        </div>
                    </div>
                    <div class='row'>";
            while ($row = mysqli_fetch_assoc($result)) {
                renderCourseCard(
                    $row["course_id"],
                    htmlspecialchars($row["course_title"]),
                    htmlspecialchars($row["course_description"]),
                    htmlspecialchars($row["course_image_url"]),
                    'btn btn-success',
                    'Review Subject',
                    "window.location.href='course.php?course_id=" . $row["course_id"] . "'",
                    false,
                    true
                );
            }
            echo "</div></div>";
            ?>
        </div>
      </div>

</div>


<?php
   require "includes/footer.php";
?>

<style>
    /* Ensure modals appear above all content */
    .modal {
        z-index: 1050 !important;
    }
    .modal-backdrop {
        z-index: 1040 !important;
    }
    .modal.show {
        display: block !important;
        opacity: 1 !important;
        pointer-events: auto !important;
    }
    .modal.show .modal-dialog {
        transform: translate(0, 0) !important;
        opacity: 1 !important;
        visibility: visible !important;
        display: block !important;
    }
    .modal-dialog {
        margin: 1.75rem auto;
        opacity: 1 !important;
        visibility: visible !important;
        display: block !important;
        position: relative !important;
    }
    .modal-content {
        position: relative !important;
        display: block !important;
    }
    .modal-content {
        background-color: white !important;
        border: 1px solid rgba(0,0,0,.2);
        border-radius: 0.5rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
</style>

<script>
    // Load course function - available on courses.php page (must be after jQuery loads)
    function loadCourse(course_id) {
        window.location.href = `course.php?course_id=${course_id}`;
    }
    
    // Handle course button clicks - only for buttons with .course class that need special handling
    $(document).ready(function() {
        // Only handle .course class buttons that don't have onclick handlers
        $(document).on('click', 'a.course:not([onclick])', function(e) {
            e.preventDefault();
            let courseid = $(this).attr('id');
            if (courseid) {
                sessionStorage.setItem('courseid', courseid);
                window.location.href = `course.php?course_id=${courseid}`;
            }
        });
        
        // Debug: Log modal-related events
        $(document).on('shown.bs.modal', '.modal', function() {
            console.log('Modal shown:', this.id);
        });
        $(document).on('show.bs.modal', '.modal', function() {
            console.log('Modal showing:', this.id);
        });
    });
</script>