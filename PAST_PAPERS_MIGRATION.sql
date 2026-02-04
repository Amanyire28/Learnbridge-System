-- Past Papers Tables for LearnBridge
-- Add these tables to the skillquest database

-- Table for past papers
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
  `description` text,
  `is_active` tinyint(1) DEFAULT 1,
  KEY `course_id` (`course_id`),
  KEY `uploaded_by` (`uploaded_by`),
  KEY `year` (`year`),
  KEY `term` (`term`),
  CONSTRAINT `fk_past_papers_course` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_past_papers_user` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Table for tracking past paper download/attempts
CREATE TABLE IF NOT EXISTS `past_paper_attempts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `paper_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `attempt_type` varchar(50) DEFAULT 'download',
  `attempted_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(50),
  `user_agent` varchar(255),
  PRIMARY KEY (`id`),
  KEY `paper_id` (`paper_id`),
  KEY `student_id` (`student_id`),
  KEY `attempted_at` (`attempted_at`),
  FOREIGN KEY (`paper_id`) REFERENCES `past_papers` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Table for tracking user answers/submissions on past papers
CREATE TABLE IF NOT EXISTS `past_paper_submissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `paper_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `submission_file_path` varchar(255),
  `score` int(11),
  `total_score` int(11),
  `submitted_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `status` varchar(50) DEFAULT 'pending',
  `feedback` text,
  PRIMARY KEY (`id`),
  KEY `paper_id` (`paper_id`),
  KEY `student_id` (`student_id`),
  KEY `status` (`status`),
  FOREIGN KEY (`paper_id`) REFERENCES `past_papers` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
