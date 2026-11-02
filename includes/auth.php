<?php
declare(strict_types=1);

function is_logged_in(): bool
{
    return isset($_SESSION['user_id']) && is_int($_SESSION['user_id']);
}

function current_user_name(): ?string
{
    return is_logged_in() ? (string) ($_SESSION['user_name'] ?? '') : null;
}

function require_login(): void
{
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}
