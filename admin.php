<?php
  require "includes/header.php";
  require "includes/connect.php";
?>

<?php
  // Page-specific scripts to be printed by footer.php before </body>
  $pageScripts = <<<'HTML'
<script>
  // Admin Navigation Handler
  $(document).ready(function() {
    console.log('Admin navigation script initialized');
    
    function loadAdminSection(section) {
      let url = '';
      
      switch(section) {
        case 'dashboard':
          url = 'includes/admin/loadadmindashboard.php';
          break;
        case 'users':
          url = 'includes/admin/loadusers.php';
          break;
        case 'courses':
          url = 'includes/admin/loadcourses.php';
          break;
        case 'units':
          url = 'includes/admin/loadunits.php';
          break;
        case 'messages':
          url = 'includes/admin/loadmessages.php';
          break;
        case 'pastpapers':
          url = 'includes/admin/uploadpastpaper.php';
          break;
        case 'statistics':
          url = 'includes/admin/pastpapersstatistics.php';
          break;
      }
      
      if (url) {
        const contentDiv = document.getElementById('admin-content');
        if (!contentDiv) {
          console.error('admin-content div not found');
          return;
        }
        
        $("#admin-content").load(url, function(response, status, xhr) {
          if (status === "error") {
            console.error('Error loading ' + url + ':', xhr.status, xhr.statusText);
            contentDiv.innerHTML = '<div class="alert alert-danger">Error loading content. Please check console for details.</div>';
          } else {
            console.log('Successfully loaded: ' + url);
          }
        });
      }
    }
    
    function setActiveNav(section) {
      $(".admin-nav li").removeClass("active");
      $(".admin-nav ." + section).addClass("active");
    }
    
    // Load Dashboard by default
    loadAdminSection('dashboard');
    setActiveNav('dashboard');
    
    // Navigation click handlers
    $(document).on("click", ".admin-nav .dashboard a", function(e) {
      e.preventDefault();
      loadAdminSection('dashboard');
      setActiveNav('dashboard');
    });
    
    $(document).on("click", ".admin-nav .users a", function(e) {
      e.preventDefault();
      loadAdminSection('users');
      setActiveNav('users');
    });
    
    $(document).on("click", ".admin-nav .courses a", function(e) {
      e.preventDefault();
      loadAdminSection('courses');
      setActiveNav('courses');
    });
    
    $(document).on("click", ".admin-nav .units a", function(e) {
      e.preventDefault();
      loadAdminSection('units');
      setActiveNav('units');
    });
    
    $(document).on("click", ".admin-nav .messages a", function(e) {
      e.preventDefault();
      loadAdminSection('messages');
      setActiveNav('messages');
    });
    
    $(document).on("click", ".admin-nav .pastpapers a", function(e) {
      e.preventDefault();
      loadAdminSection('pastpapers');
      setActiveNav('pastpapers');
    });
    
    $(document).on("click", ".admin-nav .statistics a", function(e) {
      e.preventDefault();
      loadAdminSection('statistics');
      setActiveNav('statistics');
    });
  });
</script>
HTML;
?>

