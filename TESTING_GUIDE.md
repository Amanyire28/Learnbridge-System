# Testing Guide - Uganda Curriculum Platform

## Quick Test Procedures

### Test 1: View a Course
**Steps:**
1. Login to the platform
2. Go to Courses page
3. Click on any subject (e.g., "Primary 6 English")
4. **Expected Result:** 
   - Course title displays at top and in breadcrumb
   - Syllabus units appear in left sidebar (Unit 1, Unit 2, etc.)
   - No JavaScript errors in console

**Check Console (F12 → Console):**
- Should see no errors like "Undefined variable" or "SQL syntax error"

---

### Test 2: Load Unit Content
**Steps:**
1. From course page, click on a unit in the sidebar (e.g., "Unit 1: ...")
2. **Expected Result:**
   - Unit title displays in breadcrumb
   - Notes/content appears in main area
   - Content is grouped by type with emoji indicators:
     - 📚 Lesson Notes (if any exist)
     - 📋 Past Exam Papers (if content_type='past_paper')
     - ✏️ Practice Quizzes (if content_type='practice_quiz')

**Check Console (F12 → Network):**
- `loadnotes.php?course_id=X&outline_id=Y` should return 200 OK
- Response should be valid JSON (not HTML)

---

### Test 3: Navigate Between Units
**Steps:**
1. From course page, click different units one after another
2. **Expected Result:**
   - Unit title and content change smoothly
   - No loading errors
   - Progress tracking updates silently

**Check Console (F12 → Network):**
- `updatecurrentpage.php` POST should show 200 OK response
- Response should be JSON with "message" key

---

### Test 4: Mark Course Complete
**Steps:**
1. Navigate to last unit in a course
2. Click "Mark Course as Complete" button
3. Confirm in dialog
4. **Expected Result:**
   - Success message appears
   - User redirected to courses page
   - Course shows as completed

**Check Console (F12 → Network):**
- `coursecompletion.php` POST should return 200 OK
- Response should contain `{"message": "..."}`

---

## API Testing (Using Browser Console or cURL)

### Test loadcoursetitle.php
```javascript
// In browser console (F12 → Console):
fetch('includes/course/loadcoursetitle.php?course_id=1')
  .then(r => r.text())
  .then(d => console.log(JSON.parse(d)))
```

**Expected Response:**
```json
{"title": "Primary 6 English"}
```

---

### Test loadcourseoutline.php
```javascript
fetch('includes/course/loadcourseoutline.php?course_id=1')
  .then(r => r.text())
  .then(d => console.log(JSON.parse(d)))
```

**Expected Response:**
```json
[
  {"outline_id": 1, "module_number": 1, "module_title": "Unit 1: Communication Skills", "module_link": "#"},
  {"outline_id": 2, "module_number": 2, "module_title": "Unit 2: Grammar", "module_link": "#"},
  ...
]
```

---

### Test loadnotes.php
```javascript
fetch('includes/course/loadnotes.php?course_id=1&outline_id=1')
  .then(r => r.text())
  .then(d => console.log(JSON.parse(d)))
```

**Expected Response:**
```json
[
  {
    "note_id": 1,
    "section_title": "Introduction to Verbs",
    "section_content": "<p>A verb is a word that...</p>",
    "content_type": "lesson"
  },
  {
    "note_id": 2,
    "section_title": "2022 Exam Paper",
    "section_content": "<p>Question 1: ...</p>",
    "content_type": "past_paper"
  },
  ...
]
```

---

### Test loadmoduletitle.php
```javascript
fetch('includes/course/loadmoduletitle.php?outline_id=1')
  .then(r => r.text())
  .then(d => console.log(JSON.parse(d)))
```

**Expected Response:**
```json
{"title": "Unit 1: Communication Skills"}
```

---

### Test updatecurrentpage.php
```javascript
fetch('includes/course/updatecurrentpage.php', {
  method: 'POST',
  headers: {'Content-Type': 'application/x-www-form-urlencoded'},
  body: new URLSearchParams({course_id: 1, outline_id: 1, module_number: 1})
})
  .then(r => r.text())
  .then(d => console.log(JSON.parse(d)))
```

