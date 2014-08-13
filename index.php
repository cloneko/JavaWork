<?php
error_reporting(E_ALL);
require_once('lib/ProductManager.php'); 
$pdct = new ProductManager('lib/Product.csv');

require_once('lib/Smarty/libs/Smarty.class.php'); 

$smarty = new Smarty();

$obj = array();


if(!empty($_POST)){

	if(isset($_POST['delete'])){
		try{
			$buf = $pdct->getProduct($_POST['ProductId']);
			$pdct->DeleteProduct($_POST['ProductId']);
			$pdct->Commit();
			$pdct->Reload();
			$obj['msg'] = sprintf('商品ID: %sの商品(%s)を削除しました。',$buf['ProductId'],$buf['Name']);

		} catch(Exception $e) {
			$obj['msg'] = sprintf('その商品ID(%s)は存在してないです…。',$_POST['ProductId']);
		}

	}elseif(isset($_POST['append'])){
		try{
			$pdct->AddProduct([
				'ProductId' => $_POST['ProductId'],
				'Price' => $_POST['Price'],
				'Name' => $_POST['Name'],
				'Stock' => $_POST['Stock'] 
			]);
			$pdct->Commit();
			$obj['msg'] = sprintf('商品ID: %sの商品(%s)を追加しました',$_POST['ProductId'],$_POST['Name']);
		} catch(Exception $e) {
			$obj['msg'] = sprintf('既にその商品ID(%s)は存在してます。',$_POST['ProductId']);
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
			$obj['msg'] = sprintf('商品ID: %sの商品(%s)を更新しました',$_POST['ProductId'],$_POST['Name']);
		} catch(Exception $e) {
			$obj['msg'] = sprintf('その商品ID(%s)は多分存在してません。',$_POST['ProductId']);
		}

	} 
}elseif(isset($_GET['id'])){

		try{
			$p = $pdct->getProduct($_GET['id']);
			if($p){
				$obj['current'] = $p;
			} else {
				throw new ErrorException('Invalid ProductId');
			}
		} catch(Excecption $e){ 
		}

	}


	$pdct->reload();
	$obj['products'] = $pdct->getProducts();

	$smarty->assign($obj);
	$smarty->display('template/index.tpl');

?>
