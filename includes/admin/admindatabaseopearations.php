<?php
    require "../connect.php";
    if(isset($_POST["operation"])){
        if($_POST["operation"] == "addUser"){
            $operation = $_POST["operation"];
            $name = $_POST["name"];
            $email = $_POST["email"];
            $password = $_POST["password"];
            $role = $_POST["role"];

            $sql = "INSERT INTO users(name, email, role, password) VALUES('$name', '$email', '$role', '$password')";
            if(mysqli_query($conn, $sql)){
                echo "Record inserted successfully";
            }
        }

        if($_POST["operation"] == "editUser"){
            $userID = $_POST["id"];
            $username = $_POST["name"];
            $email = $_POST["email"];
            $role = $_POST["role"];
            
            $sql = "UPDATE users SET name = '$username', email = '$email', role = '$role' WHERE id = $userID";
            if(mysqli_query($conn, $sql)){
                echo "Recorded updated successfully";
            }

        }

        if($_POST["operation"] == "deleteUser"){
            $userID =  $_POST["id"];
            $sql = "DELETE FROM users WHERE id = $userID";
            if(mysqli_query($conn, $sql)){
                echo "Recorded deleted successfully";
            }
        }

        if($_POST["operation"] == "editCourse"){
            $modules = array();
            $courseid = $_POST["courseid"];

            $sql1 = "SELECT module_title FROM course_outline WHERE course_id = $courseid";
            $result1 = mysqli_query($conn, $sql1);
            if(mysqli_num_rows($result1) > 0){
                while($row = mysqli_fetch_assoc($result1)){
                    array_push($modules, $row["module_title"]);
                }
           }
           echo json_encode($modules);
           exit;
        }

        if($_POST["operation"] == "retrieveSections"){
            $moduleTitle = $_POST["moduleTitle"];
            $sectionTitles = array();

            $sql2 = "SELECT section_title FROM notes LEFT JOIN course_outline ON notes.outline_id = course_outline.outline_id WHERE course_outline.module_title = '$moduleTitle'";
            $result2 = mysqli_query($conn, $sql2);
            if(mysqli_num_rows($result2) > 0){
                while($row = mysqli_fetch_assoc($result2)){
                    array_push($sectionTitles, $row["section_title"]);
                }
           }
 
           echo json_encode($sectionTitles);
           exit;
        }

        if($_POST["operation"] == "retrieveNotes"){
            $sectionTitle = $_POST["sectionTitle"];
            $sql = "SELECT section_content FROM notes WHERE section_title = '$sectionTitle'";
            $result = mysqli_query($conn, $sql);
            if(mysqli_num_rows($result) > 0){
                $row = mysqli_fetch_assoc($result);
           }

           echo json_encode($row["section_content"]);
           exit;
        }

        if($_POST["operation"] == "updateCourseDetails"){
            $courseid = $_POST["courseid"];
            $courseTitle = $_POST["courseTitle"];
            $courseDescription = $_POST["courseDescription"];
            $courseLanguage = $_POST["courseLanguage"];
            $moduleTitle = $_POST["moduleTitle"];
            $sectionTitle = $_POST["sectionTitle"];
            $sectionContent = $_POST["sectionContent"];

            $sql1 = "UPDATE courses SET course_title = '$courseTitle', course_description = '$courseDescription', language = '$courseLanguage' WHERE course_id = $courseid";
            if(mysqli_query($conn, $sql1)){
                echo "Record updated successfully";
            }
            
            if(!empty($moduleTitle) or !empty($sectionTitle) or !empty($sectionContent)){
                $sql2 = "UPDATE notes SET section_content = '$sectionContent' WHERE section_title = '$sectionTitle'";
                if(mysqli_query($conn, $sql2)){
                    echo "Record updated successfully";
                }
            }
         
           
        }
       


    }

   



?>