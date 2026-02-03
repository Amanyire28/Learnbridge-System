/**
 * UGANDA CURRICULUM - COURSE DISPLAY SCRIPT
 * 
 * Updated to work with the new Uganda school subjects and syllabus units
 * Previously named "modules", now called "syllabus units" (Units 1-6)
 * 
 * Content types now include:
 * - Lesson notes (regular course content)
 * - Past papers (previous examination questions)
 * - Practice quizzes (self-assessment exercises)
 */

// Load course and display content
document.addEventListener("DOMContentLoaded", function() {
    // Reset first unit loaded flag when page loads
    window.firstUnitLoaded = false;
    
    // Get course_id from URL parameter
    const urlParams = new URLSearchParams(window.location.search);
    const course_id = urlParams.get('course_id');
    
    // Validate course_id
    const courseIdNum = parseInt(course_id) || 0;
    if (!course_id || courseIdNum <= 0) {
        console.error('Invalid or missing course_id:', course_id);
        alert("No course selected. Please go back and select a course.");
        window.location.href = "courses.php";
        return;
    }
    
    // Store course_id globally for use in other functions
    window.currentCourseId = courseIdNum;
    
    // Load course title and outline
    loadCourseTitle(courseIdNum);
    loadCourseOutline(courseIdNum);
    loadCourseNavButtons(courseIdNum);
});

// Load course title and navigation breadcrumb
function loadCourseTitle(course_id) {
    // Validate course_id
    course_id = parseInt(course_id) || 0;
    if (course_id <= 0) {
        console.error('Invalid course_id in loadCourseTitle:', course_id);
        return;
    }
    
    const url = window.API_BASE ? `${window.API_BASE}loadcoursetitle.php?course_id=${course_id}` : `includes/course/loadcoursetitle.php?course_id=${course_id}`;
    fetch(url)
        .then(response => response.text())
        .then(data => {
            try {
                const courseData = JSON.parse(data);
                if (courseData.error) {
                    console.error('Course title error:', courseData);
                    return;
                }
                
                // Update page titles
                document.querySelectorAll('.course-title').forEach(elem => {
                    elem.textContent = courseData.title;
                });
                
                // Store for later use
                window.courseData = courseData;
            } catch (e) {
                console.error('Error parsing course title:', e, data);
            }
        })
        .catch(error => console.error('Error loading course title:', error));
}

// Load syllabus units (course outline)
// Units are the main organizational units of a subject aligned to Uganda National Curriculum
function loadCourseOutline(course_id) {
    // Validate course_id
    course_id = parseInt(course_id) || 0;
    if (course_id <= 0) {
        console.error('Invalid course_id in loadCourseOutline:', course_id);
        return;
    }
    
    console.log('loadCourseOutline called with course_id:', course_id);
    const url = window.API_BASE ? `${window.API_BASE}loadcourseoutline.php?course_id=${course_id}` : `includes/course/loadcourseoutline.php?course_id=${course_id}`;
    fetch(url)
        .then(response => response.text())
        .then(data => {
            try {
                const outlineData = JSON.parse(data);
                console.log('Course outline data received:', outlineData);
                if (outlineData.error) {
                    console.error('Course outline error:', outlineData);
                    return;
                }
                
                // Update both desktop and mobile menus
                const outlineElements = document.querySelectorAll('.course-outline');
                
                outlineElements.forEach(outline => {
                    outline.innerHTML = '';
                    
                    outlineData.forEach((unit, index) => {
                        console.log(`Creating unit item for index ${index}:`, unit);
                        const unitItem = document.createElement('li');
                        unitItem.className = 'nav-item w-100 unit-item';
                        
                        // Use unit-specific click handler
                        unitItem.innerHTML = `
                            <a class="nav-link w-100 unit-link" 
                               data-course-id="${course_id}" 
                               data-outline-id="${unit.outline_id}"
                               data-module-number="${unit.module_number}"
                               onclick="loadUnitContent(event, ${course_id}, ${unit.outline_id}, ${unit.module_number})">
                                ${unit.module_title}
                            </a>
                        `;
                        
                        outline.appendChild(unitItem);
                    });
                });
                
                // Load first unit by default (only once, not for each menu)
                // Use a flag on window object to prevent duplicate loading across multiple calls
                if (outlineData.length > 0 && !window.firstUnitLoaded) {
                    window.firstUnitLoaded = true;
                    setTimeout(() => {
                        const firstUnit = outlineData[0];
                        console.log('Loading first unit by default:', firstUnit);
                        loadUnitContent(null, course_id, firstUnit.outline_id, firstUnit.module_number);
                    }, 500);
                }
            } catch (e) {
                console.error('Error parsing course outline:', e, data);
            }
        })
        .catch(error => console.error('Error loading outline:', error));
}

