<?php
require "../config.php";require "../functions.php";requireAdmin();
$users=$conn->query("SELECT COUNT(*) c FROM users")->fetch_assoc()["c"];
$books=$conn->query("SELECT COUNT(*) c FROM books")->fetch_assoc()["c"];
$orders=$conn->query("SELECT COUNT(*) c FROM orders")->fetch_assoc()["c"];
$sales=$conn->query("SELECT COALESCE(SUM(total_amount),0) c FROM orders WHERE status<>'Cancelled'")->fetch_assoc()["c"];
$monthly=$conn->query("SELECT DATE_FORMAT(created_at,'%b %Y') label, SUM(total_amount) total FROM orders WHERE status<>'Cancelled' GROUP BY YEAR(created_at),MONTH(created_at) ORDER BY YEAR(created_at),MONTH(created_at)");
$labels=[];$values=[];while($m=$monthly->fetch_assoc()){ $labels[]=$m["label"]; $values[]=(float)$m["total"]; }
require "../partials/header.php";
?>
<div class="admin-nav"><a href="dashboard.php">Dashboard</a><a href="books.php">Manage Books</a><a href="users.php">Users</a><a href="orders.php">Orders</a><a href="../index.php">Store</a></div>
<h1>Admin Dashboard</h1>
<div class="stats"><div class="stat"><h3>Users</h3><p><?=$users?></p></div><div class="stat"><h3>Books</h3><p><?=$books?></p></div><div class="stat"><h3>Orders</h3><p><?=$orders?></p></div><div class="stat"><h3>Sales</h3><p>₹<?=number_format($sales,2)?></p></div></div>
<div class="card"><h2>Sales Analytics</h2><canvas id="salesChart"></canvas></div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('salesChart'),{type:'bar',data:{labels:<?=json_encode($labels)?>,datasets:[{label:'Sales (₹)',data:<?=json_encode($values)?>}]},options:{responsive:true}});
</script>
<?php require "../partials/footer.php"; ?>