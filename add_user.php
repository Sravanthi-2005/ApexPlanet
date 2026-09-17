<?php
require_once "functions.php";
requireAdmin();

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    verifyCsrf();

    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $roleId = (int)($_POST["role_id"] ?? 2);

    if (!in_array($roleId, [1, 2], true)) {
        $roleId = 2;
    }

    if ($name === "" || mb_strlen($name) > 100) {
        $error = "Enter a valid name.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Enter a valid email.";
    } elseif (strlen($password) < 6) {
        $error = "Password must contain at least 6 characters.";
    } else {
        $check = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        $check->bind_param("s", $email);
        $check->execute();
        $exists = $check->get_result()->num_rows > 0;
        $check->close();

        if ($exists) {
            $error = "Email already exists.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare(
                "INSERT INTO users (role_id, name, email, password_hash)
                 VALUES (?, ?, ?, ?)"
            );
            $stmt->bind_param("isss", $roleId, $name, $email, $hash);

            if ($stmt->execute()) {
                $stmt->close();
                flash("success", "User created successfully.");
                redirect("dashboard.php");
            }

            $error = "Could not create user.";
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header class="navbar">
    <div class="brand">User Management System</div>
    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="profile.php">My Profile</a>
        <a href="add_user.php">Add User</a>
        <a class="logout" href="logout.php">Logout</a>
    </nav>
</header>

<main class="container narrow">
<div class="card">
    <h1>Add User</h1>
    <p class="muted">Admin-only page.</p>

    <?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>

    <form method="post">
        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">

        <label>Name</label>
        <input type="text" name="name" required maxlength="100">

        <label>Email</label>
        <input type="email" name="email" required maxlength="150">

        <label>Password</label>
        <input type="password" name="password" required minlength="6">

        <label>Role</label>
        <select name="role_id">
            <option value="2">User</option>
            <option value="1">Admin</option>
        </select>

        <button class="btn primary" type="submit">Create User</button>
        <a class="btn secondary" href="dashboard.php">Cancel</a>
    </form>
</div>
</main>
</body>
</html>
