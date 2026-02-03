# Skills Quest - Uganda School Curriculum Edition

## Overview

This document outlines the transformation of the Skills Quest platform from a coding education platform to a comprehensive **Uganda School Curriculum** learning management system.

## What Has Changed

### 1. **Courses → Subjects**
The platform now features **40 subjects** aligned with Uganda's national curriculum framework:

#### **Primary Education**
- **Primary 6 (Grade 6):** 5 subjects
  - English Language
  - Mathematics
  - Integrated Science
  - Social Studies
  - Computer Studies / ICT

- **Primary 7 (Grade 7):** 5 subjects
  - Same subjects with advanced content

#### **Secondary Education**
- **Secondary 1 (S.1 / Form 1):** 8 subjects
  - English Language
  - Mathematics
  - Biology
  - Chemistry
  - Physics
  - History
  - Geography
  - Computer Studies

- **Secondary 2 (S.2 / Form 2):** 7 subjects

- **Secondary 3 (S.3 / Form 3):** 6 subjects

- **Secondary 4 (S.4 / Form 4 - UCE/O-Levels):** 8 subjects

### 2. **Modules → Syllabus Units**
Course modules are now called **Syllabus Units** to align with Uganda's national curriculum structure.

**Example:**
- **Mathematics - Primary 6, Unit 1:** Numbers to 1,000,000
- **Biology - S.1, Unit 2:** Cell Structure and Function
- **Chemistry - S.4, Unit 4:** Organic Chemistry

### 3. **Content Types**
The `notes` table now supports three content types (column: `content_type`):

| Type | Purpose | Example |
|------|---------|---------|
| **lesson** | Regular lesson notes | Explanations, definitions, examples |
| **past_paper** | Previous examination questions | UCE past papers, mock exams |
| **practice_quiz** | Self-assessment exercises | Multiple choice, short answer questions |

### 4. **Database Structure (Reused)**
All existing tables have been preserved for scalability:

```sql
-- REUSED TABLES (NO CHANGES)
- users           → Student and teacher accounts
- enrollments     → Student course enrollment
- completed_courses → Course completion tracking
- current_course_page → Progress tracking

-- MODIFIED TABLES
- courses         → Now stores 40 Uganda subjects (same schema)
- course_outline  → Stores syllabus units (modules renamed to units)
- notes           → ADDED: content_type column (lesson/past_paper/practice_quiz)
```

## How to Migrate

### Step 1: Run the Migration Script
Navigate to the migration page in your browser:
```
http://localhost/Skills%20Quest/migrate_to_uganda_curriculum.php
```

**What this does:**
- ✓ Clears old programming course data
- ✓ Inserts 40 Uganda school subjects
- ✓ Creates syllabus units (5-6 per subject)
- ✓ Adds sample lesson notes
- ✓ Adds support for past papers and practice quizzes
- ✓ Modifies the `notes` table to add `content_type` column

### Step 2: Verify the Changes
After migration, visit:
```
http://localhost/Skills%20Quest/courses.php
```

You should see:
- Courses grouped by education level (Primary 6 → Secondary 4)
- Clear subject names aligned to Uganda curriculum
- Updated descriptions matching learning standards

## Content Structure

### Sample Lesson Content
Each subject includes structured lessons:

```
Subject: Mathematics - Primary 6
├── Unit 1: Numbers to 1,000,000
│   ├── Reading and Writing Large Numbers
│   ├── Comparing and Ordering Numbers
│   └── Rounding to the Nearest 10, 100, 1000
├── Unit 2: Operations and Problem Solving
│   ├── Addition and Subtraction
│   ├── Multiplication and Division
│   └── Solving Word Problems
└── Unit 3: Fractions and Decimals
    ├── Understanding Fractions
    ├── Decimal Notation
    └── Converting Between Fractions and Decimals
```

### Adding Past Papers

To add past exam papers, insert into the `notes` table with `content_type = 'past_paper'`:

```php
// Example: Adding a Mathematics UCE past paper
INSERT INTO notes (course_id, outline_id, section_title, section_content, content_type) 
VALUES (
    35,  // Mathematics - S.4
    142, // Outline ID for relevant unit
    'Mathematics UCE 2023 - Paper 1',
    '[Full exam questions and answer key here]',
    'past_paper'
);
```

### Adding Practice Quizzes

To add interactive quizzes:

```php
// Example: Adding a Chemistry practice quiz
INSERT INTO notes (course_id, outline_id, section_title, section_content, content_type) 
VALUES (
    15,  // Chemistry - S.1
    67,  // Outline ID
    'Atomic Structure Quiz',
    'Question 1: [Quiz content with answers]',
    'practice_quiz'
);
```

## File Changes

### Modified Files
| File | Changes |
|------|---------|
| `courses.php` | Updated to display subjects grouped by education level |
| `course.php` | Now displays "Syllabus Units" instead of "Modules" |
| `skillsQuestDB.sql` | Database schema with updated data |

### New Files
| File | Purpose |
|------|---------|
| `migrate_to_uganda_curriculum.php` | Migration script (run once) |
| `assets/js/course_uganda.js` | Updated JavaScript for new content types |
| `UGANDA_CURRICULUM_README.md` | This file |

### Backend Files (Unchanged Schema)
The following PHP files work with the new data without modification:
```
includes/course/loadcoursetitle.php
includes/course/loadcourseoutline.php
includes/course/loadnotes.php
includes/course/updatecurrentpage.php
includes/course/coursecompletion.php
includes/course/loadcoursenavbuttons.php
includes/courses/enroll.php
includes/connect.php
```

## Database Schema Reference

### `courses` Table (Updated Data)

```sql
CREATE TABLE courses (
  course_id INT PRIMARY KEY AUTO_INCREMENT,
  course_title VARCHAR(255),           -- e.g., "Mathematics - Primary 6"
  course_description VARCHAR(255),     -- Learning objectives
  course_image_url VARCHAR(255),       -- Subject icon/image
  language VARCHAR(255),               -- NOW: Education level (Primary 6, Secondary 1, etc.)
  rating INT,
  created_at DATETIME,
  updated_at DATETIME
);
```

### `course_outline` Table (Syllabus Units)

```sql
CREATE TABLE course_outline (
  outline_id INT PRIMARY KEY AUTO_INCREMENT,
  course_id INT,
  module_number INT,                   -- Unit number (1-6)
  module_title VARCHAR(255),           -- Unit title (e.g., "Unit 1: Numbers to 1,000,000")
  module_link VARCHAR(255),            -- URL reference
  FOREIGN KEY (course_id) REFERENCES courses(course_id)
);
```

### `notes` Table (Enhanced)

```sql
CREATE TABLE notes (
  note_id INT PRIMARY KEY AUTO_INCREMENT,
  course_id INT,
  outline_id INT,
  section_title VARCHAR(255),
  section_content TEXT,
  content_type ENUM('lesson', 'past_paper', 'practice_quiz') DEFAULT 'lesson',  -- NEW COLUMN
  FOREIGN KEY (course_id) REFERENCES courses(course_id),
  FOREIGN KEY (outline_id) REFERENCES course_outline(outline_id)
);
```

## Features Overview

### ✅ Implemented
- [x] 40 Uganda school subjects (Primary 6 - Secondary 4)
- [x] Syllabus unit organization (5-6 units per subject)
- [x] Lesson note content management
- [x] Past paper storage capability
- [x] Practice quiz support
- [x] Student enrollment system
- [x] Progress tracking
- [x] Course completion marking
- [x] Responsive course browsing (grouped by level)

### 🔄 In Development
- [ ] Past paper detailed search and filtering
- [ ] Interactive quiz system with auto-grading
- [ ] Performance tracking by subject/unit
- [ ] Teacher dashboard for content management
- [ ] PDF download capability for past papers
- [ ] Mobile app integration
- [ ] Advanced search and curriculum mapping