// Load unit content (lesson notes, past papers, or practice quizzes)
function loadUnitContent(event, course_id, outline_id, module_number) {
    // Validate parameters
    course_id = parseInt(course_id) || 0;
    outline_id = parseInt(outline_id) || 0;
    module_number = parseInt(module_number) || 0;
    
    if (course_id <= 0 || outline_id <= 0) {
        console.error('Invalid parameters in loadUnitContent:', {course_id, outline_id, module_number});
        return;
    }
    
    console.log(`loadUnitContent called with: course_id=${course_id} (type: ${typeof course_id}), outline_id=${outline_id} (type: ${typeof outline_id}), module_number=${module_number} (type: ${typeof module_number})`);
    
    if (event) {
        event.preventDefault();
    }
    
    // Store current outline ID for download functionality
    window.currentOutlineId = outline_id;
    sessionStorage.setItem('currentOutlineId', outline_id);
    
    // Show download button
    const downloadBtn = document.querySelector('.download-notes-btn');
    if (downloadBtn) {
        downloadBtn.style.display = 'inline-block';
    }
    
    // Update active nav item
    document.querySelectorAll('.unit-link').forEach(link => {
        link.classList.remove('active');
        if (parseInt(link.getAttribute('data-outline-id')) === outline_id) {
            link.classList.add('active');
        }
    });
    
    if (event && event.target) {
        event.target.classList.add('active');
    }
    
    // Load module title
    loadModuleTitle(course_id, outline_id, module_number);
    
    // Load notes/content
    loadNotes(course_id, outline_id);
    
    // Update navigation buttons
    updateNavButtons(course_id);
    
    // Update current page for progress tracking
    updateCurrentPage(course_id, outline_id, module_number);
}

// Load unit title (e.g., "Unit 1: Numbers to 1,000,000")
function loadModuleTitle(course_id, outline_id, module_number) {
    // Ensure parameters are numbers
    course_id = parseInt(course_id) || 0;
    outline_id = parseInt(outline_id) || 0;
    module_number = parseInt(module_number) || 0;
    
    console.log(`loadModuleTitle called with: course_id=${course_id}, outline_id=${outline_id}, module_number=${module_number}`);
    const url = window.API_BASE ? `${window.API_BASE}loadmoduletitle.php?outline_id=${outline_id}` : `includes/course/loadmoduletitle.php?outline_id=${outline_id}`;
    console.log('Fetching module title from:', url);
    console.log('Full URL string:', encodeURI(url));
    fetch(url, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json'
        }
    })
        .then(response => response.text())
        .then(data => {
            try {
                const titleData = JSON.parse(data);
                if (titleData.error) {
                    console.error('Module title error:', titleData);
                    return;
                }
                
                document.querySelectorAll('.module-title').forEach(elem => {
                    elem.textContent = titleData.title;
                });
                
                window.moduleData = {
                    outline_id: outline_id,
                    module_number: module_number,
                    title: titleData.title
                };
            } catch (e) {
                console.error('Error parsing module title:', e, data);
            }
        })
        .catch(error => console.error('Error loading module title:', error));
}

