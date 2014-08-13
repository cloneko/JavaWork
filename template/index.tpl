<!html>
<html>
<head>
<title>Prodcts Management</title>
<link rel="stylesheet" type="text/css" href="static/main.css" >
</head>
<body> 
<h1>Products Management System</h1>
{if !empty($msg)}
<script>
alert("{$msg}");
location.replace('index.php'); 
</script>
{/if}
{if !empty($current) }
<div id="updateProduct">
<h2>変更用フォーム</h2>
<form action="" method="POST">
<input type="hidden" name="update">
<label>商品ID</label><input type="text" name="ProductId" value="{$current['ProductId']}" readonly="readonly">
<label>商品名</label><input type="text" name="Name" value="{$current['Name']}">
<label>価格</label><input type="text" name="Price" value="{$current['Price']}">
<label>在庫</label><input type="text" name="Stock" value="{$current['Stock']}">
<input type="submit" value="更新!!">
</form>
<form action="" method="POST">
<input type="hidden" name="delete">
<input type="hidden" name="ProductId" value="{$current['ProductId']}">
<input type="submit" value="削除!!">
</form>
</div>
{/if}


<table>
<tr><th></th><th>Name<th><th>Price</th><th>Stock</th></tr>
{foreach from=$products item=p}
<tr><td><a href="?id={$p['ProductId']}">{$p['ProductId']}</a></td><td>{$p['Name']}<td><td>{$p['Price']}</td><td>{$p['Stock']}</td></tr>
{/foreach}
</table>
<hr>
<h2>新商品</h2>
<form action="" method="POST">
<input type="hidden" name="append">
<label>商品ID</label><input type="text" name="ProductId" >
<label>商品名</label><input type="text" name="Name" >
<label>価格</label><input type="text" name="Price" >
<label>在庫</label><input type="text" name="Stock" >
<input type="submit" value="追加!!">
</form>

</body>
</html>
