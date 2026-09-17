<?php
require "partials/header.php";
$id=(int)($_GET["id"]??0);
$stmt=$conn->prepare("SELECT b.*, c.name category_name FROM books b LEFT JOIN categories c ON c.id=b.category_id WHERE b.id=?");
$stmt->bind_param("i",$id);$stmt->execute();$book=$stmt->get_result()->fetch_assoc();
if(!$book){echo '<div class="alert error">Book not found.</div>';require "partials/footer.php";exit;}
?>
<div class="card">
<div class="two-col">
<div><img src="uploads/<?=e($book["image"]?: "default-book.svg")?>" style="width:100%;max-height:500px;object-fit:contain;border-radius:12px"></div>
<div>
<span class="badge"><?=e($book["category_name"])?></span>
<h1><?=e($book["title"])?></h1>
<h3>Author: <?=e($book["author"])?></h3>
<p class="price">₹<?=number_format($book["price"],2)?></p>
<p><?=nl2br(e($book["description"]))?></p>
<p><strong>Stock:</strong> <?=e($book["stock"])?></p>
<?php if(isLoggedIn()): ?><a class="btn success" href="add_to_cart.php?id=<?=$id?>">Add to Cart</a><?php else: ?><a class="btn" href="login.php">Login to Buy</a><?php endif;?>
</div></div></div>
<?php require "partials/footer.php"; ?>