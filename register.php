<?php
require_once "functions.php";

if (isLoggedIn()) {
    redirect("dashboard.php");
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    verifyCsrf();

    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm = $_POST["confirm_password"] ?? "";

    if ($name === "" || mb_strlen($name) > 100) {
        $error = "Enter a valid name.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Enter a valid email address.";
    } elseif (strlen($password) < 6) {
        $error = "Password must contain at least 6 characters.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        $check = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        $check->bind_param("s", $email);
        $check->execute();
        $exists = $check->get_result()->num_rows > 0;
        $check->close();

        if ($exists) {
            $error = "Email is already registered.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $roleId = 2;

            $stmt = $conn->prepare(
                "INSERT INTO users (role_id, name, email, password_hash)
                 VALUES (?, ?, ?, ?)"
            );
            $stmt->bind_param("isss", $roleId, $name, $email, $hash);

            if ($stmt->execute()) {
                $stmt->close();
                flash("success", "Registration successful. Please login.");
                redirect("login.php");
            } else {
                $error = "Registration failed.";
                $stmt->close();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - User Management System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth-page">
<div class="auth-card">
    <h1>Create Account</h1>
    <p class="subtitle">Register as a normal User</p>

    <?php if ($error): ?>
        <div class="alert error"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="post">
        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">

        <label>Full Name</label>
        <input type="text" name="name" required maxlength="100"
               value="<?= e($_POST["name"] ?? "") ?>">

        <label>Email</label>
        <input type="email" name="email" required maxlength="150"
               value="<?= e($_POST["email"] ?? "") ?>">

        <label>Password</label>
        <input type="password" name="password" required minlength="6">

        <label>Confirm Password</label>
        <input type="password" name="confirm_password" required minlength="6">

        <button class="btn primary full" type="submit">Register</button>
    </form>

    <p class="center">Already have an account? <a href="login.php">Login</a></p>
</div>
</body>
</html>
