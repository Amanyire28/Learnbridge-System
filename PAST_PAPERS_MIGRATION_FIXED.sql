-- Past Papers Tables for LearnBridge - Simplified Migration
-- Add these tables to the skillquest database

-- Table for past papers (without foreign keys first)
CREATE TABLE IF NOT EXISTS `past_papers` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `course_id` int(3) NOT NULL,
  `title` varchar(255) NOT NULL,
  `year` int(4) NOT NULL,
  `term` varchar(50) NOT NULL,
  `subject` varchar(100),
  `paper_file_path` varchar(255) NOT NULL,
  `solution_file_path` varchar(255),
  `file_size` int(11) DEFAULT NULL,
  `upload_date` timestamp DEFAULT CURRENT_TIMESTAMP,
  `uploaded_by` int(11) NOT NULL,
  `description` longtext,
  `is_active` tinyint(1) DEFAULT 1,
  INDEX `idx_course_id` (`course_id`),
  INDEX `idx_uploaded_by` (`uploaded_by`),
  INDEX `idx_year` (`year`),
  INDEX `idx_term` (`term`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Table for tracking past paper download/attempts
CREATE TABLE IF NOT EXISTS `past_paper_attempts` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `paper_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `attempt_type` varchar(50) DEFAULT 'download',
  `attempted_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(50),
  `user_agent` varchar(255),
  INDEX `idx_paper_id` (`paper_id`),
  INDEX `idx_student_id` (`student_id`),
  INDEX `idx_attempted_at` (`attempted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Table for tracking user answers/submissions on past papers
CREATE TABLE IF NOT EXISTS `past_paper_submissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `paper_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `submission_file_path` varchar(255),
  `score` int(11),
  `total_score` int(11),
  `submitted_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `status` varchar(50) DEFAULT 'pending',
  `feedback` longtext,
  INDEX `idx_paper_id` (`paper_id`),
  INDEX `idx_student_id` (`student_id`),
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Add Foreign Keys (after tables are created)
ALTER TABLE `past_papers` 
ADD CONSTRAINT `fk_past_papers_course` 
FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`) ON DELETE CASCADE,
ADD CONSTRAINT `fk_past_papers_user` 
FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT;

ALTER TABLE `past_paper_attempts`
ADD CONSTRAINT `fk_attempts_paper` 
FOREIGN KEY (`paper_id`) REFERENCES `past_papers` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `fk_attempts_student` 
FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

ALTER TABLE `past_paper_submissions`
ADD CONSTRAINT `fk_submissions_paper` 
FOREIGN KEY (`paper_id`) REFERENCES `past_papers` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `fk_submissions_student` 
FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