<div class="container-fluid p-0">
  <div class="row d-lg-none">
    <div class="shadow bg-white col-12 py-3 px-4">
      <button style='background-color: white;' class='border-0' type='button' data-bs-toggle='offcanvas' data-bs-target='#offcanvasWithBothOptions' aria-controls='offcanvasWithBothOptions'>
        <img src='assets/images/menu-4-128.png'  height='30px' alt=''>
        <span class='fs-6'>Menu</span>
      </button>  
    </div>
  </div>
  <div class="row m-0 g-0" style="height: 85vh;">
    <div class="col-1 h-100 shadow d-none d-lg-block p-0 d-flex flex-column align-items-center" style="background: linear-gradient(135deg, #111161 0%, #2a3d8c 100%);">
      <ul class="nav flex-column my-3 fs-5 admin-nav w-100 text-center">
        <li class="nav-item d-flex justify-content-center dashboard">
          <a class="nav-link active text-white" style="border-radius: 8px;" aria-current="page" href="#" title="Dashboard">
            <i class="fas fa-chart-line"></i> Dashboard
          </a>
        </li>
        <li class="nav-item d-flex justify-content-center users">
          <a class="nav-link text-white" style="border-radius: 8px;" href="#" title="Users">
            <i class="fas fa-users"></i> Users
          </a>
        </li>
        <li class="nav-item d-flex justify-content-center courses">
          <a class="nav-link text-white" style="border-radius: 8px;" href="#" title="Courses">
            <i class="fas fa-book"></i> Courses
          </a>
        </li>
        <li class="nav-item d-flex justify-content-center units">
          <a class="nav-link text-white" style="border-radius: 8px;" href="#" title="Units">
            <i class="fas fa-layer-group"></i> Units
          </a>
        </li>
        <li class="nav-item d-flex justify-content-center messages">
          <a class="nav-link text-white" style="border-radius: 8px;" href="#" title="Messages">
            <i class="fas fa-envelope"></i> Messages
          </a>
        </li>
        <li class="nav-item d-flex justify-content-center pastpapers">
          <a class="nav-link text-white" style="border-radius: 8px;" href="#" title="Past Papers">
            <i class="fas fa-file-pdf"></i> Papers
          </a>
        </li>
        <li class="nav-item d-flex justify-content-center statistics">
          <a class="nav-link text-white" style="border-radius: 8px;" href="#" title="Statistics">
            <i class="fas fa-chart-bar"></i> Stats
          </a>
        </li>
      </ul>

    </div>
    <div class="col h-100 shadow mx-lg-0 py-3" style="background: linear-gradient(135deg, #f8f9fa 0%, #e8ecf1 100%);">
      <div id="admin-content"  class="container overflow-auto" style="height: 100%;">
        
      </div>
 
    </div>
  </div>
</div>

<div class='offcanvas offcanvas-start' data-bs-scroll='true' tabindex='-1' id='offcanvasWithBothOptions' aria-labelledby='offcanvasWithBothOptionsLabel'>
    <div class='offcanvas-header'>
        <h5 class='offcanvas-title' id='offcanvasWithBothOptionsLabel'>Admin</h5>
        <button type='button' class='btn-close' data-bs-dismiss='offcanvas' aria-label='Close'></button>
    </div>
    <div class='offcanvas-body'>
    <ul class="nav flex-column my-3 fs-4 admin-nav">
        <li class="nav-item d-flex justify-content-center dashboard">
          <a class="nav-link active text-dark" aria-current="page" href="#">
            <i class="fas fa-chart-line"></i> Dashboard
          </a>
        </li>
        <li class="nav-item d-flex justify-content-center users">
          <a class="nav-link text-dark" href="#">
            <i class="fas fa-users"></i> Users
          </a>
        </li>
        <li class="nav-item d-flex justify-content-center courses">
          <a class="nav-link text-dark" href="#">
            <i class="fas fa-book"></i> Courses
          </a>
        </li>
        <li class="nav-item d-flex justify-content-center units">
          <a class="nav-link text-dark" href="#">
            <i class="fas fa-layer-group"></i> Units
          </a>
        </li>
        <li class="nav-item d-flex justify-content-center messages">
          <a class="nav-link text-dark" href="#">
            <i class="fas fa-envelope"></i> Messages
          </a>
        </li>
        <li class="nav-item d-flex justify-content-center pastpapers">
          <a class="nav-link text-dark" href="#">
            <i class="fas fa-file-pdf"></i> Past Papers
          </a>
        </li>
        <li class="nav-item d-flex justify-content-center statistics">
          <a class="nav-link text-dark" href="#">
            <i class="fas fa-chart-bar"></i> Statistics
          </a>
        </li>
      </ul>
    </div>
</div>


<?php
    require "includes/footer.php";
?>

