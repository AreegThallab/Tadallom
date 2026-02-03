<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * Generate base URL that works on localhost and Railway
 */
function base_url(string $path = ''): string {
    $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');

    // إذا كان الموقع في الجذر
    if ($base === '/' || $base === '\\') {
        $base = '';
    }

    return $base . '/' . ltrim($path, '/');
}
