-- ============================================================
-- Create superadmin account: admin@maslaki.ma
-- Run ONCE in phpMyAdmin → SQL tab
-- ============================================================

-- Step 1: Make sure the role ENUM includes 'superadmin'
ALTER TABLE `students`
    MODIFY COLUMN `role` ENUM('student','admin','superadmin') NOT NULL DEFAULT 'student';

-- Step 2: Insert the superadmin account (skip if email already exists)
INSERT INTO `students` (`name`, `email`, `password`, `bac_branch`, `average`, `city`, `role`)
SELECT
    'Super Admin',
    'admin@maslaki.ma',
    '$2y$10$JVvvDc0rGJFunroFN0eW9e3DsSWoLPAbNzAD2bxJBXwY6kP..ZDKK',
    'Sciences Math',
    20,
    'Maroc',
    'superadmin'
WHERE NOT EXISTS (
    SELECT 1 FROM `students` WHERE `email` = 'admin@maslaki.ma'
);

-- Step 3: If the email already exists, just promote it to superadmin
UPDATE `students`
    SET `role` = 'superadmin'
    WHERE `email` = 'admin@maslaki.ma';

-- Verify
SELECT id, name, email, role FROM `students` WHERE email = 'admin@maslaki.ma';
