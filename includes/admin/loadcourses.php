<?php
  require "../connect.php";
?>
<div class="row">
    <div class="col">
        <h3>Courses Management</h3>
    </div>
    <div class="col-12">
    <table class="table table-hover table-bordered caption-top mt-4" style="background-color: ghostwhite;">
            <caption>List of Courses</caption>
            <thead>
            <tr>
                <th>#</th>
                <th>Course Title</th>
                <th>Course Description</th>
                <th>Language/Framework</th>
                <th>Edit</th>
                <th>Delete</th>
            </tr>
            </thead>
            <tbody>
                <?php

                    $sql = "SELECT * FROM courses";
                    $result = mysqli_query($conn, $sql);
                    while($row = mysqli_fetch_assoc($result)){
                        echo "
                        <tr>  
                            <td class='courseID'>" . $row["course_id"] . "</td>
                            <td class='courseTitle'>" . $row["course_title"] . "</td>
                            <td class='courseDescription'>" . $row["course_description"] . "</td>
                            <td class='language'>" . $row["language"] . "</td>
                            <td><a href='#' class='btn btn-warning text-white editCourse' data-bs-toggle='modal' data-bs-target='#editCourseModal' value='". $row["course_id"] ."'>Edit Course</a></td>
                            <td><a href='#' class='btn btn-danger deleteCourse'  data-bs-toggle='modal' data-bs-target='#deleteCourseModal'>Delete Course</a></td>

                        </tr>";  
                    }   
                ?>     
            </tbody>
        </table>
    </div>
</div>



<!-- Edit Course Modal -->
<div class="modal fade" id="editCourseModal" tabindex="-1" aria-labelledby="editCourseModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <!-- Header -->
      <div class="modal-header">
        <h5 class="modal-title" id="editCourseModalLabel">Edit Course</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <!-- Body -->
      <div class="modal-body">
        <form id="editCourseForm">
          <input type="hidden" id="courseId" name="courseId">

          <!-- Title & Language Side by Side -->
          <div class="row g-3">
            <div class="col-md-7">
              <label for="courseTitle" class="form-label">Course Title</label>
              <input type="text" class="form-control" id="courseTitle" name="courseTitle" required>
            </div>
            <div class="col-md-5">
              <label for="courseLanguage" class="form-label">Language / Framework</label>
              <input type="text" class="form-control" id="courseLanguage" name="courseTitle" required>
            </div>
          </div>

          <!-- Description -->
          <div class="mt-1">
            <label for="courseDescription" class="form-label">Course Description</label>
            <textarea class="form-control" id="courseDescription" name="courseDescription" rows="1" required></textarea>
          </div>

          <!-- Module Select & Notes -->
          <div class="row mt-1 g-3">
            <div class="col">
              <label for="selectedModule" class="form-label">Select Module</label>
              <select class="form-select" id="selectedModule" name="selectedModule">
                <option disabled selected>Select a module</option>
                <!-- Filled via JS -->
                
              </select>
            </div>

            <div class="col">
              <label for="selectedSection" class="form-label">Select Section</label>
              <select class="form-select" id="selectedSection" name="selectedSection">
                <option disabled selected>Select a Section</option>
                <!-- Filled via JS -->
                
              </select>
            </div>
          
          </div>
          <div class="row mt-1 g-3">
            <div class="col">
              <label for="moduleNotes" class="form-label">Module Notes</label>
              <textarea class="form-control" id="moduleNotes" name="moduleNotes" rows="4" placeholder="Edit notes..." disabled></textarea>
            </div>
          </div>

        </form>
      </div>

      <!-- Footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button id="saveCourseChanges" type="submit" form="editCourseForm" class="btn btn-success">Save Changes</button>
      </div>

    </div>
  </div>
</div>

<!-- UpdateCourseNotoficationModal-->
<div class="modal fade" id="updateCourseNotificationModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-center">
      <div class="modal-header">
        <h5 class="modal-title" id="updateCourseNotificationModalLabel">🎉 You’ve successfully updated course details!</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <a class="btn btn-warning text-white" data-bs-dismiss="modal">Okay</a>
      </div>
    </div>
  </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteCourseModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h5 class="modal-title" id="deleteCourseModalLabel">Confirm Delete</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <!-- Modal Body -->
      <div class="modal-body">
        Are you sure you want to delete this course? This action cannot be undone.
      </div>

      <!-- Modal Footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="confirmCourseDelete">Delete</button>
      </div>

    </div>
  </div>
