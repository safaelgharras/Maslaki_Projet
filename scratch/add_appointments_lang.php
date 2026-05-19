<?php
$translations = [
    'my_appointments' => [
        'fr' => 'Mes Rendez-vous',
        'en' => 'My Appointments',
        'ar' => 'مواعيدي'
    ],
    'manage_orientation_sessions' => [
        'fr' => "Gérez vos sessions d'orientation avec nos experts.",
        'en' => 'Manage your orientation sessions with our experts.',
        'ar' => 'قم بإدارة جلسات التوجيه الخاصة بك مع خبرائنا.'
    ],
    'appointment_added_success' => [
        'fr' => 'Rendez-vous ajouté avec succès !',
        'en' => 'Appointment successfully added!',
        'ar' => 'تمت إضافة الموعد بنجاح!'
    ],
    'appointment_deleted' => [
        'fr' => 'Rendez-vous supprimé.',
        'en' => 'Appointment deleted.',
        'ar' => 'تم حذف الموعد.'
    ],
    'an_error_occurred' => [
        'fr' => 'Une erreur est survenue.',
        'en' => 'An error occurred.',
        'ar' => 'حدث خطأ.'
    ],
    'book_new_appointment' => [
        'fr' => 'Prendre un nouveau RDV',
        'en' => 'Book a new Appointment',
        'ar' => 'حجز موعد جديد'
    ],
    'appointment_subject' => [
        'fr' => 'Sujet du rendez-vous',
        'en' => 'Appointment Subject',
        'ar' => 'موضوع الموعد'
    ],
    'appointment_subject_placeholder' => [
        'fr' => 'Ex: Orientation ENSA, Aide inscription...',
        'en' => 'Ex: ENSA Orientation, Enrollment help...',
        'ar' => 'مثال: توجيه ENSA، مساعدة في التسجيل...'
    ],
    'appointment_date' => [
        'fr' => 'Date',
        'en' => 'Date',
        'ar' => 'التاريخ'
    ],
    'appointment_time_label' => [
        'fr' => 'Heure',
        'en' => 'Time',
        'ar' => 'الوقت'
    ],
    'confirm_appointment' => [
        'fr' => 'Confirmer le rendez-vous',
        'en' => 'Confirm Appointment',
        'ar' => 'تأكيد الموعد'
    ],
    'upcoming_appointments' => [
        'fr' => 'Vos rendez-vous à venir',
        'en' => 'Your upcoming appointments',
        'ar' => 'مواعيدك القادمة'
    ],
    'delete_appointment_confirm' => [
        'fr' => 'Supprimer ce rendez-vous ?',
        'en' => 'Delete this appointment?',
        'ar' => 'هل تريد حذف هذا الموعد؟'
    ],
    'no_appointments_yet' => [
        'fr' => "Vous n'avez pas encore de rendez-vous programmé.",
        'en' => 'You do not have any scheduled appointments yet.',
        'ar' => 'ليس لديك أي مواعيد مبرمجة بعد.'
    ],
    'status_pending' => [
        'fr' => 'En attente',
        'en' => 'Pending',
        'ar' => 'قيد الانتظار'
    ],
    'status_confirmed' => [
        'fr' => 'Confirmé',
        'en' => 'Confirmed',
        'ar' => 'مؤكد'
    ],
    'status_cancelled' => [
        'fr' => 'Annulé',
        'en' => 'Cancelled',
        'ar' => 'ملغى'
    ]
];

$langs = ['fr', 'en', 'ar'];

foreach ($langs as $lang) {
    $filePath = __DIR__ . "/../lang/$lang.php";
    if (file_exists($filePath)) {
        $content = file_get_contents($filePath);
        $newLines = "";
        foreach ($translations as $key => $values) {
            // Check if key already exists
            if (strpos($content, "'$key'") === false && strpos($content, "\"$key\"") === false) {
                $escapedValue = str_replace("'", "\'", $values[$lang]);
                $newLines .= "    '$key' => '$escapedValue',\n";
            }
        }
        
        if (!empty($newLines)) {
            // Remove the ending ]; and append new lines
            $content = preg_replace('/\];\s*$/', $newLines . "];\n", $content);
            file_put_contents($filePath, $content);
            echo "Updated $lang.php\n";
        } else {
            echo "No new keys for $lang.php\n";
        }
    }
}
?>