// Load unit content (lesson notes, past papers, or quizzes)
// Content type is now specified in the notes table: 'lesson', 'past_paper', 'practice_quiz'
function loadNotes(course_id, outline_id) {
    // Ensure parameters are numbers
    course_id = parseInt(course_id) || 0;
    outline_id = parseInt(outline_id) || 0;
    
    if (course_id <= 0 || outline_id <= 0) {
        console.error('Invalid parameters in loadNotes:', {course_id, outline_id});
        return;
    }
    
    console.log(`loadNotes called with: course_id=${course_id}, outline_id=${outline_id}`);
    const url = window.API_BASE ? `${window.API_BASE}loadnotes.php?course_id=${course_id}&outline_id=${outline_id}` : `includes/course/loadnotes.php?course_id=${course_id}&outline_id=${outline_id}`;
    console.log('Fetching notes from:', url);
    console.log('Full URL string:', encodeURI(url));
    fetch(url)
        .then(response => response.text())
        .then(data => {
            try {
                const notesData = JSON.parse(data);
                const notesContainer = document.getElementById('notes');
                
                if (!notesContainer) return;
                
                if (notesData.error) {
                    console.error('Notes error:', notesData);
                    notesContainer.innerHTML = '<p class="text-muted">No content available for this unit yet. Please check back soon.</p>';
                    return;
                }
                
                notesContainer.innerHTML = '';
                
                if (!Array.isArray(notesData) || notesData.length === 0) {
                    notesContainer.innerHTML = '<p class="text-muted">No content available for this unit yet. Please check back soon.</p>';
                    return;
                }
                
                // Group content by type
                const contentByType = {};
                notesData.forEach(note => {
                    const type = note.content_type || 'lesson';
                    if (!contentByType[type]) {
                        contentByType[type] = [];
                    }
                    contentByType[type].push(note);
                });
                
                // Display content with type indicators
                const typeLabels = {
                    'lesson': '📚 Lesson Notes',
                    'past_paper': '📋 Past Exam Paper',
                    'practice_quiz': '✏️ Practice Quiz'
                };
                
                Object.keys(contentByType).forEach(type => {
                    const label = typeLabels[type] || type;
                    const sectionTitle = document.createElement('div');
                    sectionTitle.className = 'unit-content-type-header mb-3';
                    sectionTitle.innerHTML = `<h4 class="text-secondary">${label}</h4>`;
                    notesContainer.appendChild(sectionTitle);
                    
                    contentByType[type].forEach(note => {
                        const noteSection = document.createElement('div');
                        noteSection.className = 'unit-content-section mb-4 p-3 bg-light rounded';
                        noteSection.innerHTML = `
                            <h5 class="card-title fw-bold">${note.section_title}</h5>
                            <div class="card-text mt-3">
                                ${note.section_content}
                            </div>
                        `;
                        notesContainer.appendChild(noteSection);
                    });
                });
            } catch (e) {
                console.error('Error parsing notes:', e, data);
            }
        })
        .catch(error => console.error('Error loading notes:', error));
}

// Load navigation buttons (Previous/Next unit)
function loadCourseNavButtons(course_id) {
    // Ensure course_id is valid
    course_id = parseInt(course_id) || 0;
    if (course_id <= 0) {
        console.error('Invalid course_id in loadCourseNavButtons:', course_id);
        return;
    }
    
    const url = window.API_BASE ? `${window.API_BASE}loadcoursenavbuttons.php?course_id=${course_id}` : `includes/course/loadcoursenavbuttons.php?course_id=${course_id}`;
    fetch(url)
        .then(response => response.text())
        .then(data => {
            try {
                const navData = JSON.parse(data);
                
                // Check for errors
                if (navData.error) {
                    console.error('Nav buttons error:', navData);
                    return;
                }
                
                const navContainer = document.querySelector('.course-nav-buttons');
                
                if (!navContainer) return;
                
                // Store units for navigation
                window.courseUnits = navData;
                
                // Create Previous/Next buttons based on current unit
                // These will be updated when a unit is loaded
                updateNavButtons(course_id);
                
                // Add mark complete button
                const completeBtn = document.createElement('button');
                completeBtn.className = 'btn btn-success ms-2';
                completeBtn.textContent = '✓ Mark Course as Complete';
                completeBtn.onclick = () => markCourseComplete(course_id);
                navContainer.appendChild(completeBtn);
            } catch (e) {
                console.error('Error parsing nav buttons:', e, data);
            }
        })
        .catch(error => console.error('Error loading nav buttons:', error));
}

