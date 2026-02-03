
<?php
  require "../connect.php";
?>
<div class="row">
  <div class="col">
    <h3>User Management</h3>
    <button class="btn btn-success text-white" data-bs-toggle="modal" data-bs-target="#addUserModal">Add User</button>
  </div>

  <div class="col-12">
    <table class="table table-hover table-bordered caption-top mt-4">
      <caption>List of Users</caption>
      <thead>
        <tr>
          <th>#</th>
          <th>Name</th>
          <th>Email</th>
          <th>Role</th>
          <th>Edit</th>
          <th>Delete</th>
        </tr>
      </thead>
      <tbody id="userTable">
        <?php
          $sql = "SELECT * FROM users";
          $result = mysqli_query($conn, $sql);
          while($row = mysqli_fetch_assoc($result)){
            echo "
                  <tr>  
                    <td class='userID'>" . $row["id"] . "</td>
                    <td class='userName'>" . $row["name"] . "</td>
                    <td class='userEmail'>" . $row["email"] . "</td>
                    <td class='userRole'>" . $row["role"] . "</td>
                    <td><a href='#' class='btn btn-warning text-white editUser' data-bs-toggle='modal' data-bs-target='#editUserModal' value='". $row["id"] ."'>Edit User</a></td>
                    <td><a href='#' class='btn btn-danger deleteUser' data-bs-toggle='modal' data-bs-target='#deleteUserModal'>Delete User</a></td>
                  </tr>";  
                    } 
                ?>
            </tbody>
        </table>
  </div>
</div>

  <!-- Add User Modal -->
  <div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="addUserForm" action="loadusers.php">
            <div class="mb-3">
              <label for="name" class="form-label">Name</label>
              <input type="text" class="form-control" id="username" required>
            </div>
            <div class="mb-3">
              <label for="email" class="form-label">Email</label>
              <input type="email" class="form-control" id="useremail" required>
            </div>
            <div class="mb-3">
              <label for="password" class="form-label">Password</label>
              <input type="password" class="form-control" id="userpassword" required>
            </div>
            <div class="mb-3">
              <label for="role" class="form-label">Role</label>
              <select class="form-select" id="userrole" required>
                <option value="Admin">Admin</option>
                <option value="User">User</option>
              </select>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-warning text-white" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-warning text-white" onclick="addUser()">Add User</button>
        </div>
      </div>
    </div>
  </div>

  
<!-- Add user Notification Modal -->
<div class="modal fade" id="addUserNotificationModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-center">
      <div class="modal-header">
        <h5 class="modal-title" id="addUserNotificationModalLabel">🎉 You’ve successfully added a new user!</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <a class="btn btn-warning text-white" data-bs-dismiss="modal">Okay</a>
      </div>
    </div>
  </div>
</div>

<!-- Add user Notification Modal -->
<div class="modal fade" id="updateUserNotificationModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-center">
      <div class="modal-header">
        <h5 class="modal-title" id="updateUserNotificationModalLabel">🎉 You’ve successfully updated user details!</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <a class="btn btn-warning text-white" data-bs-dismiss="modal">Okay</a>
      </div>
    </div>
  </div>
</div>


<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="editUserForm" action="admindatabaseopearations.php" method="post">
        <div class="modal-header">
          <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        
        <div class="modal-body">
          <div class="mb-3 d-none">
            <label for="editUserID" class="form-label">ID</label>
            <input type="number" class="form-control" id="editUserID" name="id" value="" required>
          </div>  
          <div class="mb-3">
            <label for="editUserName" class="form-label">Name</label>
            <input type="text" class="form-control" id="editUserName" name="name" value="" required>
          </div>
          <div class="mb-3">
            <label for="editUserEmail" class="form-label">Email</label>
            <input type="email" class="form-control" id="editUserEmail" name="email" required>
          </div>
          <div class="mb-3">
            <label for="editUserRole" class="form-label">Role</label>
            <select class="form-select" id="editUserRole" name="role" required>
              <option value="">Select role</option>
              <option value="Admin">Admin</option>
              <option value="User">User</option>
            </select>
          </div>
        </div>
        
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button id="saveUserChanges" name="saveUserChanges" type="submit" class="btn btn-success">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>



<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h5 class="modal-title" id="deleteUserModalLabel">Confirm Delete</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <!-- Modal Body -->
      <div class="modal-body">
        Are you sure you want to delete this item? This action cannot be undone.
      </div>

      <!-- Modal Footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="confirmUserDelete">Delete</button>
      </div>

    </div>
  </div>
</div>




<script>
   function addUser() {
      const name = $('#addUserForm').find('#username').val();
      const email = $('#addUserForm').find('#useremail').val();
      const password = $('#addUserForm').find('#userpassword').val();
      const role = $('#addUserForm').find('#userrole').val();
      $.ajax({
        url: "includes/admin/admindatabaseopearations.php",
        type: "POST",
        data: {operation: 'addUser', name: name, email: email, password: password, role: role},
        success: function(data){
          $('#addUserModal').modal('hide');
          $('#addUserNotificationModal').modal('show');
          $('#admin-content').html('');
          $('#admin-content').load('includes/admin/loadusers.php');
          
        }
      })
    }

 
    $('.editUser').on("click", function(){

        const userid = $(this).parent().siblings('.userID').text();
        const username = $(this).parent().siblings('.userName').text();
        const role = $(this).parent().siblings('.userRole').text();
        const useremail = $(this).parent().siblings('.userEmail').text();

        // setting the values on edit user modal
        $("#editUserModal").find("#editUserID").val(userid);
        $("#editUserModal").find("#editUserName").val(username);
        $("#editUserModal").find("#editUserEmail").val(useremail);
        $("#editUserModal").find("#editUserRole").val(role);
      
    })

    $('#saveUserChanges').on('click', function(){
      const userid = $("#editUserID").val();
      const username = $("#editUserName").val();
      const role = $("#editUserRole").val();
      const email = $("#editUserEmail").val();
    })

    $('#editUserForm').on('submit', function(e) {
      e.preventDefault();

      const userid = $("#editUserID").val();
      const username = $("#editUserName").val();
      const role = $("#editUserRole").val();
      const email = $("#editUserEmail").val();

      $.ajax({
        url: "includes/admin/admindatabaseopearations.php",
        method: "POST",
        data: {operation: "editUser", id: userid, name: username, role: role, email: email},
        success: function(data){
          $("#editUserModal").modal("hide");
          $('#updateUserNotificationModal').modal('show');
          $('#admin-content').html('');
          $('#admin-content').load('includes/admin/loadusers.php');
        }

      })
    });

    $(".deleteUser").on("click", function(){
      const userid = $(this).parent().siblings('.userID').text();
      $("#confirmUserDelete").on("click", function(){
        $.ajax({
          url: "includes/admin/admindatabaseopearations.php",
          method: "POST",
          data: {operation: "deleteUser", id: userid},
          success: function(data){
            $("#deleteUserModal").modal("hide");
            $('#admin-content').html('');
            $('#admin-content').load('includes/admin/loadusers.php');
          }
        })
      })
    })


</script>

