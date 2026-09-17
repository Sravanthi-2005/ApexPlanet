<?php
require_once "functions.php";
requireLogin();

$userId = (int)$_SESSION["user_id"];
$error = "";

$stmt = $conn->prepare(
    "SELECT u.id, u.name, u.email, u.password_hash, u.profile_picture, r.role_name
     FROM users u
     JOIN roles r ON u.role_id = r.id
     WHERE u.id = ? LIMIT 1"
);
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    session_destroy();
    redirect("login.php");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    verifyCsrf();

    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($name === "" || mb_strlen($name) > 100) {
        $error = "Enter a valid name.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Enter a valid email.";
    } elseif ($password !== "" && strlen($password) < 6) {
        $error = "New password must contain at least 6 characters.";
    } else {
        $check = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ? LIMIT 1");
        $check->bind_param("si", $email, $userId);
        $check->execute();
        $exists = $check->get_result()->num_rows > 0;
        $check->close();

        if ($exists) {
            $error = "That email is already used by another account.";
        } else {
            $upload = uploadProfilePicture($_FILES["profile_picture"] ?? [], $user["profile_picture"]);

            if (!$upload["success"]) {
                $error = $upload["error"];
            } else {
                $newPicture = $upload["filename"];

                if ($password !== "") {
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $conn->prepare(
                        "UPDATE users SET name = ?, email = ?, password_hash = ?, profile_picture = ?
                         WHERE id = ?"
                    );
                    $stmt->bind_param("ssssi", $name, $email, $hash, $newPicture, $userId);
                } else {
                    $stmt = $conn->prepare(
                        "UPDATE users SET name = ?, email = ?, profile_picture = ?
                         WHERE id = ?"
                    );
                    $stmt->bind_param("sssi", $name, $email, $newPicture, $userId);
                }

                if ($stmt->execute()) {
                    $stmt->close();
                    $_SESSION["name"] = $name;
                    $_SESSION["email"] = $email;
                    flash("success", "Profile updated successfully.");
                    redirect("profile.php");
                } else {
                    $error = "Could not update profile.";
                    $stmt->close();
                }
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
    <title>My Profile</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header class="navbar">
    <div class="brand">User Management System</div>
    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="profile.php">My Profile</a>
        <?php if (isAdmin()): ?><a href="add_user.php">Add User</a><?php endif; ?>
        <a class="logout" href="logout.php">Logout</a>
    </nav>
</header>

<main class="container narrow">
    <div class="card">
        <h1>My Profile</h1>
        <p class="muted">Update your personal information and profile picture.</p>

        <?php showFlash(); ?>
        <?php if ($error): ?>
            <div class="alert error"><?= e($error) ?></div>
        <?php endif; ?>

        <div class="profile-photo">
            <?php if ($user["profile_picture"]): ?>
                <img class="avatar large" src="uploads/<?= e($user["profile_picture"]) ?>" alt="Profile picture">
            <?php else: ?>
                <div class="avatar-placeholder large">No Photo</div>
            <?php endif; ?>
        </div>

        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">

            <label>Name</label>
            <input type="text" name="name" maxlength="100" required value="<?= e($user["name"]) ?>">

            <label>Email</label>
            <input type="email" name="email" maxlength="150" required value="<?= e($user["email"]) ?>">

            <label>Role</label>
            <input type="text" value="<?= e($user["role_name"]) ?>" disabled>

            <label>New Password <span class="muted">(leave blank to keep current password)</span></label>
            <input type="password" name="password" minlength="6">

            <label>Profile Picture <span class="muted">(JPG/PNG/GIF, maximum 2 MB)</span></label>
            <input type="file" name="profile_picture" accept=".jpg,.jpeg,.png,.gif,image/jpeg,image/png,image/gif">

            <button class="btn primary" type="submit">Save Changes</button>
        </form>
    </div>
</main>
</body>
</html>
