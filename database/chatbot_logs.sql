-- ============================================
-- Chatbot Logs Table
-- Maslaki AI Assistant
-- ============================================

CREATE TABLE IF NOT EXISTS `chatbot_logs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NULL,
  `question` TEXT NOT NULL,
  `answer` TEXT NOT NULL,
  `source` VARCHAR(20) DEFAULT 'gemini',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
