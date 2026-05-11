<?php
require 'config/DataBase.php';

$translations = [
    1 => ["ar" => "المعلوميات والرقميات", "en" => "IT & Digital"],
    2 => ["ar" => "الذكاء الاصطناعي والبيانات", "en" => "AI & Data"],
    3 => ["ar" => "الهندسة والتكنولوجيا", "en" => "Engineering & Technology"],
    4 => ["ar" => "الروبوتات والأتمتة", "en" => "Robotics & Automation"],
    5 => ["ar" => "الاتصالات والشبكات", "en" => "Telecommunications & Networks"],
    6 => ["ar" => "هندسة البرمجيات والأنظمة المدمجة", "en" => "Software Engineering & Embedded Systems"],
    7 => ["ar" => "الرياضيات والإحصاء", "en" => "Mathematics & Statistics"],
    8 => ["ar" => "الفيزياء والكيمياء", "en" => "Physics & Chemistry"],
    9 => ["ar" => "البحث والابتكار", "en" => "Research & Innovation"],
    10 => ["ar" => "الهندسة المدنية والبناء", "en" => "Civil Engineering & Construction"],
    11 => ["ar" => "الصناعة والصيانة", "en" => "Industry & Maintenance"],
    12 => ["ar" => "السيارات والميكانيك", "en" => "Automotive & Mechanics"],
    13 => ["ar" => "الطاقة والطاقات المتجددة", "en" => "Energy & Renewable Energies"],
    14 => ["ar" => "المناجم وعلوم الأرض", "en" => "Mining & Geosciences"],
    15 => ["ar" => "الماء والهيدروليك والتطهير", "en" => "Water, Hydraulics & Sanitation"],
    16 => ["ar" => "النسيج وصناعة الملابس", "en" => "Textile & Clothing Industry"],
    17 => ["ar" => "الصحة والطب", "en" => "Health & Medicine"],
    18 => ["ar" => "البيولوجيا والتكنولوجيا الحيوية", "en" => "Biology & Biotechnology"],
    19 => ["ar" => "المختبر والتحاليل", "en" => "Laboratory & Analysis"],
    20 => ["ar" => "علم النفس والعلوم الاجتماعية", "en" => "Psychology & Social Sciences"],
    21 => ["ar" => "الفلاحة والصناعات الغذائية", "en" => "Agriculture & Food Industry"],
    22 => ["ar" => "البيئة والتنمية المستدامة", "en" => "Environment & Sustainable Development"],
    23 => ["ar" => "البحري والصيد", "en" => "Maritime & Fishing"],
    24 => ["ar" => "التجارة والتسيير والاقتصاد", "en" => "Commerce, Management & Economics"],
    25 => ["ar" => "المالية والأبناك", "en" => "Finance & Banking"],
    26 => ["ar" => "المحاسبة والتدقيق والضرائب", "en" => "Accounting, Audit & Taxation"],
    27 => ["ar" => "التسويق والتجارة الدولية", "en" => "Marketing & International Trade"],
    28 => ["ar" => "اللوجستيك وسلسلة التوريد", "en" => "Logistics & Supply Chain"],
    29 => ["ar" => "الموارد البشرية والإدارة", "en" => "Human Resources & Management"],
    30 => ["ar" => "الرياضيات الاكتوارية والتأمين", "en" => "Actuarial Science & Insurance"],
    31 => ["ar" => "القانون والعلوم السياسية", "en" => "Law & Political Science"],
    32 => ["ar" => "الأمن والدفاع", "en" => "Security & Defense"],
    33 => ["ar" => "الاجتماعي والتنمية البشرية", "en" => "Social & Human Development"],
    34 => ["ar" => "الدراسات الإسلامية والشريعة", "en" => "Islamic Studies & Sharia"],
    35 => ["ar" => "الهندسة المعمارية والتصميم والفنون", "en" => "Architecture, Design & Arts"],
    36 => ["ar" => "الوسائط المتعددة والسمعي البصري", "en" => "Multimedia & Audiovisual"],
    37 => ["ar" => "التواصل والصحافة والإعلام", "en" => "Communication, Journalism & Media"],
    38 => ["ar" => "السينما والإنتاج السمعي البصري", "en" => "Cinema & Audiovisual Production"],
    39 => ["ar" => "الموسيقى وفنون العرض", "en" => "Music & Performing Arts"],
    40 => ["ar" => "الموضة والتصميم والجمال", "en" => "Mode, Styling & Beauty"],
    41 => ["ar" => "الطيران والنقل والبحري", "en" => "Aviation, Transport & Maritime"],
    42 => ["ar" => "السياحة والفندقة والمطاعم", "en" => "Tourism, Hotel & Catering"],
    43 => ["ar" => "الطبخ وفنون الطهي", "en" => "Cooking & Culinary Arts"],
    44 => ["ar" => "التربية واللغات", "en" => "Education & Languages"],
    45 => ["ar" => "الجغرافيا والجيولوجيا والتعمير", "en" => "Geography, Geology & Urbanism"],
    46 => ["ar" => "التراث وعلم الآثار", "en" => "Heritage & Archaeology"],
];

foreach ($translations as $id => $data) {
    $stmt = $pdo->prepare("UPDATE domains SET nom_ar = ?, nom_en = ? WHERE id = ?");
    $stmt->execute([$data['ar'], $data['en'], $id]);
    echo "Updated domain $id\n";
}

echo "All domains translated.\n";
