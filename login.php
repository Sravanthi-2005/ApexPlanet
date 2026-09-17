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

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }

    if ($password === '') {
        $errors[] = 'Please enter your password.';
    }

    if (!$errors) {

        $stmt = $pdo->prepare(
            "SELECT id, name, email, password_hash, role
             FROM users
             WHERE email = ?"
        );

        $stmt->execute([$email]);

        $user = $stmt->fetch();

        if (
            $user &&
            password_verify($password, $user['password_hash'])
        ) {

            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            redirect('dashboard.php');

        } else {

            $errors[] = 'Invalid email or password.';
        }
    }
}

$pageTitle = 'Login';

include 'partials/header.php';
?>

<div class="auth-wrapper">

    <div class="card auth-card">

        <h1>Welcome Back 👋</h1>

        <p class="muted">
            Login to your BookNest account.
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
                placeholder="Enter your password"
                required
            >

            <div class="auth-links">

                <a href="forgot_password.php">
                    Forgot Password?
                </a>

            </div>

            <br>

            <button class="btn" type="submit">
                Login
            </button>

        </form>

        <p class="auth-footer">
            Don't have an account?
            <a href="register.php">Create an account</a>
        </p>

    </div>

</div>

<?php include 'partials/footer.php'; ?>