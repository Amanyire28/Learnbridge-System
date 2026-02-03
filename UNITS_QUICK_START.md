# Units Management - Quick Start Guide

## Access the Feature
1. Login as Admin
2. Click **Units** in the sidebar (new menu item)
3. See all your courses and their units

## Quick Actions

### ➕ Add a Unit (2 minutes)
```
1. Fill in "Add New Unit" form
2. Select Course: Choose from dropdown
3. Unit Number: Enter 1, 2, 3, etc.
4. Unit Title: e.g., "Unit 1: Introduction to Python"
5. Module Link: Leave blank for auto-generate
6. Click "Add Unit"
7. ✅ Unit appears in course instantly
```

### ✏️ Edit a Unit (1 minute)
```
1. Find unit in the course accordion
2. Click "✏️ Edit" button
3. Change title, number, or link
4. Click "Save Changes"
5. ✅ Updates instantly
```

### 🗑️ Delete a Unit (30 seconds)
```
1. Find unit in the course accordion
2. Click "🗑️ Delete" button
3. Confirm in popup
4. ✅ Unit deleted (⚠️ also removes all content in unit)
```

## What You'll See

### Dashboard View
```
Course Units Management
├── Add New Unit Form (at top)
│   ├── Select Course
│   ├── Unit Number
│   ├── Unit Title
│   └── Module Link
│
└── All Courses Accordion
    ├── Course 1 (5 Units)
    │   ├── Unit 1 | Title | Link | 3 items | Edit Delete
    │   ├── Unit 2 | Title | Link | 5 items | Edit Delete
    │   └── Unit 3 | Title | Link | 2 items | Edit Delete
    │
    └── Course 2 (3 Units)
        ├── Unit 1 | Title | Link | 4 items | Edit Delete
        ├── Unit 2 | Title | Link | 0 items | Edit Delete
        └── Unit 3 | Title | Link | 1 item  | Edit Delete
```

## Common Scenarios

### Scenario 1: Add English Units for Primary 6
```
Course: English - Primary 6

Unit 1: Phonics and Word Recognition
Unit 2: Reading Comprehension  
Unit 3: Basic Grammar
Unit 4: Creative Writing
Unit 5: Oral Communication

Steps:
✓ Add Unit 1 with title "Phonics and Word Recognition"
✓ Add Unit 2 with title "Reading Comprehension"
✓ Add Unit 3 with title "Basic Grammar"
✓ Add Unit 4 with title "Creative Writing"
✓ Add Unit 5 with title "Oral Communication"

Result: 5 units ready for lesson content
```

### Scenario 2: Fix a Unit Title
```
Have: "Unit 1: Intro to Phyton" (typo)
Need: "Unit 1: Introduction to Python"

Steps:
✓ Click Edit on the unit
✓ Change title to "Unit 1: Introduction to Python"
✓ Click Save

Result: ✅ Fixed instantly
```

### Scenario 3: Reorganize Units
```
Have units numbered: 1, 2, 3, 5, 6 (missing 4)
Need: 1, 2, 3, 4, 5

Steps:
✓ Edit unit #5 and change to #4
✓ Edit unit #6 and change to #5

Result: ✅ Properly numbered
```

## Tips & Tricks

| Tip | Benefit |
|-----|---------|
| Use sequential numbers (1, 2, 3...) | Easy navigation |
| Clear, descriptive titles | Users understand content |
| Let system auto-generate links | Consistency |
| Check content count before deleting | Avoid losing data |
| View course first before adding unit | Quick navigation |

## What Gets Deleted?

When you delete a unit, the following is removed:
- ✅ The unit itself
- ✅ All lesson notes in that unit
- ✅ All past papers in that unit
- ✅ All practice quizzes in that unit
- ✅ All student progress in that unit

**⚠️ Action is permanent - cannot be undone**

## Error Messages You Might See

| Message | Meaning | Fix |
|---------|---------|-----|
| "Please fill all required fields" | Missing Course, Number, or Title | Complete all fields |
| "Error adding unit" | Database error | Try again, check connection |
| "Unit updated successfully" | ✅ Edit worked | None needed |
| "Unit deleted successfully" | ✅ Delete worked | None needed |

## Keyboard Shortcuts

None currently, but planned for future:
- Ctrl+N: New unit
- Ctrl+E: Edit unit
- Ctrl+D: Delete unit

## Mobile View

The interface is fully responsive:
- ✅ Accordion still works
- ✅ Buttons still clickable
- ✅ Forms still usable
- ✅ All features available

## Status Indicators

### Content Count Badge
```
"0 items" = New empty unit (no content yet)
"1 item"  = Has 1 lesson/paper/quiz
"5 items" = Has 5 content pieces
```

Color-coded:
- Green badge = Has content
- Gray badge = Empty unit

### Course Count
```
"5 Units" = Course has 5 units
"0 Units" = Empty course (no units yet)
```

## Performance

- ⚡ Add unit: < 1 second
- ⚡ Edit unit: < 1 second  
- ⚡ Delete unit: < 1 second
- ⚡ Load all: < 2 seconds

## FAQ

**Q: What if I accidentally delete a unit?**
A: Currently it's permanent. Restore from backup if available.

**Q: Can I reorder units?**
A: Edit the unit numbers to reorder them.

**Q: Do I need to add units before adding content?**
A: Yes, units are containers for content.

**Q: What's the maximum units per course?**
A: No limit - add as many as needed.

**Q: Can I have gaps in unit numbers?**
A: Yes, but not recommended (1, 2, 4, 5 works but looks odd).

## Support Resources

1. **Full Guide**: See `UNITS_MANAGEMENT_GUIDE.md`
2. **Implementation Details**: See `UNITS_MANAGEMENT_IMPLEMENTATION.md`
3. **Code**: Check `includes/admin/loadunits.php`

---

**You're all set!** Start using Units Management to organize your courses.

**Created**: January 25, 2026  
**Version**: 1.0  
**Status**: Ready to Use ✅
