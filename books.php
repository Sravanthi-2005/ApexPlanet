<?php
require "../config.php";require "../functions.php";requireAdmin();
$result=$conn->query("SELECT b.*,c.name category_name FROM books b LEFT JOIN categories c ON c.id=b.category_id ORDER BY b.id DESC");
require "../partials/header.php";
?>
<div class="admin-nav"><a href="dashboard.php">Dashboard</a><a href="books.php">Manage Books</a><a href="users.php">Users</a><a href="orders.php">Orders</a></div>
<h1>Manage Books</h1><a class="btn success" href="add_book.php">+ Add Book</a><br><br>
<div class="table-wrap"><table class="table"><tr><th>ID</th><th>Title</th><th>Author</th><th>Category</th><th>Price</th><th>Stock</th><th>Actions</th></tr>
<?php while($b=$result->fetch_assoc()):?><tr><td><?=$b["id"]?></td><td><?=e($b["title"])?></td><td><?=e($b["author"])?></td><td><?=e($b["category_name"])?></td><td>₹<?=number_format($b["price"],2)?></td><td><?=$b["stock"]?></td><td><a class="btn" href="edit_book.php?id=<?=$b["id"]?>">Edit</a> <a class="btn secondary" onclick="return confirm('Delete this book?')" href="delete_book.php?id=<?=$b["id"]?>">Delete</a></td></tr><?php endwhile;?>
</table></div>
<?php require "../partials/footer.php"; ?>