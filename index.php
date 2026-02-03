<?php
    require "includes/header.php";
?>

    <div class="container-fluid min-vh-100" id="hero-section">
        <div class="row">
            <div class="col-lg-6 d-flex justify-content-center align-items-center flex-column px-5">
                <h1 class="mx-5 text-center ">Master <span class="text-warning cssanimation lePeek sequence">Uganda's Curriculum</span>, One Subject at a Time</h1>
                <p class="mx-5 text-center lead">Learn all subjects aligned with Uganda's National Curriculum Framework. From Primary 6 through Secondary 4 (UCE/O-Levels), LearnBridge provides comprehensive study materials, past exam papers, and practice quizzes to help you succeed.</p>
                <?php
                    if(!isset($_SESSION["user_id"])){
                       echo '<a class="btn btn-warning text-white mt-3 mx-5 fs-5" data-bs-toggle="modal" data-bs-target="#loginModal">Start Learning</a>';
                    }
                    else{
                        echo '<a href="courses.php" class="btn btn-warning text-white mt-3 mx-5 fs-5">Browse Subjects</a>';
                    }
                ?>
               
            </div>
            <div class="col-lg-6 p-0 primary">
                <img class="h-100 w-100" src="assets/images/hero.png" alt="">
            </div>
        </div>
    </div>

    <div id="reasons-section" class="container my-5 p-5 shadow rounded-3">
        <div class="row my-3">
           <div class="col-12">
            <h2 class="text-center">Why Choose <span class="text-warning">LearnBridge</span>?</h2>
           </div>
        </div>
        <div class="row text-center p-3">
            <div class="col-12 col-md-6 col-lg-3 mx-auto my-3 shadow reason rounded-3">
                <img src="assets/images/check-mark-3-32.png" alt="">
                <p class="mt-4">
                    Comprehensive coverage of Uganda's National Curriculum Framework with all subjects aligned to Primary 6 through Secondary 4 (UCE/O-Levels) standards.
                </p>
            </div>
            <div class="col-12 col-md-6 col-lg-3 mx-auto my-3 shadow reason rounded-3">
                <img src="assets/images/check-mark-3-32.png" alt="">
                <p class="mt-4">
                    Expert-created study materials, past exam papers, and practice quizzes designed to help you master each subject and ace your exams. 
                </p> 
            </div>
            <!-- <div class="col-12 col-md-6 col-lg-3 mx-auto shadow reason">
                <img src="assets/images/check-mark-3-32.png" alt="">
                <p class="mt-4">
                    Our interactive learning environment is designed to provide you with an immersive and hands-on experience as you practice coding allowing you to apply what you’ve learned in real time.
                </p>
                </p>
            </div> -->
            <div class="col-12 col-md-6 col-lg-3 mx-auto my-3 shadow reason rounded-3">
                <img src="assets/images/check-mark-3-32.png" alt="">
                <p class="mt-4">
                    Study at your own pace with flexible learning schedules that fit around your school and personal commitments. Access materials anytime, anywhere. 
            </div>
           
        </div>

        
    </div>

    <div id="courses-section" class="container my-5 p-3">
        <div class="row mb-4">
            <div class="col">
                <h2 class="text-center">Our Popular <span class="text-warning">Courses</span> </h2>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-12 mb-3 mb-lg-0 col-md-6 col-lg-3">
                <div class="card card-height">
                    <div class="course-placeholder placeholder-english">📚</div>
                    <div class="card-body">
                      <h5 class="card-title">English Language</h5>
                      <div class="card-text mb-3">
                        <p class="">Master English language skills with comprehensive lessons covering grammar, writing, and comprehension aligned with the Uganda curriculum!</p>
                        <div class="rating">
                            <span class="text-warning">4.0</span>
                            <span class="fs-6 text-warning">&#9733;</span>
                            <span class="fs-6 text-warning">&#9733;</span>
                            <span class="fs-6 text-warning">&#9733;</span>
                            <span class="fs-6 text-warning">&#9733;</span>
                            <span class="fs-6 text-secondary">&#9734;</span>
                        </div>                
                      </div>
                      <a href="#" class="btn btn-outline-warning">View Course</a>
                    </div>
                </div>
            </div>

            <div class="col-12 mb-lg-0 mb-3 col-md-6 col-lg-3">
                <div class="card card-height">
                    <div class="course-placeholder placeholder-math">🔢</div>
                    <div class="card-body">
                      <h5 class="card-title">Mathematics</h5>
                      <div class="card-text mb-3">
                        <p class="">Develop strong mathematical foundations covering algebra, geometry, calculus, and statistics for all levels</p>
                        <div class="rating">
                            <span class="text-warning">4.0</span>
                            <span class="fs-6 text-warning">&#9733;</span>
                            <span class="fs-6 text-warning">&#9733;</span>
                            <span class="fs-6 text-warning">&#9733;</span>
                            <span class="fs-6 text-warning">&#9733;</span>
                            <span class="fs-6 text-secondary">&#9734;</span>
                        </div>
                      </div>
                      <a href="#" class="btn btn-outline-warning">View Course</a>
                    </div>
                </div>
            </div>

            <div class="col-12 mb-lg-0 mb-3 col-md-6 col-lg-3">
                <div class="card card-height">
                    <div class="course-placeholder placeholder-science">🔬</div>
                    <div class="card-body">
                      <h5 class="card-title">Science</h5>
                      <div class="card-text mb-3">
                        <p class="">Explore physics, chemistry, and biology with practical explanations and past exam solutions!</p>
                        <div class="rating">
                            <span class="text-warning">4.0</span>
                            <span class="fs-6 text-warning">&#9733;</span>
                            <span class="fs-6 text-warning">&#9733;</span>
                            <span class="fs-6 text-warning">&#9733;</span>
                            <span class="fs-6 text-warning">&#9733;</span>
                            <span class="fs-6 text-secondary">&#9734;</span>
                        </div>
                      </div>
                      <a href="#" class="btn btn-outline-warning">View Course</a>
                    </div>
                </div>
            </div>

            <div class="col-12 mb-3 mb-lg-0 col-md-6 col-lg-3">
                <div class="card card-height">
                    <div class="course-placeholder placeholder-social">🌍</div>
                    <div class="card-body">
                      <h5 class="card-title">Social Studies</h5>
                      <div class="card-text mb-3">
                        <p class="">Learn history, geography, and civic education to understand Uganda's culture and society!</p>
                        <div class="rating">
                            <span class="text-warning">4.0</span>
                            <span class="fs-6 text-warning">&#9733;</span>
                            <span class="fs-6 text-warning">&#9733;</span>
                            <span class="fs-6 text-warning">&#9733;</span>
                            <span class="fs-6 text-warning">&#9733;</span>
                            <span class="fs-6 text-secondary">&#9734;</span>
                        </div>
                      </div>
                      <a href="#" class="btn btn-outline-warning">View Course</a>
                    </div>
                </div>
            </div>   
        </div>
        <div class="row">
            <div class="col text-center">
                <a href="" class="btn btn-warning text-white">See Other Courses</a>
            </div>
        </div>
    </div>

    <div id="reviews-section" class="container my-5">
        <div class="row mb-4">
            <div class="col text-center">
                <h2>What our <span class="text-warning">Students</span> Say</h2>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-12 col-md-6 col-lg-3 mb-3">
                <div class="card">
                    <div class="card-body">
                      <p class="card-text">"LearnBridge helped me excel in my O-Level exams! The past papers and detailed explanations made it easy to understand difficult concepts. I went from struggling with Mathematics to scoring excellent grades!"</p>
                      <div class="d-flex align-items-center">
                        <img class="img-fluid rounded-circle" src="assets/images/student3.jpg" style="height: 50px; width: 50px;" alt="">
                        <p class="mx-3">Sarah L.,<br>O-Level Student</p>
                      </div>  
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-3 mb-3">
                <div class="card">
                    <div class="card-body">
                      <p class="card-text">"As a Primary 7 student, I was worried about the transition to Secondary school. LearnBridge's organized approach to all subjects gave me confidence. The interactive practice quizzes really helped me prepare for my entrance exams!"</p>
                      <div class="d-flex align-items-center">
                        <img class="img-fluid rounded-circle" src="assets/images/student2.jpg" style="height: 50px; width: 50px;" alt="">
                        <p class="mx-3">James P., <br>Secondary Student</p>
                      </div>  
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-3 mb-3">
                <div class="card">
                    <div class="card-body">
                      <p class="card-text">"Having all Uganda curriculum subjects in one place is amazing! I can focus on my weakest areas and build confidence. The study materials are well-structured and really match what we learn in school. Highly recommended!"</p>
                      <div class="d-flex align-items-center">
                        <img class="img-fluid rounded-circle" src="assets/images/student1.png" style="height: 50px; width: 50px;" alt="">
                        <p class="mx-3">John R., <br>Secondary 3 Student</p>
                      </div>  
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-3 mb-3">
                <div class="card">
                    <div class="card-body">
                      <p class="card-text">"I love the comprehensive coverage of all subjects on LearnBridge. The revision notes and practice questions have been instrumental in my success. I'm now ranked top in my class in most subjects!"</p>
                      <div class="d-flex align-items-center">
                        <img class="img-fluid rounded-circle" src="assets/images/student4.jpg" style="height: 50px; width: 50px;" alt="">
                        <p class="mx-3">Ruth B., <br>Form 4 Student</p>
                      </div>  
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col text-center">
                <a href="" class="btn btn-warning text-white">See Other Reviews</a>
            </div>
        </div>
    </div>
<?php
    require "includes/footer.php";
?>