**Expected Response:**
```json
{"message": "Progress updated successfully"}
```

---

## Common Issues & Solutions

### Issue: "Undefined variable" errors still appearing
**Cause:** Browser cache
**Solution:** Hard refresh (Ctrl+Shift+R or Cmd+Shift+R)

### Issue: JSON.parse error in console
**Cause:** Backend returned HTML error instead of JSON
**Solution:** Check network tab to see actual response, look for PHP errors

### Issue: Course content not displaying
**Cause:** May not have any notes in database for this unit
**Solution:** Check database:
```sql
SELECT COUNT(*) FROM notes WHERE course_id = 1 AND outline_id = 1;
```
If result is 0, add test data or use empty state message.

### Issue: Content types not showing indicators
**Cause:** Notes don't have content_type set (column may be NULL)
**Solution:** Update notes table:
```sql
UPDATE notes SET content_type = 'lesson' WHERE content_type IS NULL;
```

---

## Browser Console Debugging

### View all API calls
```javascript
// Run this in console to monitor all fetch requests
const originalFetch = window.fetch;
window.fetch = function(...args) {
  console.log('API Call:', args[0], args[1] || '');
  return originalFetch.apply(this, args);
};
```

### Check what course_id is being used
```javascript
const urlParams = new URLSearchParams(window.location.search);
console.log('Current course_id:', urlParams.get('course_id'));
```

### Verify window.courseData is set
```javascript
console.log('Course data:', window.courseData);
console.log('Module data:', window.moduleData);
```

---

## Database Verification

### Check if content_type column exists in notes table
```sql
DESCRIBE notes;
```
Should show `content_type` column with type `enum('lesson','past_paper','practice_quiz')`

### Check course outline for a specific course
```sql
SELECT outline_id, module_number, module_title FROM course_outline 
WHERE course_id = 1 
ORDER BY module_number;
```

### Check notes for a specific unit
```sql
SELECT note_id, section_title, content_type FROM notes 
WHERE course_id = 1 AND outline_id = 1 
ORDER BY note_id;
```

### Check current_course_page is being updated
```sql
SELECT user_id, course_id, outline_id, module_number FROM current_course_page 
WHERE user_id = [YOUR_USER_ID];
```

### Check course completion tracking
```sql
SELECT * FROM completed_courses WHERE course_id = 1;
```

---

## Performance Notes

- All course loading operations should complete in < 500ms
- Database queries are indexed on course_id, outline_id
- JSON responses are small (typically < 10KB)
- No recursive queries

---

## Post-Fix Verification Checklist

- [ ] No "Undefined variable" PHP warnings
- [ ] No "SQL syntax error" messages
- [ ] Course titles load in < 1 second
- [ ] Unit outlines display as JSON list
- [ ] Unit content loads with emoji indicators
- [ ] Navigation between units works smoothly
- [ ] Course completion updates database
- [ ] Browser console shows no JavaScript errors
- [ ] All API endpoints return valid JSON
- [ ] Database shows updated current_course_page
- [ ] Completed courses are logged in completed_courses table

---

## Success Indicators

When everything is working correctly:

1. **User Experience**
   - Students can view all 40 Uganda curriculum subjects
   - Each subject shows its syllabus units (Module 1-6 or similar)
   - Clicking units loads content smoothly
   - Content is organized by type (lesson/past paper/quiz)

2. **No Console Errors**
   - Browser console (F12) is clean
   - Network tab shows all requests with 200 OK status
   - All responses are valid JSON

3. **Database State**
   - current_course_page table updates as students navigate
   - completed_courses table records course completions
   - All student progress is tracked

4. **Performance**
   - Page loads feel snappy (< 1s per unit change)
   - No slow database queries
   - No memory leaks in browser

---

**If all these checks pass, the backend parameter passing fixes are working correctly!**
