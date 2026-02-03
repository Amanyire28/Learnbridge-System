# Skills Quest - Uganda Curriculum Implementation Guide

**Date:** January 10, 2026  
**Platform:** Skills Quest Learning Management System  
**Target:** Primary 6 - Secondary 4 (Uganda National Curriculum)

---

## Quick Start

### 1. Run the Migration
Open your browser and navigate to:
```
http://localhost/Skills%20Quest/migrate_to_uganda_curriculum.php
```

This will:
- ✅ Clear old programming courses
- ✅ Add 40 Uganda school subjects
- ✅ Create syllabus units for each subject
- ✅ Add sample lesson content
- ✅ Enable support for past papers and quizzes

**Expect:** 2-3 seconds to complete

### 2. Verify the Installation
Visit the courses page:
```
http://localhost/Skills%20Quest/courses.php
```

You should see subjects grouped by education level:
- Primary 6
- Primary 7
- Secondary 1
- Secondary 2
- Secondary 3
- Secondary 4

---

## What Changed

### Files Modified
| File | Changes |
|------|---------|
| `courses.php` | Displays subjects grouped by education level |
| `includes/header.php` | Updated navbar title to show "Uganda Edition" |
| `index.php` | Updated hero section and features to match Uganda curriculum |

### Files Created
| File | Purpose |
|------|---------|
| `migrate_to_uganda_curriculum.php` | One-time migration script |
| `assets/js/course_uganda.js` | JavaScript for handling content types (lesson/past_paper/quiz) |
| `UGANDA_CURRICULUM_README.md` | Detailed documentation |
| `IMPLEMENTATION_GUIDE.md` | This file |

### Files Unchanged
All backend files continue to work without modification:
```
includes/course/loadcoursetitle.php
includes/course/loadcourseoutline.php
includes/course/loadnotes.php
includes/course/updatecurrentpage.php
includes/course/coursecompletion.php
includes/courses/enroll.php
includes/connect.php
```

---

## Database Changes Summary

### Before Migration
```
Courses: HTML, CSS, Python, Java, jQuery, Bootstrap, VB.NET, SQL (8 total)
Modules: Coding tutorials (45 total)
Content: Programming lesson notes
```

### After Migration
```
Subjects: English, Math, Science, Biology, Chemistry, Physics, History, Geography, Computer Studies (40 total)
Syllabus Units: Subject-specific units (5-6 per subject)
Content Types:
  - Lesson notes (regular content)
  - Past papers (exam questions)
  - Practice quizzes (self-assessment)
```

### SQL Change
```sql
-- ADDED column to notes table:
ALTER TABLE notes ADD COLUMN content_type ENUM('lesson', 'past_paper', 'practice_quiz') DEFAULT 'lesson';
```

---

## Content Organization

### Example: Mathematics - Primary 6

```
Subject: Mathematics - Primary 6
├─ Unit 1: Numbers to 1,000,000
│  ├─ Lesson: Reading and Writing Large Numbers
│  ├─ Lesson: Comparing Numbers
│  └─ Practice Quiz: Place Value Quiz
├─ Unit 2: Operations and Problem Solving
│  ├─ Lesson: Addition and Subtraction
│  ├─ Lesson: Multiplication and Division
│  └─ Past Paper: P6 Math End-of-Year 2023
├─ Unit 3: Fractions and Decimals
│  ├─ Lesson: Introduction to Fractions
│  └─ Practice Quiz: Fraction Basics
└─ Unit 4-5: Additional Units...
```

### Example: Chemistry - S.4 (UCE Level)

```
Subject: Chemistry - S.4
├─ Unit 1: Atomic Structure and Bonding
│  ├─ Lesson: Atomic Models
│  ├─ Lesson: Chemical Bonding Types
│  └─ Practice Quiz: Atomic Structure Quiz
├─ Unit 2: States of Matter
│  └─ Lesson: Properties of Solids, Liquids, Gases
├─ Unit 3: Chemical Reactions
│  ├─ Lesson: Types of Chemical Reactions
│  └─ Past Paper: UCE Chemistry 2023 - Paper 1
├─ Unit 4: Organic Chemistry
│  ├─ Lesson: Hydrocarbons
│  └─ Past Paper: UCE Chemistry 2023 - Paper 2
└─ Unit 5-6: Additional Units...
```

