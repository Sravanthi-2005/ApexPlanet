<?php
require_once "functions.php";

if (isLoggedIn()) {
    redirect("dashboard.php");
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    verifyCsrf();

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === "") {
        $error = "Enter a valid email and password.";
    } else {
        $stmt = $conn->prepare(
            "SELECT u.id, u.name, u.email, u.password_hash, r.role_name
             FROM users u
             JOIN roles r ON u.role_id = r.id
             WHERE u.email = ? LIMIT 1"
        );
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if ($user && password_verify($password, $user["password_hash"])) {
            session_regenerate_id(true);

            $_SESSION["user_id"] = (int)$user["id"];
            $_SESSION["name"] = $user["name"];
            $_SESSION["email"] = $user["email"];
            $_SESSION["role"] = $user["role_name"];

            redirect("dashboard.php");
        } else {
            $error = "Invalid email or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - User Management System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth-page">
<div class="auth-card">
    <h1>User Management System</h1>
    <p class="subtitle">Login to continue</p>

    <?php showFlash(); ?>
    <?php if ($error): ?>
        <div class="alert error"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="post">
        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">

        <label>Email</label>
        <input type="email" name="email" required maxlength="150"
               value="<?= e($_POST["email"] ?? "") ?>">

        <label>Password</label>
        <input type="password" name="password" required>

        <button class="btn primary full" type="submit">Login</button>
    </form>

    <p class="center">New user? <a href="register.php">Register here</a></p>
</div>
</body>
</html>
