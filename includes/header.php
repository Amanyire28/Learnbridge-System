<?php
  require "connect.php";
  session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LearnBridge - Uganda School Curriculum</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/gh/yesiamrocks/cssanimation.io@1.0.3/cssanimation.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/custom.css">
    
    <!-- Offline & Low-Bandwidth Support -->
    <script src="assets/js/low-bandwidth-manager.js" defer></script>
    <script src="assets/js/low-bandwidth-ui.js" defer></script>
    <script>
        // Register Service Worker for offline support
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/service-worker.js')
                    .then(reg => {
                        console.log('[ServiceWorker] Registered successfully:', reg);
                    })
                    .catch(err => {
                        console.log('[ServiceWorker] Registration failed:', err);
                    });
            });
        }
        
        // Show offline message when connection is lost
        window.addEventListener('offline', () => {
            console.log('[Offline] Connection lost');
            // Don't redirect immediately - let Service Worker handle it
            // Only show notification in navbar
            const notif = document.createElement('div');
            notif.style.cssText = 'position:fixed;top:0;left:0;right:0;background:#ff6b6b;color:white;padding:10px;text-align:center;z-index:99999;font-weight:bold;';
            notif.textContent = '⚠ You are offline - Limited functionality available';
            document.body.insertBefore(notif, document.body.firstChild);
        });
    </script>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark secondary">
        <div class="container-fluid">
          <a class="navbar-brand" href="index.php">
            <img src="assets/images/graduation-cap-48 (1).png" alt="">
            <h4 class="d-inline">LearnBridge</h4>
          </a> 
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <form class="d-flex m-lg-x-5 overflow-visible" role="search" action="searchresults.php" method="get">
                <input class="form-control me-2" id="searchcourse" name="searchcourse" type="search" placeholder="Search Courses" aria-label="Search">
                <img class="mt-1" src="assets/images/search-3-24.png" alt="" height="30px">
                <ul id="suggestions" class="dropdown-menu secondary text-white">
                </ul> 
            </form>
          
            <ul class="navbar-nav mx-auto">
              <li class="nav-item">
                <a class="nav-link text-white" href="index.php">Home</a>
              </li>
              <li class="nav-item dropdown">
                      <?php
                         if(isset($_SESSION["user_id"])){
                          echo '<a class="nav-link text-white d-inline-block me-0" href="courses.php">Courses</a>';
                         }
                         else{
                           echo '<a class="nav-link text-white d-inline-block me-0" data-bs-toggle="modal" data-bs-target="#loginModal">Courses</a>';
                         }
                      ?>
                      
                      <a id="dropdowncourses" class="nav-link dropdown-toggle text-white d-inline-block ms-0" href="courses.php" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="position: relative; right: 3px; top: 2px;"></a>
                      <ul id="courses-dropdown" class="dropdown-menu secondary">
                            <?php
                               $sql = "SELECT course_id, course_title FROM courses";
                               $result = mysqli_query($conn, $sql);
                               if(mysqli_num_rows($result) > 0){
                                 while($row = mysqli_fetch_assoc($result)){
                                  if(isset($_SESSION["user_id"])){
                                    echo "<li><a class='dropdown-item text-white' href='courses.php#coursecard". $row["course_id"] ."'>" . $row["course_title"] . "</a></li>";
                                  }
                                  else{
                                    echo "<li><a class='dropdown-item text-white' data-bs-toggle='modal' data-bs-target='#loginModal'". $row["course_id"] ."'>" . $row["course_title"] . "</a></li>";
                                  }
                                   
                                 }
                               } 
                            ?>
                           
                        </ul>
                  </li>
              <li class="nav-item">
                <?php
                  if(isset($_SESSION["user_id"])){
                    echo '<a class="nav-link text-white" href="past-papers.php">📚 Past Papers</a>';
                  }
                  else{
                    echo '<a class="nav-link text-white" data-bs-toggle="modal" data-bs-target="#loginModal">📚 Past Papers</a>';
                  }
                ?>
              </li>
                
  
             
              <li class="nav-item">
                <a href="about_us.php" class="nav-link text-white">About Us</a>
              </li>
              <li class="nav-item">
                <a href="contact_us.php" class="nav-link text-white">Contact Us</a>
              </li>
              <li class="nav-item">
                <?php
                  if(isset($_SESSION["user_role"]) and $_SESSION["user_role"] == "Admin"){
                    echo '
                       <a id="admin" href="admin.php" class="nav-link text-white">Admin</a>
                    ';
                  }
                ?>
               
              </li>
              

            </ul>
            <div class="me-2">
              <?php
                if(!isset($_SESSION["user_id"])){
                  echo '
                     <a id="signup" class="btn btn-outline-light me-2" data-bs-toggle="modal" data-bs-target="#signupModal">Sign Up</a>
                     <a id="signin" class="btn btn-outline-light" data-bs-toggle="modal" data-bs-target="#loginModal">Login</a>
                  ';
                }
                else{
                  echo '
                     <a id="logout" class="btn btn-outline-light me-2" data-bs-toggle="modal" data-bs-target="#logoutModal">Logout</a>
                  ';
                }
              ?>
             
            </div>      
          </div>
        </div>
    </nav>

 


   


