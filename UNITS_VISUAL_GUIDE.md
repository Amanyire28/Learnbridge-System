# Units Management System - Visual Overview

## Architecture Diagram

```
┌─────────────────────────────────────────────────────────────────┐
│                      ADMIN DASHBOARD                            │
│                      (admin.php)                                │
└─────────────────────────────────────────────────────────────────┘
                              │
                ┌─────────────┼─────────────┐
                │             │             │
            Dashboard     Users        Courses
                │             │             │
                ▼             ▼             ▼
        [loadadmin        [loadusers] [loadcourses]
        dashboard.php]                      │
                                            │
                                            ▼
                                    ┌─────────────────┐
                              NEW→  │   Units Tab     │ ← NEW
                                    └─────────────────┘
                                            │
                                            ▼
                              ┌──────────────────────────┐
                              │  loadunits.php           │
                              │  (NEW - 400+ lines)      │
                              │                          │
                              │  ✓ Add Unit Form        │
                              │  ✓ Courses Accordion    │
                              │  ✓ Units Table          │
                              │  ✓ Edit Modal           │
                              │  ✓ Delete Handler       │
                              │  ✓ AJAX Handlers        │
                              └──────────────────────────┘
                                            │
                    ┌───────────────────────┼───────────────────────┐
                    │                       │                       │
                    ▼                       ▼                       ▼
        ┌──────────────────┐    ┌──────────────────┐    ┌──────────────────┐
        │   Add Unit        │    │   View Units     │    │   Edit/Delete    │
        │   (Form)          │    │   (Accordion)    │    │   (Modal/JS)     │
        │                   │    │                  │    │                  │
        │ ✓ Course Select   │    │ ✓ Course List    │    │ ✓ Modal Edit    │
        │ ✓ Unit Number     │    │ ✓ Unit Table     │    │ ✓ Confirmation  │
        │ ✓ Unit Title      │    │ ✓ Content Count  │    │ ✓ AJAX Request  │
        │ ✓ Module Link     │    │ ✓ Action Buttons │    │ ✓ Auto Refresh  │
        └──────────────────┘    └──────────────────┘    └──────────────────┘
                    │                       │                       │
                    └───────────────────────┼───────────────────────┘
                                            │
                                            ▼
                              ┌──────────────────────────┐
                              │   Database               │
                              │   (MySQL)                │
                              │                          │
                              │  ✓ courses table         │
                              │  ✓ course_outline table  │
                              │  ✓ notes table           │
                              └──────────────────────────┘
```

## User Workflow

```
                    ┌─────────────────────────┐
                    │   Admin Login           │
                    └────────────┬────────────┘
                                 │
                    ┌────────────▼────────────┐
                    │  Admin Dashboard        │
                    │  (admin.php)            │
                    └────────────┬────────────┘
                                 │
                    ┌────────────▼────────────┐
                    │  Click "Units" Tab      │
                    │  in Sidebar             │
                    └────────────┬────────────┘
                                 │
                    ┌────────────▼────────────┐
                    │  Units Management Page  │
                    │  (loadunits.php)        │
                    └────────────┬────────────┘
                                 │
        ┌────────────────────────┼────────────────────────┐
        │                        │                        │
        ▼                        ▼                        ▼
    ADD UNIT              VIEW UNITS             EDIT/DELETE
    ├─ Fill form          ├─ Expand course      ├─ Click Edit button
    ├─ Select course      ├─ See unit count     ├─ Modal opens
    ├─ Enter number       ├─ View unit details  ├─ Modify data
    ├─ Enter title        ├─ Check content      ├─ Click Save
    ├─ Submit form        │   items count       └─ OR Click Delete
    └─ ✅ Unit added       └─ See action buttons   ✅ Unit updated/deleted
```

## Data Flow - Adding a Unit

```
User Interface Layer
┌────────────────────────────────────────────────┐
│  Form: Course | Number | Title | Link         │
│  Button: [Add Unit]                            │
└────────────────────┬───────────────────────────┘
                     │
                     │ Form Submit
                     ▼
JavaScript/jQuery Layer
┌────────────────────────────────────────────────┐
│  • Validate form fields                        │
│  • Prepare FormData                            │
│  • Make AJAX POST request                      │
│  • Action: add_unit                            │
│  • Show loading indicator                      │
└────────────────────┬───────────────────────────┘
                     │
                     │ HTTP POST
                     ▼
PHP Backend Layer (loadunits.php)
┌────────────────────────────────────────────────┐
│  • Receive POST data                           │
│  • Validate all required fields                │
│  • Check course_id is valid                    │
│  • Generate module_link if empty               │
│  • Escape input for SQL safety                 │
│  • Execute INSERT query                        │
│  • Return JSON response                        │
└────────────────────┬───────────────────────────┘
                     │
                     │ JSON Response
                     ▼
Database Layer (MySQL)
┌────────────────────────────────────────────────┐
│  INSERT INTO course_outline                    │
│  (course_id, module_number, module_title,      │
│   module_link)                                 │
│  VALUES (...)                                  │
│                                                │
│  New outline_id generated                      │
│  Row inserted successfully                     │
└────────────────────┬───────────────────────────┘
                     │
                     │ Return status
                     ▼
JavaScript/jQuery Layer
┌────────────────────────────────────────────────┐
│  • Receive JSON: {success: true}               │
│  • Show success message                        │
│  • Hide loading indicator                      │
│  • Reload page                                 │
└────────────────────┬───────────────────────────┘
                     │
                     │ Page Reload
                     ▼
User Interface Layer
┌────────────────────────────────────────────────┐
│  ✅ New unit appears in course accordion      │
│  ✅ Unit count updated                        │
│  ✅ Form cleared                              │
└────────────────────────────────────────────────┘
```

