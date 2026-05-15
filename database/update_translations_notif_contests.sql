-- Add translation columns to notifications table
ALTER TABLE notifications 
ADD COLUMN title_ar VARCHAR(255) DEFAULT NULL AFTER title,
ADD COLUMN title_en VARCHAR(255) DEFAULT NULL AFTER title_ar,
ADD COLUMN message_ar TEXT DEFAULT NULL AFTER message,
ADD COLUMN message_en TEXT DEFAULT NULL AFTER message_ar;

-- Add translation columns to contests table
ALTER TABLE contests 
ADD COLUMN title_ar VARCHAR(255) DEFAULT NULL AFTER title,
ADD COLUMN title_en VARCHAR(255) DEFAULT NULL AFTER title_ar,
ADD COLUMN description_ar TEXT DEFAULT NULL AFTER description,
ADD COLUMN description_en TEXT DEFAULT NULL AFTER description_ar;

-- Update existing notifications with translations
UPDATE notifications SET 
    title_ar = 'مرحباً بكم في مسلكي !',
    title_en = 'Welcome to Maslaki!',
    message_ar = 'اكتشفوا نظام التوجيه الشخصي الجديد الخاص بنا.',
    message_en = 'Discover our new personalized orientation system.'
WHERE id = 1;

UPDATE notifications SET 
    title_ar = 'مدرسة جديدة: ENSA طنجة',
    title_en = 'New School: ENSA Tangier',
    message_ar = 'تمت إضافة مدرسة ENSA طنجة إلى المنصة.',
    message_en = 'ENSA Tangier has been added to the platform.'
WHERE id = 2;

-- Update existing contests with translations
UPDATE contests SET 
    title_ar = 'مباراة ENSA 2026',
    title_en = 'ENSA Contest 2026',
    description_ar = 'المباراة الوطنية المشتركة للمدارس الوطنية للعلوم التطبيقية.',
    description_en = 'National joint entrance exam for the National Schools of Applied Sciences.'
WHERE id = 1;

UPDATE contests SET 
    title_ar = 'اختبار الكفاءة ENCG (TAFEM)',
    title_en = 'ENCG Aptitude Test (TAFEM)',
    description_ar = 'اختبار القبول للمدارس الوطنية للتجارة والتسيير.',
    description_en = 'Admission test for the National Schools of Business and Management.'
WHERE id = 2;
