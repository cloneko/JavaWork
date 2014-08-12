<?php
require_once('ProductManager.php');

$pdct = new ProductManager('Product.csv');

if(!empty($_POST)){
	if(isset($_POST['append'])){
		try{
			$pdct->AddProduct([
				'ProductId' => $_POST['ProductId'],
				'Price' => $_POST['Price'],
				'Name' => $_POST['Name'],
				'Stock' => $_POST['Stock'] 
			]);
			$pdct->Commit();
			$pdct->Reload();
		} catch(Exception $e) {
			printf('既にその商品ID(%s)は存在してます。',$_POST['ProductId']);
		}
	} elseif(isset($_POST['update'])){
		try{
			$pdct->updateProduct([
				'ProductId' => $_POST['ProductId'],
				'Price' => $_POST['Price'],
				'Name' => $_POST['Name'],
				'Stock' => $_POST['Stock'] 
			]);
			$pdct->Commit();
			$pdct->Reload();
		} catch(Exception $e) {
			printf('その商品ID(%s)は多分存在してません。',$_POST['ProductId']);
		}

	} 
}elseif(isset($_GET['id'])){

		try{
			$p = $pdct->getProduct($_GET['id']);
			if($p){
			print <<<END
				<form action="" method="POST">
		<input type="hidden" name="update">
		<label>商品ID</label><input type="text" name="ProductId" value="{$p['ProductId']}" readonly="readonly">
		<label>商品名</label><input type="text" name="Name" value="{$p['Name']}">
		<label>価格</label><input type="text" name="Price" value="{$p['Price']}">
		<label>在庫</label><input type="text" name="Stock" value="{$p['Stock']}">
		<input type="submit" value="更新!!">
		</form>
END;
			} else {
				throw new ErrorException('Invalid ProductId');
			}
		} catch(Excecption $e){ 
		}

	}

?>

<table>
<?
printf("<tr><th></th><th>%s<th><th>%s</th><th>%s</th></tr>",'Name','Price','Stock');

foreach ($pdct->getProducts() as $key => $value){
	printf("<tr><td><a href=\"?id=%s\">%s</a></td><td>%s<td><td>%s</td><td>%s</td></tr>",$value['ProductId'],$value['ProductId'],$value['Name'],$value['Price'],$value['Stock']);
} 

?>
</table>

<h2>新商品</h2>
<form action="" method="POST">
<input type="hidden" name="append">
<label>商品ID</label><input type="text" name="ProductId" >
<label>商品名</label><input type="text" name="Name" >
<label>価格</label><input type="text" name="Price" >
<label>在庫</label><input type="text" name="Stock" > 
<input type="submit" value="追加!!">
</form>
