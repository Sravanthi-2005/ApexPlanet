<?php
require_once "functions.php";
requireLogin();

$users = [];

if (isAdmin()) {
    $result = $conn->query(
        "SELECT u.id, u.name, u.email, u.profile_picture, u.created_at, r.role_name
         FROM users u
         JOIN roles r ON u.role_id = r.id
         ORDER BY u.id DESC"
    );

    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header class="navbar">
    <div class="brand">User Management System</div>
    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="profile.php">My Profile</a>
        <?php if (isAdmin()): ?>
            <a href="add_user.php">Add User</a>
        <?php endif; ?>
        <a class="logout" href="logout.php">Logout</a>
    </nav>
</header>

<main class="container">
    <?php showFlash(); ?>

    <section class="welcome">
        <h1>Welcome, <?= e($_SESSION["name"]) ?>! 👋</h1>
        <p>You are logged in as <strong><?= e($_SESSION["role"]) ?></strong>. Manage your account from the options below.</p>
    </section>

    <?php if (isAdmin()): ?>
        <?php
            $totalUsers = count($users);
            $adminCount = 0;
            $userCount = 0;
            foreach ($users as $statUser) {
                if ($statUser["role_name"] === "Admin") {
                    $adminCount++;
                } else {
                    $userCount++;
                }
            }
        ?>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">👥</div>
                <span class="stat-number"><?= $totalUsers ?></span>
                <span class="stat-label">Total Users</span>
            </div>
            <div class="stat-card">
                <div class="stat-icon">🛡️</div>
                <span class="stat-number"><?= $adminCount ?></span>
                <span class="stat-label">Administrators</span>
            </div>
            <div class="stat-card">
                <div class="stat-icon">✓</div>
                <span class="stat-number"><?= $userCount ?></span>
                <span class="stat-label">Normal Users</span>
            </div>
        </div>
        <div class="card">
            <div class="section-heading">
                <div>
                    <h2>All Users</h2>
                    <p>Admin can create, view, edit and delete users.</p>
                </div>
                <a class="btn primary" href="add_user.php">+ Add User</a>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Photo</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= e($user["id"]) ?></td>
                            <td>
                                <?php if ($user["profile_picture"]): ?>
                                    <img class="avatar small"
                                         src="uploads/<?= e($user["profile_picture"]) ?>"
                                         alt="Profile">
                                <?php else: ?>
                                    <div class="avatar-placeholder small">N/A</div>
                                <?php endif; ?>
                            </td>
                            <td><?= e($user["name"]) ?></td>
                            <td><?= e($user["email"]) ?></td>
                            <td><span class="badge"><?= e($user["role_name"]) ?></span></td>
                            <td><?= e($user["created_at"]) ?></td>
                            <td class="actions">
                                <a class="btn small-btn" href="edit_user.php?id=<?= e($user["id"]) ?>">Edit</a>
                                <?php if ((int)$user["id"] !== (int)$_SESSION["user_id"]): ?>
                                    <form method="post" action="delete_user.php" class="inline-form"
                                          onsubmit="return confirm('Are you sure you want to delete this user?');">
                                        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                                        <input type="hidden" name="id" value="<?= e($user["id"]) ?>">
                                        <button class="btn danger small-btn" type="submit">Delete</button>
                                    </form>
                                <?php else: ?>
                                    <span class="muted">Your account</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (!$users): ?>
                        <tr><td colspan="7" class="center">No users found.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php else: ?>
        <div class="grid two">
            <div class="card">
                <h2>User Dashboard</h2>
                <p>You are logged in as a normal user.</p>
                <a class="btn primary" href="profile.php">Manage My Profile</a>
            </div>
            <div class="card">
                <h2>Available Features</h2>
                <ul class="feature-list">
                    <li>Login and logout</li>
                    <li>View and update your profile</li>
                    <li>Upload a profile picture</li>
                    <li>Change your password</li>
                </ul>
            </div>
        </div>
    <?php endif; ?>
</main>
</body>
</html>
