-- Re-applying translations columns

ALTER TABLE notifications ADD COLUMN IF NOT EXISTS title_ar VARCHAR(255) AFTER title;
ALTER TABLE notifications ADD COLUMN IF NOT EXISTS title_en VARCHAR(255) AFTER title_ar;
ALTER TABLE notifications ADD COLUMN IF NOT EXISTS message_ar TEXT AFTER message;
ALTER TABLE notifications ADD COLUMN IF NOT EXISTS message_en TEXT AFTER message_ar;

ALTER TABLE contests ADD COLUMN IF NOT EXISTS title_ar VARCHAR(255) AFTER title;
ALTER TABLE contests ADD COLUMN IF NOT EXISTS title_en VARCHAR(255) AFTER title_ar;
ALTER TABLE contests ADD COLUMN IF NOT EXISTS description_ar TEXT AFTER description;
ALTER TABLE contests ADD COLUMN IF NOT EXISTS description_en TEXT AFTER description_ar;
