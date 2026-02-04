# Past Papers Implementation Guide

## Overview
This guide explains how to set up and use the Past Papers feature in LearnBridge, enabling students to access past examination papers and solutions.

## 📋 Table of Contents
1. [Database Setup](#database-setup)
2. [File Structure](#file-structure)
3. [Admin Features](#admin-features)
4. [Student Features](#student-features)
5. [File Management](#file-management)
6. [Low Bandwidth Support](#low-bandwidth-support)
7. [Troubleshooting](#troubleshooting)

---

## Database Setup

### Step 1: Run Migration SQL
Execute the following SQL commands in your database:

```bash
# Using command line
mysql -u root -p skillquest < PAST_PAPERS_MIGRATION.sql

# Or use phpMyAdmin to import PAST_PAPERS_MIGRATION.sql
```

### Step 2: Verify Tables Created
Three new tables will be created:
- **past_papers** - Stores past paper metadata
- **past_paper_attempts** - Tracks download/access attempts
- **past_paper_submissions** - Tracks student submissions (optional)

---

## File Structure

```
assets/
├── past-papers/
│   ├── course-1/
│   │   ├── 2024/
│   │   │   ├── 1707456000_term1_exam.pdf
│   │   │   ├── 1707456000_solution_term1_exam.pdf
│   │   ├── 2023/
│   ├── course-2/
│   │   ├── 2024/
```

**Automatic Directory Creation:** Directories are created automatically when admins upload papers.

---

## Admin Features

### Accessing Past Papers Management
1. Go to **Admin Dashboard**
2. Click **Past Papers** in the navigation menu
3. You'll see:
   - Upload form for new papers
   - List of uploaded papers
   - Management options

### Uploading a Past Paper

#### Required Fields:
- **Course** - Select from available courses
- **Title** - e.g., "End of Term 1 Exam"
- **Year** - e.g., 2024
- **Term** - Select from dropdown (Term 1, 2, 3, Mid-term, Final)
- **Paper File** - PDF or Word document (max 10MB)

#### Optional Fields:
- **Subject** - e.g., "Mathematics"
- **Solution File** - Separate solution document
- **Description** - Additional notes

### Uploading Process
```
1. Fill in form fields
2. Click "Upload Past Paper"
3. System validates file:
   - Checks file type (PDF/Word only)
   - Verifies file size (max 10MB)
   - Validates course exists
4. Files uploaded to: assets/past-papers/course-{id}/{year}/
5. Metadata saved to database
6. Success message displayed
```

### Managing Papers
**For each paper, admins can:**
- View download statistics
- Download the paper file
- Delete the paper (removes files from disk)
- Mark as inactive (hides from students)

### Viewing Statistics
Click **Statistics** to see:
- Total papers uploaded
- Courses with papers
- Students who accessed papers
- Storage usage
- Most popular papers
- Recent downloads

---

## Student Features

### Accessing Past Papers
1. Click **Past Papers** link (in main navigation)
2. Browse or filter available papers

### Filtering Options
- **Course** - Filter by selected course
- **Year** - Filter by year (2020-2024, etc.)
- **Term** - Filter by term
- **Search** - Search by title or subject

### Downloading Papers
For each paper:
- View key details (year, term, subject)
- See how many times downloaded
- Download paper file
- Download solution file (if available)
- View download history

### Access Control
- **Admin**: Can access all papers
- **Students**: Can only access papers from enrolled courses

---

## File Management

### File Naming
Files are named with timestamp to avoid conflicts:
```
{timestamp}_{original_filename}.pdf
{timestamp}_solution_{original_filename}.pdf
```

Example:
```
1707456000_final_exam_2024.pdf
1707456000_solution_final_exam_2024.pdf
```

### File Limits
- **Maximum size**: 10MB per file
- **Supported formats**: PDF, .doc, .docx
- **Storage location**: assets/past-papers/

### Cleaning Up Files
To delete a paper:
1. Admin clicks **Delete** button
2. System removes:
   - Paper file from disk
   - Solution file from disk
   - Database records
   - Download history

---

## Low Bandwidth Support

### Network Detection
The system automatically detects low network conditions:
- Connection type: 2G, 3G
- Download speed: < 1 Mbps
- User can manually enable low-bandwidth mode

### Low Bandwidth Features
In low-bandwidth mode:
- ✅ Images disabled (showing text placeholders)
- ✅ Videos disabled
- ✅ CSS animations removed
- ✅ Content compressed
- ✅ Faster loading

### Using in Low Bandwidth Areas
1. Toggle "Low Bandwidth Mode" in UI
2. System detects slow connection automatically
3. Interface adapts:
   - Removes heavy elements
   - Shows lightweight version
   - Enables offline caching

---

## Database Queries

### Check Papers for a Course
```sql
SELECT * FROM past_papers 
WHERE course_id = 1 
ORDER BY year DESC, term DESC;
```

### Get Download Statistics
```sql
SELECT pp.id, pp.title, COUNT(ppa.id) as downloads
FROM past_papers pp
LEFT JOIN past_paper_attempts ppa ON pp.id = ppa.paper_id
GROUP BY pp.id
ORDER BY downloads DESC;
```

### Get Student Download History
```sql
SELECT pp.title, ppa.attempted_at
FROM past_paper_attempts ppa
JOIN past_papers pp ON ppa.paper_id = pp.id
WHERE ppa.student_id = 5
ORDER BY ppa.attempted_at DESC;
```

---

## Key Files Created

| File | Purpose |
|------|---------|
| `PAST_PAPERS_MIGRATION.sql` | Database migration script |
| `includes/admin/uploadpastpaper.php` | Admin upload interface |
| `includes/admin/pastpapersstatistics.php` | Statistics dashboard |
| `includes/course/downloadpaper.php` | Download handler (tracks attempts) |
| `past-papers.php` | Student browsing page |

---

## API Endpoints

### Download Paper
```
GET /includes/course/downloadpaper.php?id={id}&type={paper|solution}
```

**Parameters:**
- `id` - Past paper ID
- `type` - "paper" or "solution"
- `compress` - Optional: "1" for low-bandwidth (future feature)

**Returns:** File download with tracking

---

## Troubleshooting

### Problem: Papers not appearing for students
**Solution:**
1. Check student is enrolled in course
2. Verify paper's `is_active` = 1
3. Check file paths in database are correct
4. Verify uploads directory has write permissions

### Problem: Download button not working
**Solution:**
1. Check file exists in assets/past-papers/
2. Verify database record is correct
3. Check PHP file_exists() in console
4. Verify MIME type is supported

### Problem: Students seeing all papers
**Solution:**
1. Check course enrollment in `completed_courses` table
2. Verify SQL query filters by user enrollment
3. Admin role might bypass filters intentionally

### Problem: Files not uploading
**Solution:**
1. Check directory permissions: `chmod 755 assets/past-papers/`
2. Verify file size < 10MB
3. Check file type is PDF or Word
4. Verify PHP upload_max_filesize in php.ini
5. Check disk space available

### Problem: Storage using too much space
**Solution:**
1. Review `past_papers` table for duplicate entries
2. Delete unused papers via admin interface
3. Consider compressing old PDFs
4. Archive old years separately

---

## Security Considerations

### Access Control
- Download handler checks user enrollment before serving files
- Admins can access any paper
- Students limited to enrolled courses

### File Safety
- Files stored outside web root (best practice)
- MIME type validation on upload
- Filename sanitization to prevent injection

### Tracking
- Download attempts logged with:
  - Student ID
  - Paper ID
  - Timestamp
  - IP address
  - User agent

---

## Performance Tips

1. **Compress PDFs** - Use tools like Ghostscript to reduce file size
2. **Limit downloads** - Archive very old papers
3. **Database indexing** - Indexes on `course_id`, `year`, `term` for faster queries
4. **Caching** - Cache frequently downloaded papers

---

## Future Enhancements

### Planned Features:
- [ ] Automatic PDF compression
- [ ] Student answer submissions
- [ ] Automated grading integration
- [ ] Discussion forums per paper
- [ ] Difficulty ratings
- [ ] Time-limited access
- [ ] Watermarking for security
- [ ] Batch download (ZIP)

---

## Support

For issues or questions:
1. Check logs in browser console
2. Review error messages in alerts
3. Verify database entries
4. Check file permissions
5. Test with sample file first

---

**Last Updated:** February 3, 2026