// Update navigation buttons based on current unit
function updateNavButtons(course_id) {
    if (!window.courseUnits || !window.moduleData) return;
    
    const navContainer = document.querySelector('.course-nav-buttons');
    if (!navContainer) return;
    
    const currentOutlineId = window.moduleData.outline_id;
    const currentIndex = window.courseUnits.findIndex(unit => unit.outline_id === currentOutlineId);
    
    // Remove existing nav buttons (but keep complete button)
    const existingNavButtons = navContainer.querySelectorAll('.nav-btn-prev, .nav-btn-next');
    existingNavButtons.forEach(btn => btn.remove());
    
    // Create Previous button
    if (currentIndex > 0) {
        const prevUnit = window.courseUnits[currentIndex - 1];
        const prevBtn = document.createElement('button');
        prevBtn.className = 'btn btn-outline-primary nav-btn-prev me-2';
        prevBtn.textContent = '← Previous Unit';
        prevBtn.onclick = () => {
            loadUnitContent(null, course_id, prevUnit.outline_id, prevUnit.module_number);
        };
        navContainer.insertBefore(prevBtn, navContainer.firstChild);
    }
    
    // Create Next button
    if (currentIndex < window.courseUnits.length - 1) {
        const nextUnit = window.courseUnits[currentIndex + 1];
        const nextBtn = document.createElement('button');
        nextBtn.className = 'btn btn-outline-primary nav-btn-next me-2';
        nextBtn.textContent = 'Next Unit →';
        nextBtn.onclick = () => {
            loadUnitContent(null, course_id, nextUnit.outline_id, nextUnit.module_number);
        };
        navContainer.insertBefore(nextBtn, navContainer.firstChild);
    }
}

// Update current page for progress tracking
function updateCurrentPage(course_id, outline_id, module_number) {
    const data = {
        course_id: course_id,
        outline_id: outline_id,
        module_number: module_number
    };
    
    fetch('includes/course/updatecurrentpage.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams(data)
    })
    .catch(error => console.error('Error updating current page:', error));
}

// Mark course as complete
function markCourseComplete(course_id) {
    if (!confirm('Are you sure you want to mark this course as complete?')) {
        return;
    }
    
    fetch('includes/course/coursecompletion.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams({
            course_id: course_id
        })
    })
    .then(response => response.text())
    .then(data => {
        const result = JSON.parse(data);
        if (result.message) {
            alert('Congratulations! You have completed this course.');
            window.location.href = 'courses.php';
        } else if (result.error) {
            alert('Error: ' + result.error);
        }
    })
    .catch(error => console.error('Error completing course:', error));
}

// Load course data before redirect (for Continue Learning button)
function loadDataBeforeRedirect() {
    const courseId = document.querySelector('.coursecard')?.id.replace('coursecard', '');
    if (courseId) {
        window.location.href = `course.php?course_id=${courseId}`;
    }
}

// Load course from courses page
function loadCourse(course_id) {
    window.location.href = `course.php?course_id=${course_id}`;
}
// Download notes for current unit
function downloadNotes(outline_id, format = 'txt') {
    if (!outline_id || outline_id <= 0) {
        alert('Please select a unit first');
        return;
    }
    
    const downloadUrl = `includes/course/download-notes.php?outline_id=${outline_id}&format=${format}`;
    const link = document.createElement('a');
    link.href = downloadUrl;
    link.click();
}

// Show download options modal
function showDownloadOptions(outline_id) {
    if (!outline_id || outline_id <= 0) {
        alert('Please select a unit first');
        return;
    }
    
    // Create a simple dialog
    const format = confirm('Download as PDF? (Cancel for TXT format)');
    const selectedFormat = format ? 'pdf' : 'txt';
    downloadNotes(outline_id, selectedFormat);
}

// Setup download button listener
document.addEventListener('DOMContentLoaded', function() {
    const downloadBtn = document.querySelector('.download-notes-btn');
    if (downloadBtn) {
        downloadBtn.addEventListener('click', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const outlineId = sessionStorage.getItem('currentOutlineId') || 
                              (window.currentOutlineId ? window.currentOutlineId : 0);
            
            if (!outlineId || outlineId <= 0) {
                alert('Please select a unit first');
                return;
            }
            
            // Show options for PDF or TXT
            showDownloadOptions(outlineId);
        });
    }
});