### 📋 Future Enhancements
- [ ] Video lessons integration
- [ ] Discussion forums per subject
- [ ] Peer assessment tools
- [ ] Adaptive learning paths
- [ ] Certification system
- [ ] Parent/Guardian portal

## How Content Flows

```
Student visits courses.php
         ↓
Views subjects grouped by education level
         ↓
Clicks "Enroll Now" or "Continue Learning"
         ↓
Redirected to course.php with course_id
         ↓
JavaScript loads course title and syllabus units
         ↓
Student selects a unit (e.g., "Unit 1: Numbers")
         ↓
Content loaded showing:
  - Lesson notes
  - Past exam papers (if available)
  - Practice quizzes (if available)
         ↓
Student marks progress → Progress saved
         ↓
Option to mark entire course complete
```

## Adding New Content

### To Add a New Lesson Note:
```php
// After running the migration script, add new content:
INSERT INTO notes (course_id, outline_id, section_title, section_content, content_type)
VALUES (
    2,  // Mathematics - Primary 6
    6,  // Unit 2 of this course
    'Division Basics',
    'Division is the process of splitting something into equal parts...',
    'lesson'
);
```

### To Add a Past Exam Paper:
```php
INSERT INTO notes (course_id, outline_id, section_title, section_content, content_type)
VALUES (
    36, // Mathematics - S.4 (UCE level)
    145,
    'Uganda Certificate of Education - Mathematics 2023',
    '[Complete exam paper with questions]',
    'past_paper'
);
```

### To Add a Practice Quiz:
```php
INSERT INTO notes (course_id, outline_id, section_title, section_content, content_type)
VALUES (
    13, // Biology - S.1
    52,
    'Cell Structure - Quick Quiz',
    'Q1: What is the powerhouse of the cell? A) Nucleus B) Mitochondria C) Ribosome...',
    'practice_quiz'
);
```

## Frequently Asked Questions (FAQ)

### Q: Can I add more subjects?
**A:** Yes! The system supports unlimited subjects. Simply add new entries to the `courses` table with the appropriate education level in the `language` field.

### Q: How do I import existing past papers?
**A:** Use the `INSERT` statement above with `content_type = 'past_paper'` to add past exam papers to any unit.

### Q: Are student accounts affected?
**A:** No! All user data, enrollments, and progress are preserved. Only course content changed.

### Q: Can I revert to the old programming courses?
**A:** Yes, you can restore from a backup of the original `skillsQuestDB.sql` file before migration.

### Q: How do students see past papers and quizzes?
**A:** When viewing a unit, students see all content (lesson notes, past papers, quizzes) grouped by type. Labels indicate the content type (📚 Lesson, 📋 Past Paper, ✏️ Quiz).

## Support for Teachers/Content Managers

### Managing Course Content
1. Login as admin
2. Go to Admin Dashboard
3. Navigate to "Courses" section
4. Select a subject to edit
5. Add new units or modify existing ones
6. Add lesson notes, past papers, and quizzes as needed

### Content Guidelines
- **Lesson Notes:** Clear, concise explanations with examples
- **Past Papers:** Official exam questions with answer keys
- **Practice Quizzes:** 5-10 questions per quiz with solutions

## Technical Support

For issues or questions about the migration:

1. **Verify migration:** Check `http://localhost/Skills%20Quest/courses.php`
2. **Check database:** Verify all 40 subjects appear and are grouped by level
3. **Test a course:** Enroll in a subject and verify content loads
4. **Check browser console:** Look for JavaScript errors (F12 → Console tab)
5. **Review error logs:** Check PHP error logs in your server

## References

- [Uganda National Curriculum Framework](https://www.ncdc.go.ug/)
- [UNEB - Uganda National Examinations Board](https://www.uneb.ac.ug/)
- [Uganda Certificate of Education (UCE) Syllabi](https://www.uneb.ac.ug/publications/)

---

**Migration Date:** January 10, 2026  
**System:** Skills Quest - Uganda Edition v1.0  
**Status:** ✅ Ready for Student Use
