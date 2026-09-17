<?php

require_once __DIR__ . '/../functions.php';

$flash = getFlash();

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        <?= e($pageTitle ?? 'BookNest') ?>
        | BookNest
    </title>

    <link
        rel="stylesheet"
        href="assets/style.css"
    >

</head>

<body>

<header class="topbar">

    <div class="container nav">

        <a
            class="brand"
            href="index.php"
        >
            📚 BookNest
        </a>

        <nav>

            <a href="index.php">
                Home
            </a>

            <a href="books.php">
                Books
            </a>

            <?php if (isLoggedIn()): ?>

                <a href="dashboard.php">
                    Dashboard
                </a>

                <a href="orders.php">
                    My Orders
                </a>

                <a href="profile.php">
                    Profile
                </a>

                <?php if (isAdmin()): ?>

                    <a href="admin/index.php">
                        Admin Panel
                    </a>

                <?php endif; ?>

                <a href="logout.php">
                    Logout
                </a>

            <?php else: ?>

                <a href="login.php">
                    Login
                </a>

                <a
                    class="btn small"
                    href="register.php"
                >
                    Register
                </a>

            <?php endif; ?>

            <a
                class="cart"
                href="cart.php"
            >
                🛒 Cart

                <span>
                    <?= cartCount() ?>
                </span>

            </a>

        </nav>

    </div>

</header>


<main class="container">

<?php if ($flash): ?>

    <div
        class="alert <?= e($flash['type']) ?>"
    >

        <?= e($flash['message']) ?>

    </div>

<?php endif; ?>