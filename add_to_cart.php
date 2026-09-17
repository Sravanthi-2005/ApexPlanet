<?php
require "config.php"; require "functions.php"; requireLogin();
$uid=(int)$_SESSION["user_id"];$bid=(int)($_GET["id"]??0);
$stmt=$conn->prepare("SELECT id,stock FROM books WHERE id=?");$stmt->bind_param("i",$bid);$stmt->execute();$book=$stmt->get_result()->fetch_assoc();
if(!$book){flash("error","Book not found.");redirect("books.php");}
if((int)$book["stock"]<=0){flash("error","This book is out of stock.");redirect("books.php");}
$stmt=$conn->prepare("SELECT id FROM cart WHERE user_id=? AND book_id=?");$stmt->bind_param("ii",$uid,$bid);$stmt->execute();
if($stmt->get_result()->num_rows){$stmt=$conn->prepare("UPDATE cart SET quantity=quantity+1 WHERE user_id=? AND book_id=?");$stmt->bind_param("ii",$uid,$bid);}
else{$stmt=$conn->prepare("INSERT INTO cart(user_id,book_id,quantity) VALUES(?,?,1)");$stmt->bind_param("ii",$uid,$bid);}
$stmt->execute();flash("success","Book added to cart.");redirect("cart.php");
?>