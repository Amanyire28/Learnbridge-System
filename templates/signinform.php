<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background-color: aliceblue;">
            <div class="modal-header secondary">
                <h5 class="modal-title text-white" id="loginModalLabel">Login to Skill Quest</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter: brightness(0);"></button>
            </div>
            <div class="modal-body">
                <form action="includes/auth/submit-login.php" method="POST">
                    <div class="mb-3">
                        <label for="loginUsername" class="form-label">Username</label>
                        <input type="text" class="form-control" id="loginUsername" name="username" placeholder="Enter your username" required style="background-color: rgba(255, 255, 255, 0.1);">
                    </div>
                    <div class="mb-3">
                        <label for="loginEmail" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="loginEmail" name="email" placeholder="Enter your email" required style="background-color: rgba(255, 255, 255, 0.1);">
                    </div>
                    <div class="mb-3">
                        <label for="loginPassword" class="form-label">Password</label>
                        <input type="password" class="form-control" id="loginPassword" name="password" placeholder="Enter your password" required style="background-color: rgba(255, 255, 255, 0.1);">
                    </div>
                    <button type="submit" class="btn btn-warning text-white">Login</button>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-warning" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
