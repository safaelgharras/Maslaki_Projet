<?php
$checks_register = [
    'auth-visual-side', 'auth-banner-img', 'auth-glass-card',
    'btn-google', 'auth-divider', 'auth_banner.png',
    'google_callback', 'form-grid-three', 'register_process',
    'googleSimModal',
];
$checks_login = [
    'auth-visual-side', 'auth-banner-img', 'auth-glass-card',
    'btn-google', 'auth-divider', 'auth_banner.png',
    'google_callback', 'login_process',
    'googleSimModalLogin',
];

function checkPage($url, $checks) {
    $ctx = stream_context_create(['http' => ['timeout' => 8, 'ignore_errors' => true]]);
    $body = @file_get_contents($url, false, $ctx);
    if ($body === false) {
        echo "  ERROR: Could not reach $url\n";
        return;
    }
    $http_status = $http_response_header[0] ?? 'unknown';
    echo "  HTTP: $http_status\n";
    foreach ($checks as $key) {
        $found = strpos($body, $key) !== false;
        echo "  " . ($found ? "[OK]    " : "[MISS]  ") . $key . "\n";
    }
}

echo "\n=== register.php ===\n";
checkPage('http://localhost:3000/views/register.php', $checks_register);

echo "\n=== login.php ===\n";
checkPage('http://localhost:3000/views/login.php', $checks_login);

echo "\n=== google_callback.php (GET, no code — expect redirect header) ===\n";
$ctx2 = stream_context_create(['http' => ['timeout' => 5, 'ignore_errors' => true, 'follow_location' => false]]);
@file_get_contents('http://localhost:3000/google_callback.php', false, $ctx2);
$loc = '';
foreach (($http_response_header ?? []) as $h) {
    if (stripos($h, 'Location:') === 0) $loc = $h;
}
echo "  Redirect header: " . ($loc ?: 'none') . "\n";

echo "\nDone.\n";
