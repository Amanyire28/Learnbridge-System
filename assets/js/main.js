$(document).ready(function(){
    // Check if we're on the new course.php page (handled by course_uganda.js)
    const isNewCoursePage = document.body.getAttribute('data-course-page') === 'true' ||
                           document.querySelector('script[src*="course_uganda.js"]') !== null ||
                           (window.location.pathname.toLowerCase().includes('course.php') && window.location.search.includes('course_id'));
    
    $(document).on("mouseover", ".dropdown-item", function(){
        $(this).removeClass("text-white").addClass("text-warning");
        $(this).css("background-color", "rgb(17, 17, 97)");
    })

      $(document).on("mouseout", ".dropdown-item", function(){
        $(this).removeClass("text-warning").addClass("text-white");
        $(this).css("background-color", "rgb(17, 17, 97)");
    })


    $("nav").find(".nav-link").mouseover(function(){
        $(this).removeClass("text-white").addClass("text-warning");
    })

    $("nav").find(".nav-link").mouseout(function(){
        $(this).removeClass("text-warning").addClass("text-white");
    })

    $("#signin, #signup, #searchbtn, #logout").mouseover(function(){
        $(this).removeClass("btn-outline-light").addClass("btn-outline-warning");
    })

    $("#signin, #signup, #searchbtn, #logout").mouseout(function(){
        $(this).removeClass("btn-outline-warning").addClass("btn-outline-light");
    })

    $(".course-menu-icon").click(function(){
        if ($(this).hasClass("visible")){
            $(this).next().hide();
            $(this).parent().removeClass("col-3").addClass("col-1");
            $(this).find("span").text("Show Menu");
            $(this).removeClass("visible");
        }
        else{
            $(this).next().show();
            $(this).parent().removeClass("col-1").addClass("col-3");
            $(this).find("span").text("Hide Menu");
            $(this).addClass("visible");
        }
    })

      // Skip old course loading code if we're on course.php (handled by course_uganda.js)
      // Don't run old course loading code on course.php page
      if (!isNewCoursePage) {
        const courseid = sessionStorage.getItem('courseid');
        if (courseid){
          $.ajax({
              url: 'includes/course/updatecurrentpage.php',
              type: 'POST',
              dataType: 'text',
              data: { courseid: courseid },
              success: function(data) {
                  // Display the results as a dropdown
                  $("#notes").load("includes/course/loadnotes.php");
                  $(".course-title").load("includes/course/loadcoursetitle.php");
                  $(".module-title").load("includes/course/loadmoduletitle.php");
                  $(".course-outline").load("includes/course/loadcourseoutline.php");
                  $(".course-nav-buttons").load("includes/course/loadcoursenavbuttons.php");    
              },
              error: function(xhr, status, error){
                alert(error);
              }
          });
        }
      }

 
    $(".course-outline").on("mouseover", ".nav-item", function(){
        $(this).css('background-color', 'aliceblue');
    })


    $(".course-outline").on("mouseout", ".nav-item", function(){
        $(this).css('background-color', 'white');
    })

    $(document).on('click', '#completeCourseBtn', function() {
        $.ajax({
            url: 'includes/course/coursecompletion.php',
            type: 'POST',
            dataType: 'text',
            data: { completed: true},
            success: function(data) {
                // Display the results as a dropdown
                // window.location.href = "courses.php";
            }
        });
      });

    function changemodule(moduleNumber){
        // Skip if we're on course.php (handled by course_uganda.js)
        if (isNewCoursePage) {
            return;
        }
        
        $.ajax({
            url: 'includes/course/updatecurrentpage.php',
            type: 'POST',
            dataType: 'text',
            data: { moduleNumber: (parseInt(moduleNumber))},
            success: function(data) {
                // Display the results as a dropdown
                $("#notes").load("includes/course/loadnotes.php");
                $(".course-title").load("includes/course/loadcoursetitle.php");
                $(".module-title").load("includes/course/loadmoduletitle.php");
                $(".course-nav-buttons").load("includes/course/loadcoursenavbuttons.php");    
            
            }
        });
    }

    function currentmodule() {
        const changepage = true;
    
        return new Promise((resolve, reject) => {
            $.ajax({
                url: 'includes/course/currentpage.php',
                type: 'POST',
                dataType: 'text',
                data: { changepage: changepage },
                success: function(data) {
                    resolve(parseInt(data)); // Successfully resolve the Promise
                },
                error: function(xhr, status, error) {
                    reject(error); // Reject on error
                }
            });
        });
    }
   
    
    // Use the function - only on course.php page, not on courses.php
    if (isNewCoursePage) {
        currentmodule().then(result => {
            console.log("Returned value:", result); // Now you can use the result here
        }).catch(error => {
            console.error("AJAX error:", error);
        });
    }
    

    // Only bind this event handler if not on course.php (course_uganda.js handles it there)
    if (!isNewCoursePage) {
        $(".course-outline").on("click", ".nav-item", function(){
            let moduleNumber =  $(this).attr('id');
            changemodule(moduleNumber);
        });
    }

    // Only bind this event handler if not on course.php (course_uganda.js handles it there)
    if (!isNewCoursePage) {
        $(".course-nav-buttons").on("click", "button", async function() {
            try {
                // Await the current module value
                const currentcoursemodule = await currentmodule();
        
                // Handle the navigation logic
                if ($(this).hasClass('next')) {
                    changemodule(currentcoursemodule + 1);
                } else if ($(this).hasClass('previous')) {
                    changemodule(currentcoursemodule - 1); // Assuming you want to decrement for previous
                }
        
            } catch (error) {
                console.error("Error fetching current module:", error);
            }
        });
    }
    


    $('#searchcourse').on('input', function() {
        let query = $(this).val();

        if (query.length > 0) {
            // Make AJAX request to search.php with the query
            $.ajax({
                url: 'includes/courses/searchresults.php',
                type: 'GET',
                dataType: 'html',
                data: { query: query },
                success: function(data) {
                    // Display the results as a dropdown
                    $('#suggestions').html(data).show();
                
                }
            });
        } else {
            // Hide the suggestions if the input is empty
            $('#suggestions').hide();
        }
    });

    // Note: unenroll-btn uses Bootstrap's data-bs-toggle, so no custom handler needed

    $('#admin-content').html('');
    $('#admin-content').load('includes/admin/loadadmindashboard.php');
    sessionStorage.setItem("currentadminpage", "dashboard");
    $(".dashboard").css("background-color", "aliceblue");

    function updateAdminNavStylings(){
        if(sessionStorage.getItem("currentadminpage") == "dashboard"){
            $(".dashboard").css("background-color", "aliceblue");
            $(".users").css("background-color", "white");
            $(".courses").css("background-color", "white");
            $(".messages").css("background-color", "white");
        }
        else if(sessionStorage.getItem("currentadminpage") == "users"){
            $(".dashboard").css("background-color", "white");
            $(".users").css("background-color", "aliceblue");
            $(".courses").css("background-color", "white");
            $(".messages").css("background-color", "white");
        }
        else if(sessionStorage.getItem("currentadminpage") == "courses"){
            $(".dashboard").css("background-color", "white");
            $(".users").css("background-color", "white");
            $(".courses").css("background-color", "aliceblue");
            $(".messages").css("background-color", "white");

        }
        else if(sessionStorage.getItem("currentadminpage") == "messages"){
            $(".dashboard").css("background-color", "white");
            $(".users").css("background-color", "white");
            $(".courses").css("background-color", "white");
            $(".messages").css("background-color", "aliceblue");

        }



    }

    $('.dashboard, #admin').on('click', function(){
        $('#admin-content').html('');
        $('#admin-content').load('includes/admin/loadadmindashboard.php');
        sessionStorage.setItem("currentadminpage", "dashboard");
        updateAdminNavStylings();
    })

    $('.users').on('click', function(){
        $('#admin-content').html('');
        $('#admin-content').load('includes/admin/loadusers.php');
        sessionStorage.setItem("currentadminpage", "users");
        updateAdminNavStylings();
    })

    $('.courses').on('click', function(){
        $('#admin-content').html('');
        $('#admin-content').load('includes/admin/loadcourses.php');
        sessionStorage.setItem("currentadminpage", "courses");
        updateAdminNavStylings();
    })

    $('.messages').on('click', function(){
        $('#admin-content').html('');
        $('#admin-content').load('includes/admin/loadmessages.php');
        sessionStorage.setItem("currentadminpage", "messages");
        updateAdminNavStylings();
    })

});
