<?php
require "../config.php";require "../functions.php";requireAdmin();
$error="";
if($_SERVER["REQUEST_METHOD"]==="POST"){
$title=trim($_POST["title"]??"");$author=trim($_POST["author"]??"");$cat=(int)($_POST["category_id"]??0);$price=(float)($_POST["price"]??0);$stock=(int)($_POST["stock"]??0);$desc=trim($_POST["description"]??"");
$image=saveUploadedImage($_FILES["image"]??null);
if($image===false)$error="Invalid image or image is larger than 2 MB.";
elseif($title===""||$author===""||$cat<=0||$price<0||$stock<0)$error="Fill all required fields.";
else{$stmt=$conn->prepare("INSERT INTO books(category_id,title,author,price,description,stock,image) VALUES(?,?,?,?,?,?,?)");$stmt->bind_param("issdsis",$cat,$title,$author,$price,$desc,$stock,$image);$stmt->execute();flash("success","Book added.");redirect("books.php");}
}
$cats=$conn->query("SELECT * FROM categories ORDER BY name");require "../partials/header.php";
?>
<div class="form-card"><h2>Add Book</h2><?php if($error):?><div class="alert error"><?=e($error)?></div><?php endif;?>
<form method="post" enctype="multipart/form-data"><label>Title</label><input name="title" required><label>Author</label><input name="author" required><label>Category</label><select name="category_id" required><?php while($c=$cats->fetch_assoc()):?><option value="<?=$c["id"]?>"><?=e($c["name"])?></option><?php endwhile;?></select><label>Price</label><input type="number" step="0.01" name="price" required><label>Stock</label><input type="number" name="stock" min="0" required><label>Description</label><textarea name="description"></textarea><label>Book Image (max 2 MB)</label><input type="file" name="image" accept="image/*"><br><br><button class="btn success">Save Book</button></form></div>
<?php require "../partials/footer.php"; ?>