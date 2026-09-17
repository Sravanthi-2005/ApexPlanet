<?php
require "../config.php";require "../functions.php";requireAdmin();
if(isset($_POST["status"],$_POST["order_id"])){
$id=(int)$_POST["order_id"];$status=$_POST["status"];
$allowed=["Pending","Processing","Shipped","Delivered","Cancelled"];
if(in_array($status,$allowed,true)){ $stmt=$conn->prepare("UPDATE orders SET status=? WHERE id=?");$stmt->bind_param("si",$status,$id);$stmt->execute(); }
}
$result=$conn->query("SELECT o.*,u.name,u.email FROM orders o JOIN users u ON u.id=o.user_id ORDER BY o.id DESC");
require "../partials/header.php";
?>
<div class="admin-nav"><a href="dashboard.php">Dashboard</a><a href="books.php">Manage Books</a><a href="users.php">Users</a><a href="orders.php">Orders</a></div>
<h1>Orders</h1><div class="table-wrap"><table class="table"><tr><th>ID</th><th>User</th><th>Total</th><th>Status</th><th>Address</th><th>Date</th><th>Update</th></tr>
<?php while($o=$result->fetch_assoc()):?><tr><td>#<?=$o["id"]?></td><td><?=e($o["name"])?><br><small><?=e($o["email"])?></small></td><td>₹<?=number_format($o["total_amount"],2)?></td><td><?=e($o["status"])?></td><td><?=e($o["address"])?></td><td><?=$o["created_at"]?></td><td><form method="post"><input type="hidden" name="order_id" value="<?=$o["id"]?>"><select name="status"><?php foreach(["Pending","Processing","Shipped","Delivered","Cancelled"] as $s):?><option <?=$s===$o["status"]?"selected":""?>><?=$s?></option><?php endforeach;?></select><button class="btn">Save</button></form></td></tr><?php endwhile;?>
</table></div>
<?php require "../partials/footer.php"; ?>