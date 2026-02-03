<style>
    .form-control, .form-select{
        border-width: 2px;
    }

    .form-control:focus {
        border-color:rgba(0, 0, 0, 0.8);
        box-shadow: none !important; /* optional: remove blue shadow */
    }
</style>

<?php
    require "templates/signinform.php";
    require "templates/signupform.php";
    require "templates/logoutmodal.php";
?>

<!-- footer -->
     <footer class="py-4 text-white secondary">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6 col-12">
                    <h3 class="text-warning mb-10 fs-3">Quick Links</h3>
                    <ul class="list-unstyled">
                        <li class="mb-20" ><a href="index.html" class="text-decoration-none text-white">Home</a></li>
                        <li class="mb-10" ><a href="about_us.html" class="text-decoration-none text-white">About Us</a></li>
                        <li class="mb-10" ><a href="contact_us.html" class="text-decoration-none text-white">Contact Us</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 col-md-6 col-12">
                    <h3 class="text-warning mb-10 fs-3">Connect with Us</h3>
                    <div class="social-icons">
                        <a href="https://www.facebook.com" target="_blank" class="text-decoration-none text-white"><i class="fab fa-facebook-f me-2 "></i><span>facebook</span></a><br>
                        <a href="https://www.twitter.com" target="_blank" class="text-decoration-none text-white"><i class="fab fa-twitter me-2"></i><span>twitter</span></a><br>
                        <a href="https://www.instagram.com" target="_blank" class="text-decoration-none text-white"><i class="fab fa-instagram me-2"></i><span>instagram</span></a><br>
                        <a href="https://www.linkedin.com" target="_blank" class="text-decoration-none text-white"><i class="fab fa-linkedin-in me-2"></i><span>linkedin-in</span></a><br>
                        <a href="https://www.youtube.com" target="_blank" class="text-decoration-none text-white"><i class="fab fa-youtube me-2 "></i><span>youtube</span></a><br>
                    </div>
                </div>
                <div class="col-lg-4 col-md-12 col-12">
                    <h3 class="text-warning mb-10 fs-3">Contact Us</h3>
                    <ul>
                        <li>Address: 123, LearnBridge Street, Skill City, Kampala</li>
                        <li>Email: <a href="mailto:learnbridge@gmail.com" class="text-white text-decoration-none">learnbridge@gmailcom</a></li>
                        <li>Phone: <a href="tel:+256703558174" class="text-white text-decoration-none">+256 703558174</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </footer> 

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <!-- jQuery (cdnjs CDN) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/gh/yesiamrocks/cssanimation.io@1.0.3/letteranimation.min.js"></script>
    <script src="assets/js/main.js"></script>

    <?php
        // Optional per-page scripts (should be a string containing <script> tags).
        if (isset($pageScripts)) {
            echo $pageScripts;
        }
    ?>

</body>
</html>