---

## How to Add Content

### Adding a Lesson Note
```php
// Step 1: Identify the course_id and outline_id
// Course IDs: 1-10 (Primary 6), 11-20 (Primary 7), etc.
// Outline IDs: Available in course_outline table

// Step 2: Insert the lesson
INSERT INTO notes (course_id, outline_id, section_title, section_content, content_type)
VALUES (
    2,                          // Course ID: Mathematics - Primary 6
    6,                          // Unit 2 (Operations)
    'Multiplication Strategies',
    'Multiplication can be done using several strategies:
     1. Array method: Arrange numbers in rows and columns
     2. Repeated addition: 3 × 4 = 4 + 4 + 4
     3. Number line method: Jump 4 units, 3 times
     ...',
    'lesson'                    // Content type
);
```

### Adding a Past Paper
```php
INSERT INTO notes (course_id, outline_id, section_title, section_content, content_type)
VALUES (
    36,                         // Course ID: Mathematics - S.4
    145,                        // Relevant unit
    'Uganda Certificate of Education - Mathematics 2023 Paper 1',
    'SECTION A: Multiple Choice (20 marks)
     1. Solve: 2x + 5 = 13
        a) 2    b) 4    c) 6    d) 8
        Answer: b
     
     2. Find the area of a circle with radius 7cm
        a) 154 cm²   b) 49 cm²   c) 98 cm²   d) 22 cm²
        Answer: a
     
     SECTION B: Short Answer (30 marks)
     3. Solve the quadratic equation: x² - 5x + 6 = 0
     ...',
    'past_paper'                // Content type
);
```

### Adding a Practice Quiz
```php
INSERT INTO notes (course_id, outline_id, section_title, section_content, content_type)
VALUES (
    13,                         // Course ID: Biology - S.1
    52,                         // Cell Structure unit
    'Cell Structure Quiz - Quick Assessment',
    'QUIZ: Cell Structure and Function
     Questions: 5 | Time: 10 minutes | Passing Score: 70%
     
     Q1: What is the powerhouse of the cell?
     a) Nucleus
     b) Mitochondria ✓ (Correct)
     c) Ribosome
     d) Chloroplast
     
     Q2: Which organelle is responsible for photosynthesis?
     a) Mitochondria
     b) Lysosome
     c) Chloroplast ✓ (Correct)
     d) Golgi apparatus
     
     ...',
    'practice_quiz'             // Content type
);
```

---

## Content Type Indicators

When students view a unit, content is grouped and labeled by type:

### Visual Indicators
```
📚 Lesson Notes       - Regular educational content
📋 Past Exam Paper    - Previous examination questions
✏️  Practice Quiz      - Self-assessment exercises
```

### Example Display
```
[Unit 3: Fractions and Decimals]

📚 Lesson Notes
├─ Understanding Fractions
│  Introduction to fractions, proper and improper fractions...
├─ Decimal Notation
│  Converting decimals to fractions, decimal places...
└─ Converting Between Fractions and Decimals
   Step-by-step guide with examples...

📋 Past Exam Paper
├─ Primary 6 Mathematics - End of Year 2022
   PART A: Multiple Choice...
   PART B: Short Answer...

✏️  Practice Quiz
├─ Fractions Basics Quiz
   Q1: What does 3/4 represent?
   Q2: Convert 0.75 to a fraction...
```

---

## Managing Students

### Enrollment Flow
1. Student visits `courses.php`
2. Browses subjects by education level
3. Clicks "Enroll Now" for a subject
4. Appears in "Enrolled Courses" tab
5. Can access all units within the subject

### Progress Tracking
- Last accessed unit is remembered
- Module completion status tracked
- Course completion date recorded
- No performance metrics yet (planned feature)

---

## Administrative Tasks

### View All Subjects (as Admin)
Visit the admin dashboard:
```
http://localhost/Skills%20Quest/admin.php
```

Navigation shows:
- Dashboard
- Users
- Courses (shows all 40 subjects)
- Messages

### Add New Subject
Contact database admin or use phpMyAdmin:
```sql
INSERT INTO courses (course_title, course_description, course_image_url, language, rating, created_at, updated_at)
VALUES (
    'Subject Title - Education Level',
    'Subject description aligned with NCDF',
    'assets/images/subject_image.avif',
    'Education Level',  -- e.g., 'Secondary 2'
    5,
    NOW(),
    NOW()
);
```

