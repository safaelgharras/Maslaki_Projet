-- Notifications System Setup

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;

-- 1. Add role to students if not exists
ALTER TABLE `students` ADD COLUMN IF NOT EXISTS `role` VARCHAR(20) DEFAULT 'student';

-- 2. Create Notifications table
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` enum('system', 'school', 'filiere', 'announcement', 'maintenance', 'orientation', 'deadline') NOT NULL DEFAULT 'system',
  `related_link` varchar(255) DEFAULT NULL,
  `is_global` tinyint(1) DEFAULT 1,
  `target_user_id` int(11) DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_notif_target_user` FOREIGN KEY (`target_user_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Create User Notifications (status tracking)
CREATE TABLE IF NOT EXISTS `user_notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `notification_id` int(11) NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `is_deleted` tinyint(1) DEFAULT 0,
  `read_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_notif` (`user_id`, `notification_id`),
  CONSTRAINT `fk_un_user` FOREIGN KEY (`user_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_un_notif` FOREIGN KEY (`notification_id`) REFERENCES `notifications` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Initial Sample Notifications
INSERT INTO `notifications` (`title`, `message`, `type`, `related_link`, `is_global`) VALUES
('Bienvenue sur Maslaki !', 'Découvrez notre nouveau système d\'orientation personnalisé.', 'system', 'views/orientation_explore.php', 1),
('Nouvelle École : ENSA Tanger', 'L\'ENSA Tanger a été ajoutée à la plateforme avec ses nouveaux seuils.', 'school', 'views/institution_detail.php?id=61', 1);

COMMIT;
