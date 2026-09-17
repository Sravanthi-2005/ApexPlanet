<?php
require "../config.php";require "../functions.php";requireAdmin();
$result=$conn->query("SELECT u.id,u.name,u.email,r.role_name,u.email_verified,u.created_at FROM users u JOIN roles r ON r.id=u.role_id ORDER BY u.id DESC");
require "../partials/header.php";
?>
<div class="admin-nav"><a href="dashboard.php">Dashboard</a><a href="books.php">Manage Books</a><a href="users.php">Users</a><a href="orders.php">Orders</a></div>
<h1>Users</h1><div class="table-wrap"><table class="table"><tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Verified</th><th>Created</th></tr>
<?php while($u=$result->fetch_assoc()):?><tr><td><?=$u["id"]?></td><td><?=e($u["name"])?></td><td><?=e($u["email"])?></td><td><?=e($u["role_name"])?></td><td><?=$u["email_verified"]?"Yes":"No"?></td><td><?=$u["created_at"]?></td></tr><?php endwhile;?>
</table></div>
<?php require "../partials/footer.php"; ?>