</div>



<script>
    $('.editCourse').on("click", function(){
        const courseid = $(this).parent().siblings('.courseID').text();
        const courseTitle = $(this).parent().siblings('.courseTitle').text();
        const courseDescription = $(this).parent().siblings('.courseDescription').text();
        const language = $(this).parent().siblings('.language').text();

        $("#courseId").val(courseid);
        $("#courseTitle").val(courseTitle);
        $("#courseDescription").val(courseDescription);
        $("#courseLanguage").val(language);

      
        $.ajax({
            url: "includes/admin/admindatabaseopearations.php",
            type: "POST",
            data: {operation: "editCourse", courseid: courseid},
            success: function(response){
                const modules = JSON.parse(response);
                console.log(modules);
                $("#selectedModule").html('');
                $("#selectedModule").append('<option disabled selected>Select a Module</option>');
                
                $("#selectedSection").html('');
                $("#selectedSection").append('<option disabled selected>Select a Section</option>');
                
                $("#moduleNotes").val('');

                $.each(modules, function(index, module){
                    $("#selectedModule").append(`<option value="${module}">${module}</option>`);
                })

            }
        })


    })

    $("#selectedModule").change(function(){
        let moduleTitle =  $(this).val();

        $.ajax({
            url: "includes/admin/admindatabaseopearations.php",
            type: "POST",
            data: {operation: "retrieveSections", moduleTitle: moduleTitle},
            success: function(data){
                let sectionTitles = JSON.parse(data);
                $("#selectedSection").html('');
                $("#selectedSection").append('<option disabled selected>Select a Section</option>'); 
                $("#moduleNotes").val('');

                $.each(sectionTitles, function(index, sectionTitle){
                    $("#selectedSection").append(`<option value="${sectionTitle}">${sectionTitle}</option>`);
                })
            }
        })


    })

    $("#selectedSection").change(function(){
        let sectionTitle =  $(this).val();

        $.ajax({
            url: "includes/admin/admindatabaseopearations.php",
            type: "POST",
            data: {operation: "retrieveNotes", sectionTitle: sectionTitle},
            success: function(data){
                let sectionContent = JSON.parse(data);
                $("#moduleNotes").val(sectionContent);
                $("#moduleNotes").prop("disabled", false);
        
            }
        })


    })

    $('#editCourseForm').on('submit', function(e) {
        e.preventDefault();

        const courseid = $("#courseId").val();
        const courseTitle = $("#courseTitle").val();
        const courseDescription = $("#courseDescription").val();
        const courseLanguage =  $("#courseLanguage").val();
        const moduleTitle = $("#selectedModule").val();
        const sectionTitle = $("#selectedSection").val();
        const sectionContent = $("#moduleNotes").val();

      $.ajax({
        url: "includes/admin/admindatabaseopearations.php",
        method: "POST",
        data: {
            operation: "updateCourseDetails", 
            courseid: courseid, 
            courseTitle: courseTitle, 
            courseDescription: courseDescription, 
            courseLanguage: courseLanguage,
            moduleTitle: moduleTitle,
            sectionTitle: sectionTitle,
            sectionContent: sectionContent 
        },
        success: function(data){
          $("#editCourseModal").modal("hide");
          $('#updateCourseNotificationModal').modal('show');
          $('#admin-content').html('');
          $('#admin-content').load('includes/admin/loadcourses.php');
        }

      })
    });

    $(".deleteCourse").on("click", function(){
      const courseid = $(this).parent().siblings('.courseID').text();
      $("#confirmCourseDelete").on("click", function(){
        $.ajax({
          url: "includes/admin/admindatabaseopearations.php",
          method: "POST",
          data: {operation: "deleteCourse", id: courseid},
          success: function(data){
            $("#deleteCourseModal").modal("hide");
            $('#admin-content').html('');
            $('#admin-content').load('includes/admin/loadcourses.php');
          }
        })
      })
    })

   

</script>




