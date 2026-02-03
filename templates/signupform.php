<div class="modal fade" id="signupModal" tabindex="-1" aria-labelledby="signupModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered"> <!--stlyes the modal -->
       <div class="modal-content" style="background-color: ghostwhite;"> <!--defines the content of the modal -->
           <div class="modal-header secondary" style="border-bottom: 1px solid #111161;"><!--style  the header of the modal -->
               <h5 class="modal-title text-white" id="signupModalLabel">Sign Up for Skill Quest</h5>
               <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter: brightness(0);"></button>
           </div>
           <div class="modal-body" >
               <!-- Placeholder Form  -->
               <form action="includes/auth/submit-signup.php" method="POST">
                   <div class="mb-3">
                       <label for="name" class="form-label">Full Name</label>
                       <input type="text" class="form-control" id="name" name="name" placeholder="Enter your name" required style="background-color: rgba(255, 255, 255, 0.1);">
                   </div>
                   <div class="mb-3">
                       <label for="email" class="form-label">Email Address</label>
                       <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required style="background-color: rgba(255, 255, 255, 0.1);">
                   </div>
                   <div class="mb-3">
                       <label for="password" class="form-label">Password</label>
                       <input type="password" class="form-control" id="password" name="password" placeholder="Create a password" required style="background-color: rgba(255, 255, 255, 0.1);">
                   </div>
                   <button type="submit" class="btn btn-warning text-white">Sign Up</button>
               </form>
           </div>
           <div class="modal-footer">
               <button type="button" class="btn btn-outline-warning" data-bs-dismiss="modal">Close</button>
           </div>
       </div>
   </div>
</div>
