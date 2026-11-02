
<?php
session_start();

// Clear all session variables
$_SESSION = [];

// Remove the session cookie
if (ini_get('session.use_cookies')) {
    $cookie = session_get_cookie_params();

    setcookie(
        session_name(),
        '',
        time() - 3600,
        $cookie['path'],
        $cookie['domain'],
        $cookie['secure'],
        $cookie['httponly']
    );
}

// Destroy the session
session_destroy();

// Return to the homepage
header('Location: index.php');
exit;
?>
