-- Early Warning Academic Monitoring System - Database Schema
-- Run this SQL to create all required tables

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Users Table
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `role` enum('administrator','faculty','advisor','student') NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `student_id` varchar(50) DEFAULT NULL,
  `program_id` int DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `username` (`username`),
  KEY `role` (`role`),
  KEY `program_id` (`program_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Academic Terms
DROP TABLE IF EXISTS `academic_terms`;
CREATE TABLE `academic_terms` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `is_current` tinyint(1) DEFAULT '0',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Programs/Majors
DROP TABLE IF EXISTS `programs`;
CREATE TABLE `programs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Courses
DROP TABLE IF EXISTS `courses`;
CREATE TABLE `courses` (
  `id` int NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `credits` int DEFAULT '3',
  `program_id` int DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `program_id` (`program_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sections
DROP TABLE IF EXISTS `sections`;
CREATE TABLE `sections` (
  `id` int NOT NULL AUTO_INCREMENT,
  `course_id` int NOT NULL,
  `term_id` int NOT NULL,
  `instructor_id` int DEFAULT NULL,
  `section_number` varchar(20) NOT NULL,
  `capacity` int DEFAULT '30',
  `schedule` varchar(255) DEFAULT NULL,
  `room` varchar(50) DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_section` (`course_id`, `term_id`, `section_number`),
  KEY `course_id` (`course_id`),
  KEY `term_id` (`term_id`),
  KEY `instructor_id` (`instructor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Enrollments
DROP TABLE IF EXISTS `enrollments`;
CREATE TABLE `enrollments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `student_id` int NOT NULL,
  `section_id` int NOT NULL,
  `enrollment_date` date NOT NULL,
  `status` enum('active','withdrawn','completed') DEFAULT 'active',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_enrollment` (`student_id`, `section_id`),
  KEY `student_id` (`student_id`),
  KEY `section_id` (`section_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Assessment Types
DROP TABLE IF EXISTS `assessment_types`;
CREATE TABLE `assessment_types` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `abbreviation` varchar(20) DEFAULT NULL,
  `weight` decimal(5,2) DEFAULT '0.00',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Assessments
DROP TABLE IF EXISTS `assessments`;
CREATE TABLE `assessments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `section_id` int NOT NULL,
  `assessment_type_id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `max_score` decimal(10,2) DEFAULT '100.00',
  `due_date` datetime DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `section_id` (`section_id`),
  KEY `assessment_type_id` (`assessment_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Grades
DROP TABLE IF EXISTS `grades`;
CREATE TABLE `grades` (
  `id` int NOT NULL AUTO_INCREMENT,
  `enrollment_id` int NOT NULL,
  `assessment_id` int NOT NULL,
  `score` decimal(10,2) DEFAULT NULL,
  `is_late` tinyint(1) DEFAULT '0',
  `feedback` text,
  `graded_by` int DEFAULT NULL,
  `graded_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_grade` (`enrollment_id`, `assessment_id`),
  KEY `enrollment_id` (`enrollment_id`),
  KEY `assessment_id` (`assessment_id`),
  KEY `graded_by` (`graded_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Attendance
DROP TABLE IF EXISTS `attendance`;
CREATE TABLE `attendance` (
  `id` int NOT NULL AUTO_INCREMENT,
  `enrollment_id` int NOT NULL,
  `date` date NOT NULL,
  `status` enum('present','absent','late','excused') DEFAULT 'present',
  `notes` text,
  `recorded_by` int DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `enrollment_id` (`enrollment_id`),
  KEY `date` (`date`),
  KEY `recorded_by` (`recorded_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Risk Assessments
DROP TABLE IF EXISTS `risk_assessments`;
CREATE TABLE `risk_assessments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `student_id` int NOT NULL,
  `term_id` int DEFAULT NULL,
  `risk_score` decimal(5,2) DEFAULT '0.00',
  `risk_level` enum('low','moderate','high') DEFAULT 'low',
  `attendance_score` decimal(5,2) DEFAULT '100.00',
  `grade_score` decimal(5,2) DEFAULT '100.00',
  `submission_score` decimal(5,2) DEFAULT '100.00',
  `late_score` decimal(5,2) DEFAULT '100.00',
  `calculated_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `student_id` (`student_id`),
  KEY `term_id` (`term_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Alerts
DROP TABLE IF EXISTS `alerts`;
CREATE TABLE `alerts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `student_id` int NOT NULL,
  `alert_type` enum('risk_increase','low_performance','critical_event','improvement') NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text,
  `severity` enum('info','warning','critical') DEFAULT 'info',
  `is_read` tinyint(1) DEFAULT '0',
  `created_by` int DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `student_id` (`student_id`),
  KEY `created_by` (`created_by`),
  KEY `is_read` (`is_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Notifications
DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `alert_id` int DEFAULT NULL,
  `type` enum('alert','email','sms') DEFAULT 'alert',
  `channel` enum('dashboard','email','sms') DEFAULT 'dashboard',
  `subject` varchar(255) DEFAULT NULL,
  `message` text,
  `is_sent` tinyint(1) DEFAULT '0',
  `sent_at` datetime DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `alert_id` (`alert_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Advisor Assignments
DROP TABLE IF EXISTS `advisor_assignments`;
CREATE TABLE `advisor_assignments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `advisor_id` int NOT NULL,
  `student_id` int NOT NULL,
  `assigned_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_assignment` (`advisor_id`, `student_id`),
  KEY `advisor_id` (`advisor_id`),
  KEY `student_id` (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Interventions
DROP TABLE IF EXISTS `interventions`;
CREATE TABLE `interventions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `student_id` int NOT NULL,
  `advisor_id` int NOT NULL,
  `type` varchar(100) DEFAULT NULL,
  `description` text,
  `outcome` text,
  `follow_up_date` date DEFAULT NULL,
  `status` enum('planned','in_progress','completed','cancelled') DEFAULT 'planned',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `student_id` (`student_id`),
  KEY `advisor_id` (`advisor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Activity Logs
DROP TABLE IF EXISTS `activity_logs`;
CREATE TABLE `activity_logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `entity_type` varchar(50) DEFAULT NULL,
  `entity_id` int DEFAULT NULL,
  `details` text,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `action` (`action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add foreign key constraints
ALTER TABLE `users` ADD CONSTRAINT `fk_users_program` FOREIGN KEY (`program_id`) REFERENCES `programs`(`id`) ON DELETE SET NULL;
ALTER TABLE `courses` ADD CONSTRAINT `fk_courses_program` FOREIGN KEY (`program_id`) REFERENCES `programs`(`id`) ON DELETE SET NULL;
ALTER TABLE `sections` ADD CONSTRAINT `fk_sections_course` FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE CASCADE;
ALTER TABLE `sections` ADD CONSTRAINT `fk_sections_term` FOREIGN KEY (`term_id`) REFERENCES `academic_terms`(`id`) ON DELETE CASCADE;
ALTER TABLE `sections` ADD CONSTRAINT `fk_sections_instructor` FOREIGN KEY (`instructor_id`) REFERENCES `users`(`id`) ON DELETE SET NULL;
ALTER TABLE `enrollments` ADD CONSTRAINT `fk_enrollments_student` FOREIGN KEY (`student_id`) REFERENCES `users`(`id`) ON DELETE CASCADE;
ALTER TABLE `enrollments` ADD CONSTRAINT `fk_enrollments_section` FOREIGN KEY (`section_id`) REFERENCES `sections`(`id`) ON DELETE CASCADE;
ALTER TABLE `assessments` ADD CONSTRAINT `fk_assessments_section` FOREIGN KEY (`section_id`) REFERENCES `sections`(`id`) ON DELETE CASCADE;
ALTER TABLE `assessments` ADD CONSTRAINT `fk_assessments_type` FOREIGN KEY (`assessment_type_id`) REFERENCES `assessment_types`(`id`) ON DELETE CASCADE;
ALTER TABLE `grades` ADD CONSTRAINT `fk_grades_enrollment` FOREIGN KEY (`enrollment_id`) REFERENCES `enrollments`(`id`) ON DELETE CASCADE;
ALTER TABLE `grades` ADD CONSTRAINT `fk_grades_assessment` FOREIGN KEY (`assessment_id`) REFERENCES `assessments`(`id`) ON DELETE CASCADE;
ALTER TABLE `grades` ADD CONSTRAINT `fk_grades_graded_by` FOREIGN KEY (`graded_by`) REFERENCES `users`(`id`) ON DELETE SET NULL;
ALTER TABLE `attendance` ADD CONSTRAINT `fk_attendance_enrollment` FOREIGN KEY (`enrollment_id`) REFERENCES `enrollments`(`id`) ON DELETE CASCADE;
ALTER TABLE `attendance` ADD CONSTRAINT `fk_attendance_recorded_by` FOREIGN KEY (`recorded_by`) REFERENCES `users`(`id`) ON DELETE SET NULL;
ALTER TABLE `risk_assessments` ADD CONSTRAINT `fk_risk_student` FOREIGN KEY (`student_id`) REFERENCES `users`(`id`) ON DELETE CASCADE;
ALTER TABLE `risk_assessments` ADD CONSTRAINT `fk_risk_term` FOREIGN KEY (`term_id`) REFERENCES `academic_terms`(`id`) ON DELETE SET NULL;
ALTER TABLE `alerts` ADD CONSTRAINT `fk_alerts_student` FOREIGN KEY (`student_id`) REFERENCES `users`(`id`) ON DELETE CASCADE;
ALTER TABLE `alerts` ADD CONSTRAINT `fk_alerts_created_by` FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL;
ALTER TABLE `notifications` ADD CONSTRAINT `fk_notifications_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE;
ALTER TABLE `notifications` ADD CONSTRAINT `fk_notifications_alert` FOREIGN KEY (`alert_id`) REFERENCES `alerts`(`id`) ON DELETE SET NULL;
ALTER TABLE `advisor_assignments` ADD CONSTRAINT `fk_advisor_assign_advisor` FOREIGN KEY (`advisor_id`) REFERENCES `users`(`id`) ON DELETE CASCADE;
ALTER TABLE `advisor_assignments` ADD CONSTRAINT `fk_advisor_assign_student` FOREIGN KEY (`student_id`) REFERENCES `users`(`id`) ON DELETE CASCADE;
ALTER TABLE `interventions` ADD CONSTRAINT `fk_interventions_student` FOREIGN KEY (`student_id`) REFERENCES `users`(`id`) ON DELETE CASCADE;
ALTER TABLE `interventions` ADD CONSTRAINT `fk_interventions_advisor` FOREIGN KEY (`advisor_id`) REFERENCES `users`(`id`) ON DELETE CASCADE;
ALTER TABLE `activity_logs` ADD CONSTRAINT `fk_activity_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL;

SET FOREIGN_KEY_CHECKS = 1;

-- Insert sample data

-- Sample Programs
INSERT INTO `programs` (`code`, `name`, `description`) VALUES
('CS', 'Computer Science', 'Bachelor of Science in Computer Science'),
('BA', 'Business Administration', 'Bachelor of Science in Business Administration'),
('ENG', 'English', 'Bachelor of Arts in English'),
('MATH', 'Mathematics', 'Bachelor of Science in Mathematics');

-- Grade Scales
DROP TABLE IF EXISTS `grade_scales`;
CREATE TABLE `grade_scales` (
  `id` int NOT NULL AUTO_INCREMENT,
  `letter_grade` varchar(2) NOT NULL,
  `min_percentage` decimal(5,2) NOT NULL,
  `max_percentage` decimal(5,2) NOT NULL,
  `grade_points` decimal(3,2) NOT NULL DEFAULT '0.00',
  `description` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample Grade Scales
INSERT INTO `grade_scales` (`letter_grade`, `min_percentage`, `max_percentage`, `grade_points`, `description`) VALUES
('A', 90.00, 100.00, 4.00, 'Excellent'),
('A-', 85.00, 89.99, 3.70, 'Excellent'),
('B+', 82.00, 84.99, 3.30, 'Good'),
('B', 78.00, 81.99, 3.00, 'Good'),
('B-', 75.00, 77.99, 2.70, 'Above Average'),
('C+', 72.00, 74.99, 2.30, 'Average'),
('C', 68.00, 71.99, 2.00, 'Average'),
('C-', 65.00, 67.99, 1.70, 'Below Average'),
('D', 60.00, 64.99, 1.00, 'Passing'),
('F', 0.00, 59.99, 0.00, 'Failing');

-- Sample Academic Terms
INSERT INTO `academic_terms` (`name`, `start_date`, `end_date`, `is_current`) VALUES
('Fall 2025', '2025-08-15', '2025-12-15', 1),
('Spring 2026', '2026-01-10', '2026-05-10', 0);

-- Sample Assessment Types
INSERT INTO `assessment_types` (`name`, `abbreviation`, `weight`) VALUES
('Quiz', 'QZ', 15.00),
('Midterm Exam', 'ME', 20.00),
('Final Exam', 'FE', 25.00),
('Assignment', 'ASG', 20.00),
('Project', 'PRJ', 15.00),
('Participation', 'PART', 5.00);

-- Sample Users (password: Password123)
INSERT INTO `users` (`username`, `email`, `password_hash`, `first_name`, `last_name`, `role`, `phone`) VALUES
('admin', 'admin@edu.com', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5GyYIq.3iHJ3u', 'System', 'Administrator', 'administrator', '555-0100'),
('jsmith', 'john.smith@edu.com', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5GyYIq.3iHJ3u', 'John', 'Smith', 'faculty', '555-0101'),
('sjohnson', 'sarah.johnson@edu.com', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5GyYIq.3iHJ3u', 'Sarah', 'Johnson', 'advisor', '555-0102'),
('mwilliams', 'mike.williams@edu.com', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5GyYIq.3iHJ3u', 'Mike', 'Williams', 'student', '555-0103'),
('ebrown', 'emily.brown@edu.com', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5GyYIq.3iHJ3u', 'Emily', 'Brown', 'student', '555-0104'),
('djones', 'david.jones@edu.com', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5GyYIq.3iHJ3u', 'David', 'Jones', 'student', '555-0105'),
('jdavis', 'jennifer.davis@edu.com', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5GyYIq.3iHJ3u', 'Jennifer', 'Davis', 'student', '555-0106'),
('cmiller', 'chris.miller@edu.com', '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5GyYIq.3iHJ3u', 'Chris', 'Miller', 'student', '555-0107');

-- Sample Courses
INSERT INTO `courses` (`code`, `name`, `description`, `credits`, `program_id`) VALUES
('CS101', 'Introduction to Programming', 'Basic programming concepts using Python', 3, 1),
('CS201', 'Data Structures', 'Fundamental data structures and algorithms', 3, 1),
('BA101', 'Introduction to Business', 'Overview of business principles', 3, 2),
('MATH101', 'Calculus I', 'Introduction to differential calculus', 4, 4);

-- Sample Sections
INSERT INTO `sections` (`course_id`, `term_id`, `instructor_id`, `section_number`, `capacity`, `schedule`, `room`) VALUES
(1, 1, 2, 'A', 30, 'MWF 9:00-10:00', 'Room 101'),
(2, 1, 2, 'A', 25, 'TTh 11:00-12:30', 'Room 203'),
(3, 1, 2, 'A', 35, 'MWF 14:00-15:00', 'Room 105'),
(4, 1, 2, 'A', 30, 'TTh 9:00-11:00', 'Room 201');

-- Sample Enrollments (Student IDs: 4=Mike, 5=Emily, 6=David, 7=Jennifer, 8=Chris)
INSERT INTO `enrollments` (`student_id`, `section_id`, `enrollment_date`, `status`) VALUES
-- Mike Williams (Student 4) - enrolled in CS101 and BA101
(4, 1, '2025-08-20', 'active'),
(4, 2, '2025-08-20', 'active'),
-- Emily Brown (Student 5) - enrolled in CS101 and CS201
(5, 1, '2025-08-20', 'active'),
(5, 3, '2025-08-20', 'active'),
-- David Jones (Student 6) - enrolled in CS101 and BA101 (struggling)
(6, 1, '2025-08-20', 'active'),
(6, 2, '2025-08-20', 'active'),
-- Jennifer Davis (Student 7) - enrolled in CS201 (high risk)
(7, 3, '2025-08-20', 'active'),
-- Chris Miller (Student 8) - enrolled in BA101 (moderate)
(8, 2, '2025-08-20', 'active');

-- Sample Assessments
INSERT INTO `assessments` (`section_id`, `assessment_type_id`, `name`, `max_score`, `due_date`) VALUES
-- Section 1 (CS101)
(1, 1, 'Quiz 1', 100, '2025-09-05'),
(1, 1, 'Quiz 2', 100, '2025-09-19'),
(1, 2, 'Midterm Exam', 100, '2025-10-15'),
(1, 4, 'Assignment 1', 100, '2025-09-10'),
(1, 4, 'Assignment 2', 100, '2025-10-01'),
(1, 3, 'Final Exam', 100, '2025-12-10'),
-- Section 2 (BA101)
(2, 1, 'Quiz 1', 50, '2025-09-08'),
(2, 2, 'Midterm Exam', 100, '2025-10-20'),
(2, 4, 'Assignment 1', 75, '2025-09-15'),
(2, 5, 'Project', 100, '2025-11-01'),
(2, 3, 'Final Exam', 100, '2025-12-12'),
-- Section 3 (CS201)
(3, 1, 'Quiz 1', 100, '2025-09-10'),
(3, 2, 'Midterm Exam', 100, '2025-10-18'),
(3, 4, 'Programming Assignment 1', 100, '2025-09-20'),
(3, 4, 'Programming Assignment 2', 100, '2025-10-10'),
(3, 5, 'Group Project', 100, '2025-11-15'),
(3, 3, 'Final Exam', 100, '2025-12-15');

-- Sample Grades (enrollment_id maps to the order above)
-- Enrollment IDs: 1=Mike-CS101, 2=Mike-BA101, 3=Emily-CS101, 4=Emily-CS201, 5=David-CS101, 6=David-BA101, 7=Jennifer-CS201, 8=Chris-BA101
INSERT INTO `grades` (`enrollment_id`, `assessment_id`, `score`, `is_late`, `graded_by`) VALUES
-- Mike Williams (enrollment 1, section 1) - Good grades
(1, 1, 88, 0, 2),
(1, 2, 92, 0, 2),
(1, 3, 85, 0, 2),
(1, 4, 90, 0, 2),
(1, 5, 78, 0, 2),
-- Mike Williams (enrollment 2, section 2) - Good grades
(2, 7, 45, 0, 2),
(2, 8, 88, 0, 2),
(2, 9, 70, 0, 2),
-- Emily Brown (enrollment 3, section 1) - Excellent grades
(3, 1, 95, 0, 2),
(3, 2, 98, 0, 2),
(3, 3, 92, 0, 2),
(3, 4, 100, 0, 2),
(3, 5, 88, 0, 2),
-- Emily Brown (enrollment 4, section 3) - Excellent grades
(4, 13, 90, 0, 2),
(4, 14, 85, 0, 2),
(4, 15, 78, 0, 2),
-- David Jones (enrollment 5, section 1) - Struggling
(5, 1, 55, 0, 2),
(5, 2, 48, 1, 2),
(5, 3, 42, 0, 2),
(5, 4, 60, 0, 2),
(5, 5, 35, 1, 2),
-- David Jones (enrollment 6, section 2) - Failing
(6, 7, 30, 0, 2),
(6, 8, 25, 0, 2),
(6, 9, 40, 1, 2),
-- Jennifer Davis (enrollment 7, section 3) - High Risk student
(7, 13, 45, 0, 2),
(7, 14, 38, 1, 2),
(7, 15, 30, 0, 2),
(7, 16, 55, 0, 2),
(7, 17, 42, 1, 2),
-- Chris Miller (enrollment 8, section 2) - Moderate
(8, 7, 35, 0, 2),
(8, 8, 55, 0, 2),
(8, 9, 45, 1, 2);

-- Sample Risk Assessments (with 4 indicators)
INSERT INTO `risk_assessments` (`student_id`, `term_id`, `risk_score`, `risk_level`, `attendance_score`, `grade_score`, `submission_score`, `late_score`) VALUES
-- Mike Williams - Low Risk
(4, 1, 12.00, 'low', 95.00, 86.00, 100.00, 100.00),
-- Emily Brown - Low Risk (Excellent)
(5, 1, 5.00, 'low', 100.00, 94.00, 100.00, 100.00),
-- David Jones - High Risk (failing)
(6, 1, 72.00, 'high', 70.00, 42.00, 60.00, 67.00),
-- Jennifer Davis - High Risk
(7, 1, 78.00, 'high', 65.00, 38.00, 80.00, 50.00),
-- Chris Miller - Moderate Risk
(8, 1, 52.00, 'moderate', 80.00, 55.00, 90.00, 67.00);

-- Sample Alerts
INSERT INTO `alerts` (`student_id`, `alert_type`, `title`, `message`, `severity`, `created_by`) VALUES
(6, 'risk_increase', 'Risk Level Increased to Moderate', 'Student risk level has increased from Low to Moderate due to declining grades', 'warning', 1),
(7, 'risk_increase', 'High Risk Alert', 'Student is now at High Risk - immediate intervention recommended', 'critical', 1),
(4, 'improvement', 'Performance Improvement', 'Student grade average has improved by 10%', 'info', 1);

-- Student Final Grades
DROP TABLE IF EXISTS `student_final_grades`;
CREATE TABLE `student_final_grades` (
  `id` int NOT NULL AUTO_INCREMENT,
  `student_id` int NOT NULL,
  `section_id` int NOT NULL,
  `term_id` int DEFAULT NULL,
  `final_grade` varchar(2) DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_student_section` (`student_id`, `section_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Student Class Standing
DROP TABLE IF EXISTS `student_class_standing`;
CREATE TABLE `student_class_standing` (
  `id` int NOT NULL AUTO_INCREMENT,
  `student_id` int NOT NULL,
  `section_id` int NOT NULL,
  `term_id` int DEFAULT NULL,
  `standing` decimal(5,2) DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_student_section` (`student_id`, `section_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample Advisor Assignment
INSERT INTO `advisor_assignments` (`advisor_id`, `student_id`) VALUES
(3, 4),
(3, 5),
(3, 6),
(3, 7);
