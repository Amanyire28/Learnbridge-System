<div class="card">
    <div class="card-body">
        <h2 class="card-title text-center mb-3" style="color: rgb(17, 17, 97);">Get in Touch</h2>
        <form action="includes/admin/submit-message.php" method="POST">
            <div class="mb-3">
                <label for="name" >Name</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Your Name" required style="box-shadow:none !important; ">
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Your Email" required style="box-shadow:none !important;">
            </div>
            <div class="mb-3">
                <label for="message" class="form-label">Message</label>
                <textarea class="form-control" id="message" name="message" rows="4" placeholder="Your Message" required style="box-shadow:none !important;"></textarea>
            </div>
            <div class="text-center mt-5">
                <button type="submit" class="btn btn-warning">Submit</button>
            </div>
        </form>
    </div>
</div>