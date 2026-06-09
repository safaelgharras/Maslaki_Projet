-- Add rating column to reviews table
-- Run once. Safe to run again (IF NOT EXISTS guard via SHOW COLUMNS check is not
-- standard SQL, but the PHP fallback in submit_review.php handles missing column too).
ALTER TABLE `reviews`
    ADD COLUMN IF NOT EXISTS `rating` tinyint(1) DEFAULT NULL
        COMMENT '1–5 star rating, NULL means no rating given'
    AFTER `content`;
