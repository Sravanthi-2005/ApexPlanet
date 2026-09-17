<?php
require_once "functions.php";
requireAdmin();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    redirect("dashboard.php");
}

verifyCsrf();

$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

if (!$id) {
    flash("error", "Invalid user ID.");
    redirect("dashboard.php");
}

if ($id === (int)$_SESSION["user_id"]) {
    flash("error", "You cannot delete your own account.");
    redirect("dashboard.php");
}

$stmt = $conn->prepare("SELECT profile_picture FROM users WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    flash("error", "User not found.");
    redirect("dashboard.php");
}

$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    deleteProfilePicture($user["profile_picture"]);
    flash("success", "User deleted successfully.");
} else {
    flash("error", "Could not delete user.");
}

$stmt->close();
redirect("dashboard.php");
?>
