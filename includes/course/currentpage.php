<?php
    header('Content-Type: text/plain');
    require "../connect.php";

    $course_id;
    $outline_id;
    $module_number = 0;

    $course_id_query = "SELECT course_id FROM current_course_page";
    $course_id_result = mysqli_query($conn, $course_id_query);
    
    if(mysqli_num_rows($course_id_result) > 0){
        $row = mysqli_fetch_assoc($course_id_result);
        $course_id = $row["course_id"];
    }
  
    $outline_id_query = "SELECT outline_id FROM current_course_page";
    $outline_id_result = mysqli_query($conn, $outline_id_query);

    if(mysqli_num_rows($outline_id_result) > 0){
        $row = mysqli_fetch_assoc($outline_id_result);
        $outline_id = $row["outline_id"];
    }
    
    $module_number_query = "SELECT module_number FROM current_course_page";
    $module_number_result = mysqli_query($conn, $module_number_query);

    if(mysqli_num_rows($module_number_result) > 0){
        $row = mysqli_fetch_assoc($module_number_result);
        $module_number = $row["module_number"];
    }

    if(isset($_POST["changepage"])){
        echo trim($module_number);
    }
    
    exit;
 ?>   