
<?php
  require "../connect.php";
?>
<div class="row">
  <div class="col">
    <h3>Messages</h3>
  </div>

  <div class="col-12">
    <table class="table table-hover table-bordered caption-top mt-4">
      <caption>List of Messages</caption>
      <thead>
        <tr>
          <th>#</th>
          <th>Name</th>
          <th>Email</th>
          <th>Message</th>
        </tr>
      </thead>
      <tbody>
        <?php
          $sql = "SELECT * FROM messages";
          $result = mysqli_query($conn, $sql);
          while($row = mysqli_fetch_assoc($result)){
            echo "
                  <tr>  
                    <td>" . $row["messageid"] . "</td>
                    <td>" . $row["name"] . "</td>
                    <td>" . $row["email"] . "</td>
                    <td>" . $row["message"] . "</td>
                  </tr>";  
                    } 
                ?>
            </tbody>
        </table>
  </div>
</div>