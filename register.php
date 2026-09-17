<?php

require_once 'functions.php';

if (isLoggedIn()) {
    redirect('dashboard.php');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!verifyCsrf($_POST['csrf'] ?? '')) {
        $errors[] = 'Invalid security token. Please try again.';
    }

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($name === '') {
        $errors[] = 'Please enter your name.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }

    if (strlen($password) < 6) {
        $errors[] = 'Password must contain at least 6 characters.';
    }

    if ($password !== $confirmPassword) {
        $errors[] = 'Passwords do not match.';
    }

    if (!$errors) {

        $stmt = $pdo->prepare(
            "SELECT id FROM users WHERE email = ?"
        );

        $stmt->execute([$email]);

        if ($stmt->fetch()) {

            $errors[] = 'An account with this email already exists.';

        } else {

            $passwordHash = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $stmt = $pdo->prepare(
                "INSERT INTO users 
                (name, email, password_hash, role)
                VALUES (?, ?, ?, 'user')"
            );

            $stmt->execute([
                $name,
                $email,
                $passwordHash
            ]);

            flash(
                'success',
                'Registration successful! Please login.'
            );

            redirect('login.php');
        }
    }
}

$pageTitle = 'Register';

include 'partials/header.php';
?>

<div class="auth-wrapper">

    <div class="card auth-card">

        <h1>Create Your Account 📚</h1>

        <p class="muted">
            Join BookNest and start exploring our collection.
        </p>

        <?php if ($errors): ?>

            <div class="alert error">

                <?php foreach ($errors as $error): ?>

                    <p><?= e($error) ?></p>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

        <form method="post">

            <input
                type="hidden"
                name="csrf"
                value="<?= e(csrfToken()) ?>"
            >

            <label>
                Full Name
            </label>

            <input
                class="form-control"
                type="text"
                name="name"
                value="<?= old('name') ?>"
                placeholder="Enter your name"
                required
            >

            <label>
                Email Address
            </label>

            <input
                class="form-control"
                type="email"
                name="email"
                value="<?= old('email') ?>"
                placeholder="Enter your email"
                required
            >

            <label>
                Password
            </label>

            <input
                class="form-control"
                type="password"
                name="password"
                placeholder="Minimum 6 characters"
                required
            >

            <label>
                Confirm Password
            </label>

            <input
                class="form-control"
                type="password"
                name="confirm_password"
                placeholder="Confirm your password"
                required
            >

            <br>

            <button class="btn" type="submit">
                Create Account
            </button>

        </form>

        <p class="auth-footer">
            Already have an account?
            <a href="login.php">Login here</a>
        </p>

    </div>

</div>

<?php include 'partials/footer.php'; ?>