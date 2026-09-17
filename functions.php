<?php
require_once __DIR__ . "/config.php";

function e($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}

function redirect(string $url): never {
    header("Location: " . $url);
    exit;
}

function isLoggedIn(): bool {
    return isset($_SESSION["user_id"]);
}

function requireLogin(): void {
    if (!isLoggedIn()) {
        redirect("login.php");
    }
}

function isAdmin(): bool {
    return isset($_SESSION["role"]) && $_SESSION["role"] === "Admin";
}

function requireAdmin(): void {
    requireLogin();
    if (!isAdmin()) {
        http_response_code(403);
        die("403 Forbidden: Admin access required.");
    }
}

function csrfToken(): string {
    if (empty($_SESSION["csrf_token"])) {
        $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
    }
    return $_SESSION["csrf_token"];
}

function verifyCsrf(): void {
    $token = $_POST["csrf_token"] ?? "";
    if (!hash_equals($_SESSION["csrf_token"] ?? "", $token)) {
        http_response_code(419);
        die("Invalid security token. Please go back and try again.");
    }
}

function flash(string $type, string $message): void {
    $_SESSION["flash"] = ["type" => $type, "message" => $message];
}

function showFlash(): void {
    if (!empty($_SESSION["flash"])) {
        $f = $_SESSION["flash"];
        echo '<div class="alert ' . e($f["type"]) . '">' . e($f["message"]) . '</div>';
        unset($_SESSION["flash"]);
    }
}

function uploadProfilePicture(array $file, ?string $oldFile = null): array {
    if (!isset($file["error"]) || $file["error"] === UPLOAD_ERR_NO_FILE) {
        return ["success" => true, "filename" => $oldFile];
    }

    if ($file["error"] !== UPLOAD_ERR_OK) {
        return ["success" => false, "error" => "Profile picture upload failed."];
    }

    $maxSize = 2 * 1024 * 1024; // 2 MB
    if ($file["size"] > $maxSize) {
        return ["success" => false, "error" => "Profile picture must be 2 MB or smaller."];
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file["tmp_name"]);

    $allowed = [
        "image/jpeg" => "jpg",
        "image/png"  => "png",
        "image/gif"  => "gif"
    ];

    if (!isset($allowed[$mime])) {
        return ["success" => false, "error" => "Only JPG, PNG and GIF images are allowed."];
    }

    $uploadDir = __DIR__ . DIRECTORY_SEPARATOR . "uploads";
    if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
        return ["success" => false, "error" => "Could not create uploads directory."];
    }

    $filename = bin2hex(random_bytes(16)) . "." . $allowed[$mime];
    $destination = $uploadDir . DIRECTORY_SEPARATOR . $filename;

    if (!move_uploaded_file($file["tmp_name"], $destination)) {
        return ["success" => false, "error" => "Could not save the profile picture."];
    }

    if ($oldFile) {
        $oldPath = $uploadDir . DIRECTORY_SEPARATOR . basename($oldFile);
        if (is_file($oldPath)) {
            @unlink($oldPath);
        }
    }

    return ["success" => true, "filename" => $filename];
}

function deleteProfilePicture(?string $filename): void {
    if (!$filename) return;
    $path = __DIR__ . DIRECTORY_SEPARATOR . "uploads" . DIRECTORY_SEPARATOR . basename($filename);
    if (is_file($path)) {
        @unlink($path);
    }
}
?>
