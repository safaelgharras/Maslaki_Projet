-- ============================================================
-- Migration: Add 'superadmin' role + assign platform owner
-- Run this ONCE in phpMyAdmin → SQL tab
-- ============================================================

-- Step 1: Extend the role ENUM to include 'superadmin'
ALTER TABLE `students`
    MODIFY COLUMN `role` ENUM('student', 'admin', 'superadmin') NOT NULL DEFAULT 'student';

-- Step 2: Promote ehsafaa7@gmail.com to superadmin (platform owner)
UPDATE `students`
    SET `role` = 'superadmin'
    WHERE `email` = 'ehsafaa7@gmail.com'
    LIMIT 1;

-- Step 3: Verify (optional — run separately to check)
-- SELECT id, name, email, role FROM students ORDER BY FIELD(role, 'superadmin', 'admin', 'student');
