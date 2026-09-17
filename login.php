<?php
require "config.php";
require "functions.php";
if (isLoggedIn()) redirect("index.php");
$error = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $stmt = $conn->prepare("SELECT u.*, r.role_name FROM users u JOIN roles r ON r.id=u.role_id WHERE u.email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    if ($user && password_verify($password, $user["password_hash"])) {
        if (!(int)$user["email_verified"]) $error = "Please verify your email first.";
        else {
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["name"] = $user["name"];
            $_SESSION["email"] = $user["email"];
            $_SESSION["role"] = $user["role_name"];
            redirect("index.php");
        }
    } else $error = "Invalid email or password.";
}
require "partials/header.php";
?>
<div class="form-card">
<h2>Login</h2>
<?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
<form method="post">
<label>Email</label><input type="email" name="email" required>
<label>Password</label><input type="password" name="password" required>
<br><br><button class="btn">Login</button>
</form>
<p>New user? <a href="register.php" style="color:#118ab2">Create an account</a></p>
</div>
<?php require "partials/footer.php"; ?>