<?php
require_once "functions.php";

if (isLoggedIn()) {
    redirect("dashboard.php");
}

redirect("login.php");
?>
