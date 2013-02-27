<?php
require_once('ProductManager.php');

function main(){
	$obj = new ProductManager('Product.csv');

	top:
	print '-------------------------------------------------' . "\n";
	print '1) 一覧表示' . "\n";
	print '2) 商品追加' . "\n";
	print '3) 商品情報変更' . "\n";
	print '4) 商品削除' . "\n";
	print "\n";
	print '9) 保存する' . "\n";
	print '0) 保存する前の状態に戻す' . "\n";
	print 'r) CSVデータの再読み込み' . "\n";
	print 'x) 保存して終了' . "\n";
	print 'q) 終了' . "\n";
	print "\n";
	print 'コマンドを入力:';

	// 入力待ち…
	$input = rtrim(fgets(STDIN,1024));

	switch($input){ 
		case '1':
			view($obj);
			break;
		case '2':
			add($obj);
			break;
		case '3':
			update($obj);
			break;
		case '4':
			delete($obj);
			break;
		case '9':
			$obj->Commit();
			print "保存しました\n";
			break;
		case '0':
			$obj->Rollback();
			print "最終保存時に戻しました\n";
			break;
		case 'r':
			$obj->Reload();
			print "CSVデータを再読み込みしました\n";
			break;
		case 'x':
			$obj->Commit();
			print "終了する前に保存しました\n";
		case 'q':
			print "お疲れ様でした\n";
			exit();
			break; // 意味ないけどな
		default:
			print '意味わからんぞ' . "\n";
	}
	sleep(1);
	goto top;
}


function view($obj){

	$Labels = $obj->getLabels();
	$Products = $obj->getProducts();

	$length = array();
	// Initialize
	foreach($Labels as $label){ 
		$length[$label] = mb_strlen($label);
	}

	foreach($Products as $key => $product){
		foreach($Labels as $label){
			$length[$label] = strlen($product[$label]) > $length[$label] ? mb_strlen($product[$label]) : $length[$label];
			
		} 
	}

	foreach($Labels as $label){ 
		$length[$label] += ($length[$label] % 2 == 1 ? 2 : 3);
	}

	$maxlength = 0;

	$first = true;
	foreach($Labels as $label){
		$maxlength += $length[$label];	
		if($first){
			print str_pad($label,$length[$label],' ',STR_PAD_BOTH);
			$first = false;
		} else {
			print '|'.str_pad($label,$length[$label],' ',STR_PAD_BOTH); 
			$maxlength++;
		}
	}

	print "\n";
	while($maxlength--){
		print '-';
	}
	print "\n";
	
	// Main Output

	foreach($Products as $product){
		$first = true;
		foreach($Labels as $label){
			if($first){
				print str_pad($product[$label],$length[$label],' ',STR_PAD_RIGHT);
				$first = false;
			} else {
				print '|' .str_pad($product[$label],$length[$label],' ',STR_PAD_RIGHT);
			}
		}
	print "\n";
	}
}
	

function add($obj){ 
	$Labels = $obj->getLabels();
	$Products = $obj->getProducts();
	print ($Labels[0] .'を入力してください:');
	$key = rtrim(fgets(STDIN,1024));

	if(array_key_exists($key,$Products)){
		print 'その'.$Labels[0].'は存在しています'."\n";
	} else {
		$data = array();
		foreach($Labels as $label){
			if($label === $Labels[0]){
				$data[$label] = $key;
				continue;
			} else {
				print ($label .'を入力してください:');
				$data[$label] = rtrim(fgets(STDIN,1024)); 
			}
		}
		if($obj->addProduct($data)){
			print '商品を追加しました'."\n";
		}
	}

}

function update($obj){ 
	$Labels = $obj->getLabels();
	$Products = $obj->getProducts();
	print ('更新したい商品の'.$Labels[0] .'を入力してください:');
	$key = rtrim(fgets(STDIN,1024));

	if(!array_key_exists($key,$obj->getProducts())){
		print 'その'.$Labels[0].'は存在していません'."\n";
	} else {
		print '更新したい項目のみ入力してください。現在の項目はかっこの中に表示されています'."\n";
		$data = array();
		foreach($Labels as $label){
			if($label === $Labels[0]){
				$data[$label] = $key;
				continue;
			} else {
				print ($label .'を入力してください(現在の値:'.$Products[$key][$label].'):');
				$input = rtrim(fgets(STDIN,1024));
				$data[$label] = $input === '' ? $Products[$key][$label] : $input; 
			}
		}
		if($obj->UpdateProduct($data)){
			print '商品を更新しました'."\n";
		}
	}

}

function delete($obj){ 
	$Labels = $obj->getLabels();
	$Products = $obj->getProducts();

	print ('削除したい商品の'.$Labels[0] .'を入力してください:');
	$key = rtrim(fgets(STDIN,1024));

	if(!array_key_exists($key,$Products)){
		print 'その'.$Labels[0].'は存在していません'."\n";
	} else {
		if($obj->DeleteProduct($key)){
			print '商品を削除しました'."\n";
		}
	}

}

?>
