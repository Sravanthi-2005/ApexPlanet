<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';


/*
|--------------------------------------------------------------------------
| Escape HTML
|--------------------------------------------------------------------------
*/

function e(string|int|float|null $value): string
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES,
        'UTF-8'
    );
}


/*
|--------------------------------------------------------------------------
| Redirect
|--------------------------------------------------------------------------
*/

function redirect(string $url): never
{
    header('Location: ' . $url);
    exit;
}


/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']);
}


function isAdmin(): bool
{
    return ($_SESSION['role'] ?? '') === 'admin';
}


function requireLogin(): void
{
    if (!isLoggedIn()) {

        redirect('login.php');
    }
}


function requireAdmin(): void
{
    if (!isLoggedIn() || !isAdmin()) {

        http_response_code(403);

        exit(
            '403 - Admin access required.'
        );
    }
}


/*
|--------------------------------------------------------------------------
| CSRF Protection
|--------------------------------------------------------------------------
*/

function csrfToken(): string
{
    if (empty($_SESSION['csrf'])) {

        $_SESSION['csrf'] =
            bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf'];
}


function verifyCsrf(): void
{
    if (
        !hash_equals(
            $_SESSION['csrf'] ?? '',
            $_POST['csrf'] ?? ''
        )
    ) {

        http_response_code(419);

        exit(
            'Invalid request token. '
            . 'Please refresh the page.'
        );
    }
}


/*
|--------------------------------------------------------------------------
| Flash Messages
|--------------------------------------------------------------------------
*/

function flash(
    string $type,
    string $message
): void {

    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}


function getFlash(): ?array
{
    $flash = $_SESSION['flash'] ?? null;

    unset($_SESSION['flash']);

    return $flash;
}


/*
|--------------------------------------------------------------------------
| Shopping Cart
|--------------------------------------------------------------------------
*/

function cartCount(): int
{
    return array_sum(
        $_SESSION['cart'] ?? []
    );
}


function cartTotal(PDO $pdo): float
{
    if (empty($_SESSION['cart'])) {

        return 0.0;
    }

    $ids = array_keys(
        $_SESSION['cart']
    );

    $placeholders = implode(
        ',',
        array_fill(
            0,
            count($ids),
            '?'
        )
    );

    $stmt = $pdo->prepare(
        "SELECT id, price
         FROM books
         WHERE id IN ($placeholders)"
    );

    $stmt->execute($ids);

    $total = 0.0;

    foreach ($stmt as $book) {

        $quantity =
            (int) (
                $_SESSION['cart']
                [$book['id']] ?? 0
            );

        $total +=
            (float) $book['price']
            * $quantity;
    }

    return $total;
}


/*
|--------------------------------------------------------------------------
| Form Helper
|--------------------------------------------------------------------------
*/

function old(string $key): string
{
    return e(
        $_POST[$key] ?? ''
    );
}