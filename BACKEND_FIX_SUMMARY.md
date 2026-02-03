# Backend Parameter Passing Fixes - Uganda Curriculum Platform

## Overview
Fixed critical backend PHP files that were not properly receiving AJAX parameters, causing undefined variable errors and SQL syntax failures. All course loading operations now properly receive and process parameters.

## Files Fixed

### 1. **loadcoursetitle.php** ✅
**Problem:** Undefined variable `$course_id` from missing `currentpage.php` dependency
**Solution:**
- Changed from requiring `currentpage.php` to requiring `../connect.php`
- Now receives `course_id` via `$_GET['course_id']`
- Validates and sanitizes parameter as integer
- Returns JSON response instead of plain text
- Added error handling for missing/invalid course IDs

**Before:**
```php
require "currentpage.php";
$course_title_query = "SELECT course_title FROM courses WHERE course_id = $course_id";
```

**After:**
```php
$course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;
if($course_id <= 0) { echo json_encode(['error' => '...']); exit; }
echo json_encode(['title' => $row["course_title"]]);
```

---

### 2. **loadmoduletitle.php** ✅
**Problem:** Three undefined variables: `$outline_id`, `$course_id`, `$module_number`
**Solution:**
- Now receives only `outline_id` via `$_GET` (simplifies logic)
- Validates parameter as integer
- Returns JSON response with unit title
- Removed unnecessary parameters from query

**Before:**
```php
require "currentpage.php";
$module_title_query = "SELECT module_title FROM course_outline WHERE outline_id = $outline_id AND course_id = $course_id AND module_number = $module_number";
```

**After:**
```php
$outline_id = isset($_GET['outline_id']) ? (int)$_GET['outline_id'] : 0;
$module_title_query = "SELECT module_title FROM course_outline WHERE outline_id = $outline_id";
echo json_encode(['title' => $row["module_title"]]);
```

---

### 3. **loadcourseoutline.php** ✅
**Problem:** 
- Undefined `$course_id` variable
- Returned HTML instead of JSON (incompatible with JavaScript)
**Solution:**
- Now receives `course_id` via `$_GET`
- Validates parameter and returns JSON array
- Includes all necessary fields: `outline_id`, `module_number`, `module_title`, `module_link`
- Properly ordered by module_number

**Before:**
```php
echo "<li class='nav-item' id='" .$row["module_number"] ."'><a class='nav-link text-dark disabled href='" . $row["module_link"] . "'>" . $row["module_title"] ."</a></li>";
```

**After:**
```php
echo json_encode($units);  // Returns array of unit objects with all metadata
```

---

### 4. **loadnotes.php** ✅
**Problem:**
- Undefined `$course_id` and `$outline_id` variables
- Didn't support new `content_type` field (lesson/past_paper/practice_quiz)
- Returned HTML instead of JSON
**Solution:**
- Now receives both `course_id` and `outline_id` via `$_GET`
- Returns JSON array with `content_type` support
- Enables JavaScript to group content by type with visual indicators
- Included all necessary fields: note_id, section_title, section_content, content_type

**Before:**
```php
echo "<section><h6 class='fw-bolder'>" . $row["section_title"] . "</h6><p>" . $row["section_content"] . "</p><hr></section>";
```

**After:**
```php
echo json_encode($notes);  // Returns array with content_type: 'lesson'|'past_paper'|'practice_quiz'
```

---

### 5. **loadcoursenavbuttons.php** ✅
**Problem:** Complex logic depending on undefined `$module_number` variable
**Solution:**
- Now receives `course_id` via `$_GET`
- Returns JSON array of all units in the course
- Simplifies navigation by returning unit metadata
- JavaScript now handles button generation and logic

**Before:**
```php
require "currentpage.php";
if ($module_number == 1){ echo "..."; }
```

**After:**
```php
$course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;
$units_query = "SELECT outline_id, module_number FROM course_outline WHERE course_id = $course_id ORDER BY module_number ASC";
echo json_encode($units);  // Returns all units; JS handles UI
```

---

### 6. **updatecurrentpage.php** ✅
**Problem:** 
- Depended on undefined `$course_id` variable
- Complex conditional logic for different parameter names
**Solution:**
- Now receives `course_id`, `outline_id`, `module_number` via `$_POST`
- Requires user to be logged in (session check)
- Simplified to single UPDATE query
- Returns JSON response

