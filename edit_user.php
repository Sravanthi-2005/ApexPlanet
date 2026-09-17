<?php
require_once "functions.php";
requireAdmin();

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
if (!$id) {
    redirect("dashboard.php");
}

$stmt = $conn->prepare(
    "SELECT u.id, u.name, u.email, u.profile_picture, r.role_name, u.role_id
     FROM users u JOIN roles r ON u.role_id = r.id
     WHERE u.id = ? LIMIT 1"
);
$stmt->bind_param("i", $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    flash("error", "User not found.");
    redirect("dashboard.php");
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    verifyCsrf();

    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $roleId = (int)($_POST["role_id"] ?? 2);
    $password = $_POST["password"] ?? "";

    if (!in_array($roleId, [1, 2], true)) {
        $roleId = 2;
    }

    if ($name === "" || mb_strlen($name) > 100) {
        $error = "Enter a valid name.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Enter a valid email.";
    } elseif ($password !== "" && strlen($password) < 6) {
        $error = "New password must contain at least 6 characters.";
    } else {
        $check = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ? LIMIT 1");
        $check->bind_param("si", $email, $id);
        $check->execute();
        $exists = $check->get_result()->num_rows > 0;
        $check->close();

        if ($exists) {
            $error = "Email already belongs to another user.";
        } else {
            if ($password !== "") {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare(
                    "UPDATE users SET name = ?, email = ?, role_id = ?, password_hash = ?
                     WHERE id = ?"
                );
                $stmt->bind_param("ssisi", $name, $email, $roleId, $hash, $id);
            } else {
                $stmt = $conn->prepare(
                    "UPDATE users SET name = ?, email = ?, role_id = ?
                     WHERE id = ?"
                );
                $stmt->bind_param("ssii", $name, $email, $roleId, $id);
            }

            if ($stmt->execute()) {
                $stmt->close();

                // If admin changes their own role, refresh session role.
                if ($id === (int)$_SESSION["user_id"]) {
                    $_SESSION["name"] = $name;
                    $_SESSION["email"] = $email;
                    $_SESSION["role"] = ($roleId === 1) ? "Admin" : "User";
                }

                flash("success", "User updated successfully.");
                redirect("dashboard.php");
            }

            $error = "Could not update user.";
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
    <title>Edit User</title>
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
    <h1>Edit User #<?= e($user["id"]) ?></h1>

    <?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>

    <form method="post">
        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">

        <label>Name</label>
        <input type="text" name="name" required maxlength="100" value="<?= e($user["name"]) ?>">

        <label>Email</label>
        <input type="email" name="email" required maxlength="150" value="<?= e($user["email"]) ?>">

        <label>Role</label>
        <select name="role_id">
            <option value="2" <?= (int)$user["role_id"] === 2 ? "selected" : "" ?>>User</option>
            <option value="1" <?= (int)$user["role_id"] === 1 ? "selected" : "" ?>>Admin</option>
        </select>

        <label>New Password <span class="muted">(optional)</span></label>
        <input type="password" name="password" minlength="6">

        <button class="btn primary" type="submit">Update User</button>
        <a class="btn secondary" href="dashboard.php">Cancel</a>
    </form>
</div>
</main>
</body>
</html>
