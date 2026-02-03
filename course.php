<?php
  require "includes/header.php";
?>
<script>
  // Mark this page as using the new course system
  document.body.setAttribute('data-course-page', 'true');
</script>

<script>
  // Set base API path for AJAX calls - use relative path to avoid encoding issues
  window.API_BASE = 'includes/course/';
  console.log('API_BASE set to:', window.API_BASE);
</script>

<div class='container-fluid my-5 overflow' style='height: 100vh;'>
    <div class='row shadow mx-2 my-2 p-3 rounded-3 d-flex justify-content-between' style='background-color: white;'>
        <div class='col-2 d-lg-none' >
            <button style='background-color: white;' class='border-0' type='button' data-bs-toggle='offcanvas' data-bs-target='#offcanvasWithBothOptions' aria-controls='offcanvasWithBothOptions'>
                <img src='assets/images/menu-4-128.png'  height='30px' alt=''>
                <span class='fs-6'>Menu</span>
            </button>
        </div>
        <div class='col d-none d-lg-block fs-6'>
            <h3 class="course-title"></h3>
        </div>
        <div class='col d-flex justify-content-end align-items-center'>
            <button class='btn btn-success btn-sm me-3 download-notes-btn' style='display: none;' title='Download notes as text file'>
                <i class='fas fa-download'></i> Download Notes
            </button>
            <nav aria-label='breadcrumb'>
                <ol class='breadcrumb'>
                    <li class='breadcrumb-item'><a href='courses.php'>Courses</a></li>
                    <li class='breadcrumb-item'><a href='#' class="course-title"></a></li>
                    <li class='breadcrumb-item active module-title' aria-current='page'></li>
                </ol>
            </nav>
        </div>
    </div>

    <div class='row mx-2' style='height: 90vh;'>
        <div class='col-3 h-100 shadow p-2 overflow-auto  d-none d-lg-block' style='background-color: white'>
            <button style='background-color: white' class='d-none d-md-block course-menu-icon visible border-0' type='button' data-bs-toggle='collapse' data-bs-target='#collapseWidthExample' aria-expanded='false' aria-controls='collapseWidthExample'>
                <img src='assets/images/menu-4-128.png' height='30px' width='30px' alt=''>
                <span class='fs-6 d-block d-lg-inline-block'>Hide Menu</span>
            </button>
            <ul class='nav flex-column h-100 course-outline my-3'>

            </ul>
        </div>
        <div class='col h-100 shadow p-x-3 pt-3 ms-lg-2 rounded-3' style='background-color: white;'>
            <div class='row border-bottom' style='height: 15%;'>
                <div class='col'>
                    <h4 class='text-center course-title'></h4>
                    <h5 class='fw-bolder text-center module-title'></h5>
                </div>
            </div>
            <div class='row my-3 overflow-auto border' style='height: 70%;'>
                <div class='col h-100'>
                    <div id='notes' class='content-container px-5'>";

        
                    </div>
                </div>
            </div>

            <div class='row border-top' style='height: 10%;'>
                <div class='col d-flex justify-content-center align-items-center course-nav-buttons'>
                   
                </div>
            </div>
        </div>
    </div>

</div>


<div class='offcanvas offcanvas-start' data-bs-scroll='true' tabindex='-1' id='offcanvasWithBothOptions' aria-labelledby='offcanvasWithBothOptionsLabel'>
    <div class='offcanvas-header'>
        <h5 class='offcanvas-title' id='offcanvasWithBothOptionsLabel'>Course Outline</h5>
        <button type='button' class='btn-close' data-bs-dismiss='offcanvas' aria-label='Close'></button>
    </div>
    <div class='offcanvas-body'>
        <ul class='nav flex-column h-100 course-outline my-3'>

        </ul>
    </div>
</div>

<!-- Course Completion Modal -->
<div class="modal fade" id="completionModal" tabindex="-1" aria-labelledby="completionModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-center">
      <div class="modal-header">
        <h5 class="modal-title" id="completionModalLabel">🎉 Congratulations!</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <a href="courses.php" class="btn btn-warning text-white">You’ve successfully completed the course!</a>
      </div>
    </div>
  </div>
</div>


<?php
     require "includes/footer.php";
?>

<script src="assets/js/course_uganda.js"></script>
