<?php
  require "includes/header.php";
?>

 <!-- contact info -->
 <div class="bg-white p-4">
    <h1 class="text-center " style="color: #FFD700;">Skill Quest - Contact Us</h1>
    <p class="text-center fs-5 text-muted">
        We’d love to hear from you! Whether you have questions, feedback, or suggestions,<br> 
        the LearnBridge team is here to help and serve you.
        <br>Reach out to us and we’ll get back to you 
        as soon as possible.
    </p>
</div>
    <section style="width: 90%; margin: auto;" >
        <hr>
            <!-- Contact Section -->
            <section class="row mb-4 gap-4">
                <!-- Contact Form -->
                <div class="col-md-6 mb-4 bg-light">
                   <?php
                      require "templates/contactusform.php";
                   ?>
                </div>

                <!-- Contact Info -->
                <div class="col-md-6 mb-4" style="width: 40%; box-shadow:  0 0 5px rgba(108, 117, 125, 0.5) ;">
                    <div class="card">
                        <div class="card-body">
                            <h2 class="card-title text-center mb-3" style="color: rgb(17, 17, 97);">Reach Out</h2>
                            <p class="text-center mb-4">
                                Have questions? We’re here to help!
                            </p>
                            <ul class="list-unstyled text-center h-100 mb-5">
                                <h6 style="color: rgb(17, 17, 97);">ADDRESS</h6>
                                <hr>
                                <li><i class="fas fa-envelope me-2"></i> <a href="mailto:support@skillsquest.com" class="text-decoration-none">support@skillsquest.com</a></li>
                                <H6 class="mt-4" style="color: rgb(17, 17, 97);">MOBILE</H6>
                                <li><i class="fas fa-phone me-2"></i> +256-123-456-789 (Optional)</li>
                                <hr>
                                <H6 class="mt-4" style="color: rgb(17, 17, 97);">LOCATION</H6>
                                <li><i class="fab fa-twitter me-2"></i> <a href="#" class="text-decoration-none"> @SkillsQuestUG</a></li>
                                <hr>
                                <h6 class="mt-4" style="color: rgb(17, 17, 97);">WEBSITE</h6>
                                <li><i class="fas fa-globe "> <a href="#" class="text-decoration-none"> www.skillquest.com</a></i></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>
    </section>

    <section class="p-5">
        <div class="d-flex mt-4" style="width: 90%; margin: auto;">
            <div class="card ms-4 p-4" style="box-shadow:  0 0 5px rgba(108, 117, 125, 0.5) ;">
                <i class="fas fa-check text-center fs-1"></i> 
                <h2 style="color: rgb(17, 17, 97)">Customer Support</h2>
                <p>Need help with our platform? Our dedicated support team is available to assist you with any technical issues, course-related questions, or general inquiries. Reach out to us anytime for prompt and reliable support.</p>
            </div>
            
            <div class="card ms-4 p-4" style="box-shadow:  0 0 5px rgba(108, 117, 125, 0.5) ;">
                <i class="fas fa-check text-center fs-1"></i> 
                <h2 style="color: rgb(17, 17, 97)" >Feedback & Suggestions</h2>
                <p>We value your opinions! Let us know how we can improve your experience on LearnBridge. Whether it's a feature request or general feedback, your insights help us create a better learning environment.</p>
            </div>
            
            <div class="card ms-4 p-4" style="box-shadow:  0 0 5px rgba(108, 117, 125, 0.5) ;">
                <i class="fas fa-check text-center fs-1"></i> 
                <h2 style="color: rgb(17, 17, 97)">Media Inquiries</h2>
                <p>For press releases, interviews, or media collaborations, please contact our media relations team. We are happy to provide information about our platform, mission, and upcoming developments.</p>
            </div>        
        </div>
    </section>
    
    <section class=" text-center p-5">
        <h5 class="mb-4">Connect With Us</h5>
    <div class="d-flex justify-content-center gap-4">
        <a href="https://facebook.com" target="_blank" class="text-decoration-none pe-3 border-end">
            <i class="fab fa-facebook fa-lg fs-3" style="color: #FFD700;"></i>
        </a>
        <a href="https://twitter.com" target="_blank" class="text-decoration-none pe-3 border-end">
            <i class="fab fa-twitter fa-lg fs-3" style="color: #FFD700;"></i>
        </a>
        <a href="https://instagram.com" target="_blank" class="text-decoration-none pe-3 border-end" style="border-color: #FFD700;">
            <i class="fab fa-instagram fa-lg fs-3" style="color: #FFD700;"></i>
        </a>
        <a href="https://linkedin.com" target="_blank" class="text-decoration-none ">
            <i class="fab fa-linkedin fa-lg fs-3" style="color: #FFD700;"></i>
        </a>
    </div>
    </section>
    <section class=" my-5 p-5" style="box-shadow:  0 0 5px rgba(108, 117, 125, 0.5) ; width: 90%; margin: auto;">
        <h2 class="text-center mb-4" style="color: #FFD700;">Frequently Asked Questions</h2>
        
        <div class="row">
          <!-- First Row - 3 Questions -->
          <div class="col-md-4 mb-3">
            <div class="card h-100 shadow-sm">
              <div class="card-body">
                <h5 class="card-title"> ❓What is LearnBridge?</h5>
                <p class="">LearnBridge is an online learning platform offering free and accessible courses aligned with Uganda's National Curriculum Framework.</p>
              </div>
            </div>
          </div>
          
          <div class="col-md-4 mb-3">
            <div class="card h-100 shadow-sm">
              <div class="card-body">
                <h5 class="card-title"> 🎓🆓Are the courses free?</h5>
                <p class="">Yes, all our courses are completely free. Our goal is to make quality education available to everyone.</p>
              </div>
            </div>
          </div>
          
          <div class="col-md-4 mb-3">
            <div class="card h-100 shadow-sm">
              <div class="card-body">
                <h5 class="card-title"> 📚 How do I enroll?</h5>
                <p class="">Simply choose a course you're interested in and click on the "Enroll Now" button to get started right away.</p>
              </div>
            </div>
          </div>
        </div>
        
        <div class="row">
          <!-- Second Row - 3 Questions -->
          <div class="col-md-4 mb-3">
            <div class="card h-100 shadow-sm">
              <div class="card-body">
                <h5 class="card-title"> 📚🤔Do I need any prior knowledge?</h5>
                <p class="">No prior knowledge is required! Our courses are beginner-friendly and designed to guide you step by step.</p>
              </div>
            </div>
          </div>
          
          <div class="col-md-4 mb-3">
            <div class="card h-100 shadow-sm">
              <div class="card-body">
                <h5 class="card-title">📜 Can I get a certificate?</h5>
                <p class="">Yes, upon completing a course and passing the final assessment, you’ll receive a digital certificate of completion.</p>
              </div>
            </div>
          </div>
          
          <div class="col-md-4 mb-3">
            <div class="card h-100 shadow-sm">
              <div class="card-body">
                <h5 class="card-title"> 💬✉️How can I contact support?</h5>
                <p class="">You can reach us through the Contact Us page or email us directly at support@learnbridge.org.</p>
              </div>
            </div>
          </div>
        </div>
      </section>

<?php
    require "includes/footer.php";
?>