# Units Management System - Admin Dashboard

## Overview
The admin dashboard now includes a **Units Management** section that allows you to allocate details to different units for your courses. This feature provides a complete CRUD (Create, Read, Update, Delete) interface for managing course units and their details.

## Features

### 1. **View All Courses and Units**
- See all courses in an accordion-style interface
- Each course shows the number of units it contains
- Expand each course to view all its units
- See unit details including:
  - Unit number
  - Unit title
  - Module link (slug)
  - Content count (number of lesson items in that unit)

### 2. **Add New Units**
- Quick form to add new units to any course
- Fields:
  - **Select Course**: Choose which course the unit belongs to
  - **Unit Number**: Numeric identifier (e.g., 1, 2, 3)
  - **Unit Title**: Display name (e.g., "Unit 1: Introduction to Python")
  - **Module Link** (Optional): Auto-generated from title if left empty

### 3. **Edit Units**
- Click the "✏️ Edit" button on any unit
- Modal dialog opens with current unit information
- Modify:
  - Unit number
  - Unit title
  - Module link
- Changes saved instantly to database

### 4. **Delete Units**
- Click the "🗑️ Delete" button on any unit
- Confirmation dialog prevents accidental deletion
- **Important**: Deleting a unit also deletes all content (lesson notes, past papers, quizzes) in that unit
- Deletion is permanent

## How to Use

### Step 1: Access the Units Management
1. Login as an admin
2. Go to the Admin Dashboard
3. Click on **Units** in the sidebar menu

### Step 2: Add a New Unit
1. Fill in the "Add New Unit" form at the top
2. Select the course
3. Enter the unit number (must be numeric)
4. Enter a descriptive unit title
5. (Optional) Enter a custom module link
6. Click "Add Unit" button
7. Unit appears in the course accordion immediately

### Step 3: View Units by Course
1. Find the course you want to manage
2. Click the course name to expand it
3. See all units for that course in a table
4. Each row shows:
   - Unit number (highlighted in blue badge)
   - Unit title (full name)
   - Module link (system slug)
   - Content count (how many items are in this unit)
   - Action buttons (Edit/Delete)

### Step 4: Edit a Unit
1. Find the unit you want to edit
2. Click the "✏️ Edit" button in the Actions column
3. Modal dialog opens with current details
4. Modify any field
5. Click "Save Changes"
6. Changes applied immediately

### Step 5: Delete a Unit
1. Find the unit you want to delete
2. Click the "🗑️ Delete" button in the Actions column
3. Confirm the deletion in the popup
4. Unit and all its content are removed
5. Table refreshes automatically

## Database Structure

### course_outline table
Stores unit/module information:
```
- outline_id: Unique identifier
- course_id: Which course this unit belongs to
- module_number: Unit number (1-6, etc.)
- module_title: Display name
- module_link: URL slug
```

### notes table
Stores content items within units:
```
- note_id: Unique identifier
- course_id: Which course
- outline_id: Which unit (links to course_outline)
- section_title: Content title
- section_content: Content details
- content_type: 'lesson', 'past_paper', or 'practice_quiz'
```

## Example Workflow

**Scenario**: Adding English units for a Primary 6 course

1. Course already exists: "English - Primary 6"

2. Add first unit:
   - Course: English - Primary 6
   - Unit Number: 1
   - Unit Title: Unit 1: Phonics and Word Recognition
   - Module Link: (auto-generated)
   - Click Add Unit

3. Add more units (repeat for each):
   - Unit 2: Reading Comprehension
   - Unit 3: Basic Grammar
   - Unit 4: Creative Writing
   - Unit 5: Oral Communication

4. View all units:
   - Expand "English - Primary 6" course
   - See all 5 units listed
   - Each shows how many content items it has

5. Edit a unit (example):
   - Click Edit on "Unit 3: Basic Grammar"
   - Change title to "Unit 3: Advanced Grammar"
   - Save changes
   - Title updates immediately

6. Add content to units:
   - Units are now ready to receive lesson notes
   - Use the course management system to add:
     - Lesson notes
     - Past exam papers
     - Practice quizzes

## Tips & Best Practices

1. **Unit Numbering**: Keep units numbered sequentially (1, 2, 3, etc.) for easy navigation
2. **Unit Titles**: Use descriptive titles that clearly indicate the topic
3. **Module Links**: Let the system auto-generate these for consistency
4. **Content Planning**: Plan your units before adding them
5. **Backup Data**: Always backup your database before bulk deletions
6. **Organization**: Keep related units together in the same course

## Troubleshooting

| Issue | Solution |
|-------|----------|
| Units not appearing | Refresh the page or check course selection |
| Cannot add unit | Fill all required fields (Course, Number, Title) |
| Edit modal won't open | Clear browser cache and try again |
| Delete not working | Check database permissions and try from a fresh page load |

## Features Coming Soon

- **Bulk unit import** from CSV
- **Unit templates** for quick setup
- **Unit ordering** (drag and drop)
- **Unit descriptions** and learning objectives
- **Unit assessment** integration
- **Unit analytics** (completion rates)

## Technical Details

### Files Modified
- `admin.php` - Added Units navigation and JavaScript handlers
- `includes/admin/loadunits.php` - NEW: Complete units management interface
- `assets/css/custom.css` - Added styling for units interface

### JavaScript Functions
- `loadAdminSection(section)` - Loads the appropriate admin section
- `setActiveNav(section)` - Highlights current navigation item
- Add/Edit/Delete unit handlers - AJAX-based operations

### AJAX Endpoints
All operations go through `includes/admin/loadunits.php`:
- Action: `add_unit` - Creates new unit
- Action: `edit_unit` - Updates existing unit
- Action: `delete_unit` - Removes unit and related content

## Security

- All database inputs are properly escaped
- ID parameters are validated as integers
- Form submissions are POST-based
- Confirmation dialogs prevent accidental operations

## Support

For issues or questions about the Units Management system:
1. Check this documentation
2. Review the inline code comments in loadunits.php
3. Check browser console for JavaScript errors
4. Verify database connection is working

---

**Version**: 1.0  
**Last Updated**: January 25, 2026  
**Status**: ✅ Complete and Ready for Use
