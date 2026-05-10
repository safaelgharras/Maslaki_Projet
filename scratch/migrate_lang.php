<?php
$files = ['fr', 'en', 'ar'];
$newTrans = [
    'notifications_center' => ['Centre de Notifications', 'Notifications Center', 'مركز الإشعارات'],
    'all_notifs' => ['Toutes', 'All', 'الكل'],
    'announcements' => ['Annonces', 'Announcements', 'إعلانات'],
    'schools' => ['Écoles', 'Schools', 'مدارس'],
    'deadlines' => ['Dates Limites', 'Deadlines', 'مواعيد نهائية'],
    'system' => ['Système', 'System', 'نظام'],
    'see_more' => ['Voir plus', 'See more', 'عرض المزيد'],
    'mark_as_read' => ['Marquer comme lu', 'Mark as read', 'تحديد كمقروء'],
    'delete' => ['Supprimer', 'Delete', 'حذف'],
    'up_to_date_msg' => ['Vous êtes à jour ! Revenez plus tard pour de nouvelles actualités.', 'You are up to date! Check back later for new updates.', 'أنت على اطلاع! عد لاحقًا للحصول على التحديثات الجديدة.'],
    'confirm_delete_notif' => ['Voulez-vous vraiment supprimer cette notification ?', 'Are you sure you want to delete this notification?', 'هل أنت متأكد أنك تريد حذف هذا الإشعار؟']
];
foreach ($files as $index => $lang) {
    $path = __DIR__ . '/../lang/' . $lang . '.php';
    $content = file_get_contents($path);
    $add = '';
    foreach ($newTrans as $key => $vals) {
        $add .= "    '" . $key . "' => '" . addslashes($vals[$index]) . "',\n";
    }
    $content = str_replace('];', $add . '];', $content);
    file_put_contents($path, $content);
}
echo "Done\n";
