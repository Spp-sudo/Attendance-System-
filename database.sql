-- =============================================
-- Student Attendance Management System
-- Database Setup SQL File with USN
-- =============================================

CREATE DATABASE IF NOT EXISTS attendance_db;
USE attendance_db;

CREATE TABLE IF NOT EXISTS students (
    student_id INT PRIMARY KEY AUTO_INCREMENT,  -- Internal ID used for relationships
    usn VARCHAR(30) NOT NULL UNIQUE,            -- Student USN shown in the UI
    name VARCHAR(100) NOT NULL,
    department VARCHAR(100) NOT NULL
);

CREATE TABLE IF NOT EXISTS attendance (
    attendance_id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    date DATE NOT NULL,
    status VARCHAR(10) NOT NULL,
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
    UNIQUE KEY unique_student_date (student_id, date)
);

INSERT INTO students (usn, name, department) VALUES
('1SP24AI046', 'Sourav', 'AIML'),
('1SP24AI026', 'Niranjan', 'AIML'),
('1SP24CS001', 'Praveen', 'CSE'),
('1SP24AI029', 'Nuthan', 'AIML'),
('1SP24CS004', 'Kamal', 'CSE'),
('1SP24AI020', 'Mani', 'AIML'),
('1SP25AI404', 'Muruli', 'AIML');