**Before:**
```php
require "currentpage.php";
if(isset($_POST["courseid"])){ ... }
if(isset($_POST["moduleNumber"])){ ... }
```

**After:**
```php
$course_id = isset($_POST['course_id']) ? (int)$_POST['course_id'] : 0;
$outline_id = isset($_POST['outline_id']) ? (int)$_POST['outline_id'] : 0;
$module_number = isset($_POST['module_number']) ? (int)$_POST['module_number'] : 0;
$update_query = "UPDATE current_course_page SET course_id = $course_id, outline_id = $outline_id, module_number = $module_number WHERE user_id = $user_id LIMIT 1";
```

---

### 7. **coursecompletion.php** ✅
**Problem:** Undefined `$course_id` variable
**Solution:**
- Now receives `course_id` via `$_POST`
- Validates both user_id and course_id
- Returns JSON response for consistent API
- Provides meaningful error messages

**Before:**
```php
require "currentpage.php";
echo "You have already completed this course.";
```

**After:**
```php
$course_id = isset($_POST['course_id']) ? (int)$_POST['course_id'] : 0;
echo json_encode(['message' => 'You have already completed this course.']);
```

---

### 8. **assets/js/course_uganda.js** ✅
**Problem:** Expected HTML responses, needs to handle JSON
**Solution:**
- Updated `loadCourseTitle()` to parse JSON and display title
- Updated `loadCourseOutline()` to handle JSON unit array
- Updated `loadNotes()` to group content by `content_type` with emoji indicators:
  - 📚 Lesson Notes
  - 📋 Past Exam Papers
  - ✏️ Practice Quizzes
- Updated `loadModuleTitle()` to parse JSON
- Updated `loadCourseNavButtons()` to handle JSON unit array
- Updated `markCourseComplete()` to properly parse JSON response

**Key Changes:**
```javascript
// Now handles JSON from all backend files
const courseData = JSON.parse(data);
console.log(courseData.title);  // or courseData.error
```

---

## Parameter Flow Summary

### Course Loading Flow
```
JavaScript (course.php) 
  ↓ AJAX GET
Backend (loadcoursetitle.php?course_id=2)
  ↓ Parse $_GET['course_id']
Database Query
  ↓ Return JSON
JavaScript (parse JSON, display)
```

### Unit Content Loading Flow
```
JavaScript (loadUnitContent event)
  ↓ AJAX GET to loadnotes.php?course_id=2&outline_id=5
Backend (loadnotes.php)
  ↓ Parse $_GET parameters
Database Query (with content_type grouping)
  ↓ Return JSON array
JavaScript (group by type, display with indicators)
```

### Progress Tracking Flow
```
JavaScript (loadUnitContent event)
  ↓ AJAX POST to updatecurrentpage.php
Backend (updatecurrentpage.php)
  ↓ Parse $_POST, check session
Database UPDATE (current_course_page)
  ↓ Return JSON success/error
JavaScript (silently log for debugging)
```

---

## Testing Checklist

- [x] No undefined variable errors in PHP
- [x] No SQL syntax errors
- [x] Course title loads when viewing a subject
- [x] Syllabus units (course outline) display properly
- [x] Clicking a unit loads its content
- [x] Lesson notes display with 📚 indicator
- [x] Past papers display with 📋 indicator (if content_type='past_paper')
- [x] Practice quizzes display with ✏️ indicator (if content_type='practice_quiz')
- [x] Navigation between units works smoothly
- [x] Course completion button functions properly
- [x] Progress is tracked in database

---

## Notes

### Why currentpage.php was problematic
The original `currentpage.php` was designed to:
- Query a `current_course_page` database table
- Return course/unit data from that table

Problem: This table is meant to *track* progress, not *provide* course data. The course IDs should come from AJAX parameters, not from this tracking table.

### Benefits of these fixes
1. **No more undefined variable errors** - All files properly receive GET/POST parameters
2. **Better error handling** - Invalid parameters are detected and reported
3. **JSON responses** - Consistent API format with backend
4. **Content type support** - Past papers and quizzes now properly distinguished
5. **Security** - Parameters are validated as integers before use
6. **Maintainability** - Clear parameter flow and error messages

---

## Rollback Information
If needed to revert, the original broken versions used:
- `require "currentpage.php"` to get variables
- HTML echo statements for output
- No parameter validation
