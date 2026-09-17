<?php

declare(strict_types=1);

session_start();

/*
|--------------------------------------------------------------------------
| Database Configuration
|--------------------------------------------------------------------------
*/

const DB_HOST = 'localhost';
const DB_NAME = 'online_bookstore';
const DB_USER = 'root';
const DB_PASS = '';

/*
|--------------------------------------------------------------------------
| Database Connection
|--------------------------------------------------------------------------
*/

try {

    $pdo = new PDO(
        'mysql:host=' . DB_HOST .
        ';dbname=' . DB_NAME .
        ';charset=utf8mb4',

        DB_USER,
        DB_PASS,

        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,

            PDO::ATTR_DEFAULT_FETCH_MODE =>
                PDO::FETCH_ASSOC,

            PDO::ATTR_EMULATE_PREPARES =>
                false
        ]
    );

} catch (PDOException $e) {

    die(
        'Database connection failed. '
        . 'Please check XAMPP MySQL and config.php.'
    );
}