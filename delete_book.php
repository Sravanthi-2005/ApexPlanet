<?php
require "../config.php";require "../functions.php";requireAdmin();
$id=(int)($_GET["id"]??0);$stmt=$conn->prepare("SELECT image FROM books WHERE id=?");$stmt->bind_param("i",$id);$stmt->execute();$book=$stmt->get_result()->fetch_assoc();
if($book){$stmt=$conn->prepare("DELETE FROM books WHERE id=?");$stmt->bind_param("i",$id);$stmt->execute();if($book["image"]&&file_exists(UPLOAD_DIR.$book["image"]))unlink(UPLOAD_DIR.$book["image"]);flash("success","Book deleted.");}
redirect("books.php");
?>