### Add Syllabus Units to a Subject
```sql
INSERT INTO course_outline (course_id, module_number, module_title, module_link)
VALUES (
    1,              -- Course ID
    1,              -- Unit number
    'Unit 1: Topic Title',
    'unit-1-slug'
);
```

---

## Technical Details

### Database Table Structure

**courses** (40 entries)
```
course_id | course_title | course_description | language | rating
```
Note: `language` field now stores education level (Primary 6, S.1, etc.)

**course_outline** (200+ entries)
```
outline_id | course_id | module_number | module_title | module_link
```

**notes** (500+ entries)
```
note_id | course_id | outline_id | section_title | section_content | content_type
```
Content types: 'lesson', 'past_paper', 'practice_quiz'

### API Endpoints
All endpoints return JSON:

```
GET includes/course/loadcoursetitle.php?course_id=X
    → Returns: {title: "Subject Name"}

GET includes/course/loadcourseoutline.php?course_id=X
    → Returns: [{outline_id, module_number, module_title}, ...]

GET includes/course/loadnotes.php?course_id=X&outline_id=Y
    → Returns: [{section_title, section_content, content_type}, ...]

POST includes/course/updatecurrentpage.php
    → Params: course_id, outline_id, module_number
    → Updates user's current position

POST includes/course/coursecompletion.php
    → Params: course_id
    → Marks course as complete
```

---

## Troubleshooting

### Issue: Subjects not appearing after migration
**Solution:** 
- Refresh browser (Ctrl+F5)
- Check that migration ran successfully (no errors in output)
- Verify database connection: `includes/connect.php`

### Issue: Old programming courses still showing
**Solution:**
- Migration may not have completed
- Try migration again: `migrate_to_uganda_curriculum.php`
- Check database: `SELECT COUNT(*) FROM courses;` (should be 40)

### Issue: Content types (past papers, quizzes) not showing
**Solution:**
- Verify `content_type` column exists: `SHOW COLUMNS FROM notes;`
- If missing, add it: `ALTER TABLE notes ADD COLUMN content_type ENUM('lesson', 'past_paper', 'practice_quiz') DEFAULT 'lesson';`

### Issue: Images not loading
**Solution:**
- Verify image files exist in `assets/images/`
- Update course record with correct image URL
- Common images: english.avif, math.avif, science.avif, biology.avif, chemistry.avif, physics.avif, history.avif, geography.avif, computer.avif

---

## Next Steps / Roadmap

### Phase 2 (Future Implementation)
- [ ] Interactive quiz engine with auto-grading
- [ ] Performance tracking by subject and unit
- [ ] Teacher dashboard for adding/editing content
- [ ] PDF download for past papers
- [ ] Discussion forums per subject
- [ ] Mobile app (offline support planned)

### Phase 3 (Future Implementation)
- [ ] Video lesson integration
- [ ] Adaptive learning paths
- [ ] Performance analytics for teachers
- [ ] Certification system
- [ ] Parent/Guardian portal

---

## Support & References

### Uganda Educational Resources
- [Uganda National Curriculum Development Centre](https://www.ncdc.go.ug/)
- [Uganda National Examinations Board (UNEB)](https://www.uneb.ac.ug/)
- [Ministry of Education, Uganda](https://education.go.ug/)

### Technical Support
For technical issues:
1. Check error logs in browser console (F12)
2. Verify database connection
3. Ensure all files are in correct locations
4. Clear browser cache and try again

### Contact
For questions about curriculum alignment or content:
- Consult Uganda National Curriculum Framework documents
- Contact UNEB for exam-related questions
- Use admin contact form on the platform

---

## Checklist: After Migration

- [ ] Run migration script successfully
- [ ] Verify 40 subjects appear on courses.php
- [ ] Subjects grouped by education level (Primary 6-7, S.1-4)
- [ ] Sample lesson content loads without errors
- [ ] Enroll in a subject and verify unit content displays
- [ ] Test navigation through units
- [ ] Verify "Mark as Complete" functionality
- [ ] Check mobile responsiveness
- [ ] Test with different user accounts

---

**Status:** ✅ Ready for Student Use  
**Version:** 1.0 - Uganda Edition  
**Last Updated:** January 10, 2026
