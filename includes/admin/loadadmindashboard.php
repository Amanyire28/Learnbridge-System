<?php
    require "../connect.php";
?>
<div class="row">
    <h3 class="lead fs-4" style="color: #111161; font-weight: 600;">Dashboard Stats</h3>
    <?php
        $stats_queries = array(
            "Total Users" => "SELECT COUNT(*) AS result FROM users",
            "Total Courses" => "SELECT COUNT(*) AS result FROM courses",
            "Total Enrollments" => "SELECT COUNT(*) AS result FROM enrollments",
            "Completed Courses" => "SELECT COUNT(*) AS result FROM completed_courses"
        );

        $colors = array(
            "Total Users" => "linear-gradient(135deg, #667eea 0%, #764ba2 100%)",
            "Total Courses" => "linear-gradient(135deg, #f093fb 0%, #f5576c 100%)",
            "Total Enrollments" => "linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)",
            "Completed Courses" => "linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)"
        );

        foreach($stats_queries as $title => $query){
            $total_result = mysqli_query($conn, $query);
            if(mysqli_num_rows($total_result) > 0){
            $row = mysqli_fetch_assoc($total_result);
            echo '
            <div class="col-12 col-lg-3 my-2">
                <div class="card" style="border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.1); overflow: hidden;">
                    <div style="background: '. $colors[$title] .'; padding: 30px; text-align: center; color: white;">
                        <p style="font-size: 2.5rem; font-weight: bold; margin: 0;">'. $row["result"].'</p>
                        <h5 style="margin-top: 10px; margin-bottom: 0; font-weight: 600;">'. $title .'</h5>
                    </div>
                </div>
            </div>     
              ';
              }

            }

          ?>
          
</div>

<div class="row mt-4">
    <div class="col-12">
        <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
            <h5 style="color: #111161; font-weight: 600; margin-bottom: 20px;">Recent Users</h5>
            <table class="table table-hover table-striped">
                <thead style="background-color: #f8f9fa;">
                    <tr>
                      <th style="color: #111161;">ID</th>
                      <th style="color: #111161;">Name</th>
                      <th style="color: #111161;">Email</th>
                      <th style="color: #111161;">Role</th>
                    </tr>
                </thead>
                <tbody id="userTable">
                    <?php
                        $sql = "SELECT * FROM users LIMIT 5";
                        $result = mysqli_query($conn, $sql);
                        while($row = mysqli_fetch_assoc($result)){
                            echo "
                            <tr>  
                                <td>" . $row["id"] . "</td>
                                <td>" . $row["name"] . "</td>
                                <td>" . $row["email"] . "</td>
                                <td><span class='badge' style='background-color: #111161;'>" . $row["role"] . "</span></td>
                            </tr>";  
                        } 
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-12 mt-4">
        <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
            <h5 style="color: #111161; font-weight: 600; margin-bottom: 20px;">Recent Courses</h5>
            <table class="table table-hover table-striped">
                <thead style="background-color: #f8f9fa;">
                <tr>
                    <th style="color: #111161;">ID</th>
                    <th style="color: #111161;">Course Title</th>
                    <th style="color: #111161;">Description</th>
                    <th style="color: #111161;">Language/Framework</th>
                </tr>
                </thead>
                <tbody id="userTable">
                    <?php

                        $sql = "SELECT * FROM courses LIMIT 5";
                        $result = mysqli_query($conn, $sql);
                        while($row = mysqli_fetch_assoc($result)){
                            echo "
                            <tr>  
                                <td>" . $row["course_id"] . "</td>
                                <td><strong>" . $row["course_title"] . "</strong></td>
                                <td>" . substr($row["course_description"], 0, 50) . "...</td>
                                <td><span class='badge' style='background-color: #ffc107; color: #111161;'>" . $row["language"] . "</span></td>
                            </tr>";  
                        }   
                    ?>     
                </tbody>
            </table>
        </div>
    </div>
</div>