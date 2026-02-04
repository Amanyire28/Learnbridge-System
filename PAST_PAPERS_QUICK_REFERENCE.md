# Past Papers - Quick Reference Card

## For Admins

### Quick Start (3 steps)
1. **Run Migration:** Import `PAST_PAPERS_MIGRATION.sql` to database
2. **Navigate:** Admin → Past Papers
3. **Upload:** Fill form and click "Upload Past Paper"

### Upload Form Fields
| Field | Type | Required | Limit |
|-------|------|----------|-------|
| Course | Dropdown | ✅ | - |
| Title | Text | ✅ | 255 char |
| Year | Number | ✅ | 2000-2026 |
| Term | Dropdown | ✅ | 5 options |
| Subject | Text | ❌ | 100 char |
| Paper File | File | ✅ | 10MB PDF/Word |
| Solution File | File | ❌ | 10MB PDF/Word |
| Description | Textarea | ❌ | 65k char |

### Navigation Links
```
Admin Dashboard
├── Past Papers (Upload & Manage)
└── Statistics (View Analytics)
```

### Common Tasks
```
Upload paper:      Form → File selection → Submit → Check
Manage papers:     View list → Download/Delete buttons
View statistics:   Statistics tab → See trends & popularity
Delete paper:      Click trash icon → Confirm → Done
```

---

## For Students

### Quick Start (1 step)
1. **Navigate:** Main menu → Past Papers

### Browse & Filter
- **Course Filter:** Select your enrolled course
- **Year Filter:** 2020, 2021, 2022, 2023, 2024
- **Term Filter:** Term 1, 2, 3, Mid-term, Final
- **Search:** By title or subject

### Download Options
For each paper:
- 📄 **Download Paper** - Get exam questions
- ✅ **Download Solution** - Get answers (if available)
- ℹ️ **Info Button** - View details

### Status Indicators
- 🔵 **Blue badge** - Paper type
- 🟢 **Green badge** - Has solution
- 📊 **Info icon** - Download count

---

## Database Tables

### past_papers
```sql
- id (PK)
- course_id (FK)
- title
- year
- term
- subject
- paper_file_path
- solution_file_path
- file_size
- upload_date
- uploaded_by (FK → users)
- description
- is_active
```

### past_paper_attempts
```sql
- id (PK)
- paper_id (FK)
- student_id (FK)
- attempt_type (download/preview)
- attempted_at (timestamp)
- ip_address
- user_agent
```

### past_paper_submissions
```sql
- id (PK)
- paper_id (FK)
- student_id (FK)
- submission_file_path
- score / total_score
- submitted_at (timestamp)
- status (pending/graded)
- feedback (text)
```

---

## File Structure

```
assets/past-papers/
├── course-1/
│   ├── 2024/
│   │   ├── 1707456000_term1_exam.pdf
│   │   ├── 1707456000_solution_term1_exam.pdf
│   ├── 2023/
│   │   ├── 1707456000_term1_exam.pdf
└── course-2/
    ├── 2024/
```

---

## Key API Endpoints

| Endpoint | Method | Purpose | Auth |
|----------|--------|---------|------|
| `/admin.php?section=pastpapers` | GET | Admin upload interface | Admin |
| `/admin.php?section=statistics` | GET | Analytics dashboard | Admin |
| `/includes/admin/uploadpastpaper.php` | POST | Upload new paper | Admin |
| `/includes/course/downloadpaper.php?id={id}&type={type}` | GET | Download & track | User |
| `/past-papers.php` | GET | Student browser | User |

---

## Common SQL Queries

### List all papers for a course
```sql
SELECT * FROM past_papers 
WHERE course_id = 1 AND is_active = 1
ORDER BY year DESC, term DESC;
```

### Most downloaded papers
```sql
SELECT pp.id, pp.title, COUNT(ppa.id) as downloads
FROM past_papers pp
LEFT JOIN past_paper_attempts ppa ON pp.id = ppa.paper_id
GROUP BY pp.id
ORDER BY downloads DESC
LIMIT 10;
```

### Get student download history
```sql
SELECT pp.title, pp.year, pp.term, ppa.attempted_at
FROM past_paper_attempts ppa
JOIN past_papers pp ON ppa.paper_id = pp.id
WHERE ppa.student_id = ?
ORDER BY ppa.attempted_at DESC;
```

---

## Troubleshooting Checklist

### Papers not showing up for students?
- [ ] Check `past_papers.is_active = 1`
- [ ] Verify student enrolled in course
- [ ] Check `completed_courses` table has enrollment
- [ ] Verify `course_id` matches

### Download failing?
- [ ] Check file exists in assets/past-papers/
- [ ] Verify file path in database matches actual file
- [ ] Check directory permissions (755)
- [ ] Check file isn't corrupted

### Upload not working?
- [ ] File size < 10MB?
- [ ] File type is PDF/Word?
- [ ] Directory writable? `chmod 755 assets/past-papers/`
- [ ] Disk space available?
- [ ] PHP error log shows issue?

### Statistics not updating?
- [ ] Check `past_paper_attempts` table has data
- [ ] Run queries manually to verify data exists
- [ ] Check date filters aren't excluding data

---

## File Operations

### Uploading
```
User selects file → Validate (type, size) → Generate unique name → 
Create directory if needed → Move to folder → Save metadata → Log attempt
```

### Downloading
```
Request file → Check access → Verify enrollment → Log attempt → 
Serve file with correct MIME type → Update statistics
```

### Deleting
```
Get file paths → Delete paper files from disk → Delete DB record → 
Delete download history → Confirm deletion
```

---

## Access Control Rules

| Role | Can Access | Can Upload | Can Delete |
|------|------------|-----------|-----------|
| Admin | All papers | ✅ | ✅ |
| Student | Own courses only | ❌ | ❌ |
| Guest | None | ❌ | ❌ |

---

## Performance Notes

- **Indexes:** course_id, year, term for fast queries
- **Caching:** Frequently download papers are cached by browser
- **Compression:** Consider compressing PDFs > 5MB
- **Storage:** Monitor assets/past-papers/ directory size

---

## Configuration

### File Upload Limits (in php.ini)
```
upload_max_filesize = 10M
post_max_size = 10M
max_execution_time = 300
```

### Directory Permissions
```bash
chmod 755 assets/past-papers/
chmod 644 assets/past-papers/*/*/*.pdf
```

---

## Monitoring

### Check Upload Activity
```php
SELECT COUNT(*) FROM past_paper_attempts 
WHERE attempted_at > DATE_SUB(NOW(), INTERVAL 7 DAY);
```

### Monitor Storage
```bash
du -sh assets/past-papers/
```

### List Recent Uploads
```sql
SELECT * FROM past_papers 
ORDER BY upload_date DESC 
LIMIT 10;
```

---

**Revision Date:** February 3, 2026
**Status:** ✅ Complete & Ready
