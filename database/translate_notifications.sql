-- Translate Existing Notifications

-- 1. Welcome Notification
UPDATE notifications SET 
    title_ar = 'مرحباً بكم في مسلكي !', 
    title_en = 'Welcome to Maslaki!',
    message_ar = 'اكتشفوا نظام التوجيه الشخصي الجديد الخاص بنا.', 
    message_en = 'Discover our new personalized orientation system.' 
WHERE message LIKE '%système d\'orientation personnalisé%';

-- 2. New School Notification
UPDATE notifications SET 
    title_ar = 'مدرسة جديدة: ENSA طنجة', 
    title_en = 'New School: ENSA Tangier',
    message_ar = 'تمت إضافة مدرسة ENSA طنجة إلى المنصة.', 
    message_en = 'ENSA Tangier has been added to the platform.' 
WHERE message LIKE '%ENSA Tanger a été ajoutée%';

-- 3. Catch all: if message_ar is null, use message
UPDATE notifications SET 
    title_ar = title, 
    title_en = title,
    message_ar = message, 
    message_en = message 
WHERE (message_ar IS NULL OR message_ar = '') AND (message_en IS NULL OR message_en = '');
