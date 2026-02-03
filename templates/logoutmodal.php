   <!-- Logout Modal -->
   <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="background-color: ghostwhite;">
      <div class="modal-header" style="border-bottom: 1px solid black;">
        <h5 class="modal-title" id="logoutModalLabel">Ready to Leave?</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter: brightness(0);"></button>
      </div>
      <div class="modal-body">
        Click "Logout" below if you are ready to end your current session.
      </div>
      <div class="modal-footer" style="border-top: black;">
        <button type="button" class="btn btn-outline-warning" data-bs-dismiss="modal">Cancel</button>
        <form action="includes/auth/logout.php" method="POST">
          <button type="submit" class="btn btn-warning text-white">Logout</button>
        </form>
      </div>
    </div>
  </div>
</div>