## Database Schema (Used)

```
courses
├─ course_id (PK)
├─ course_title
├─ course_description
├─ course_image_url
├─ language
├─ rating
├─ created_at
└─ updated_at
    │
    └─► FK to course_outline

course_outline (Units Storage)
├─ outline_id (PK)
├─ course_id (FK) ──────────► connects to courses.course_id
├─ module_number
├─ module_title
└─ module_link
    │
    └─► FK to notes (cascade delete)

notes (Unit Content)
├─ note_id (PK)
├─ course_id
├─ outline_id (FK) ─────► connects to course_outline.outline_id
├─ section_title
├─ section_content
└─ content_type
```

## Component Structure

```
loadunits.php
│
├── POST Handler (Lines 1-60)
│   ├── add_unit action
│   │   └── INSERT INTO course_outline
│   ├── edit_unit action
│   │   └── UPDATE course_outline
│   └── delete_unit action
│       ├── DELETE FROM notes (cascade)
│       └── DELETE FROM course_outline
│
├── HTML Structure (Lines 61-250)
│   ├── Heading
│   ├── Add Unit Card
│   │   └── Form with inputs
│   ├── Courses Accordion Card
│   │   └── Course sections with unit tables
│   └── Edit Modal Dialog
│       └── Form for editing
│
└── JavaScript (Lines 251-400+)
    ├── Add Unit Form Handler
    ├── Delete Unit Handler
    ├── Edit Unit Handler
    ├── AJAX Functions
    │   ├── add_unit POST
    │   ├── edit_unit POST
    │   └── delete_unit POST
    └── UI Functions
        ├── Modal management
        ├── Alert/confirmation
        └── Page reload
```

## UI Layout

```
┌─────────────────────────────────────────────────────────────┐
│         Course Units Management                             │
│         ➕ Add New Unit                                      │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  [Form Section]                                             │
│  ┌──────────────────────────────────────────────────────┐  │
│  │ Select Course  │ Unit Number │ Unit Title      │ Link│  │
│  │ [Dropdown▼]    │ [Input]     │ [Input      ]   │ [In│  │
│  │ [Add Unit Button]                                   │  │
│  └──────────────────────────────────────────────────────┘  │
│                                                              │
│  [Accordion Section - Courses]                              │
│  ┌────────────────────────────────────────────────────────┐ │
│  │ ▼ English - Primary 6                      [5 Units]  │ │
│  ├────────────────────────────────────────────────────────┤ │
│  │ # │ Unit │ Title                  │ Link  │ Count │ A │ │
│  │ 1 │ [1]  │ Phonics and Recognition│ link  │ 3 ▼  │ D │ │
│  │ 2 │ [2]  │ Reading Comprehension  │ link  │ 5 ▼  │ D │ │
│  │ 3 │ [3]  │ Basic Grammar          │ link  │ 2 ▼  │ D │ │
│  │ 4 │ [4]  │ Creative Writing       │ link  │ 4 ▼  │ D │ │
│  │ 5 │ [5]  │ Oral Communication     │ link  │ 0 ▼  │ D │ │
│  └────────────────────────────────────────────────────────┘ │
│  ┌────────────────────────────────────────────────────────┐ │
│  │ ▶ Mathematics - Primary 6                 [3 Units]   │ │
│  └────────────────────────────────────────────────────────┘ │
│                                                              │
└─────────────────────────────────────────────────────────────┘

Legend:
▼ = Expandable section
D = Delete button
✏️ = Edit button
[n] = Unit number
```

## State Transitions

```
INITIAL STATE
└─ Page loads
   └─ All courses collapsed
   └─ Add form empty
   └─ Ready for input

USER ADDS UNIT
└─ Fill form
   └─ Submit (AJAX)
      └─ Processing... (loading)
         └─ ✅ Success
            └─ Show message
            └─ Reload page
               └─ Unit appears in accordion
               └─ Form clears
               └─ Back to ready state

USER EDITS UNIT
└─ Click Edit button
   └─ Modal opens
      └─ Form pre-filled with data
      └─ User modifies
      └─ Submit (AJAX)
         └─ Processing... (loading)
            └─ ✅ Success
            └─ Modal closes
            └─ Reload page
               └─ Changes visible
               └─ Back to ready state

USER DELETES UNIT
└─ Click Delete button
   └─ Confirmation dialog
      └─ User confirms
      └─ Submit (AJAX)
         └─ Processing... (loading)
            └─ ✅ Success
            └─ Show message
            └─ Reload page
               └─ Unit removed from accordion
               └─ Back to ready state

ERROR STATE
└─ Submission fails
   └─ AJAX error
      └─ Error message shown
      └─ Back to ready state
      └─ User can retry
```

---

This diagram provides a complete visual overview of how the Units Management system works!
