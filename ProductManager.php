<?php
/**
* Product Manager Class for [sn]12's Java work
*
* @package ProductManager
* @author Yonashiro Yuu <yonashiro@std.it-college.ac.jp>
* @since 2013/2/27
* @version 1.0
*/

class ProductManager{

	// Global Variables
	protected $Products = array(); // Products Store
	protected $Labels = array(); // Labels(Products Table)
	protected $FilePath = null; // Database FilePath
	protected $Buffer = null; // Buffer(for Rollback)

/**
* Constructor  
*
* @param string $filepath Path of Database
* @return bool Maybe true
*/
	function ProductManager($filepath = null){
		if($filepath !== null){
			$this->DataLoader($filepath);
			$this->FilePath = $filepath;
		} else {
			throw new ErrorException('ProductManager Class Require filepath');
		}
		return true;

	}

/**
* Commit   
*
* @return bool Result Commit;
*/

	function Commit(){
		$this->Buffer = $this->Products;
		$this->DataWriter();
		// Reload Data ...
		$this->reload();
	}

/**
* getLabels   
*
* @return array Label;
*/

	function getLabels(){
		return $this->Labels;
	}

/**
* getProducts   
*
* @return array Products;
*/

	function getProducts(){
		return $this->Products;
	}

/**
* Reload   
*
* @return bool Result Commit;
*/

	function Reload(){
		return $this->DataLoader($this->FilePath);
	}

/**
* Rollback   
*
* @return bool Result Rollback;
*/

	function Rollback(){
		return $this->Products = $this->Buffer; 
	}

/**
* DataLoader / Load Product Data From CSV File   
*
* @access Private
* @param string File path of CSV File
* @return bool Result Load data from CSV File;
*/

	private function DataLoader($filepath){
		$fp = null;
		if(!(is_readable($filepath))){
			throw new ErrorException('Cannot Open ' .$filepath);
		}
		$fp = fopen($filepath,'rb');
		$first = true;
		$DBLabel = array();
		while($line = fgetcsv($fp,1024)){
			
			if($first){
				foreach($line as $value){
					array_push($DBLabel,$value);
				} 
				$first = false;
			} else {
				$counter = 0;
				$tmpProduct = array();
				foreach($line as $value){
					$tmpProduct[$DBLabel[$counter++]] = $value;
				} 
				
				$this->Products[$tmpProduct[$DBLabel[0]]] = $tmpProduct;
				$this->Labels = $DBLabel;
			}
		}

		fclose($fp);
		$this->Buffer = $this->Products;
		return true;
	}

/**
* AddProduct / Add Product to Product Object
*
* @param array Product Data(ProductId,Name,Price,Stock,...)
* @return bool Result add to Product Object;
*/
	function AddProduct($array = null){

		if(!is_array($array)){
			throw new ErrorException('Invalid Arugment');
		} 

		// Primary Key Check
		if(!array_key_exists($this->Labels[0],$array)){
			throw new ErrorException('Primary Key('.$this->Labels[0].') is not Exists'); 
		}
		// Duplicate Check
		if(array_key_exists($array[$this->Labels[0]],$this->Products)){
			throw new ErrorException($this->Labels[0] . '('.$array[$this->Labels[0]].') is Already Exists'); 
		}
		// Create Data
		$tmp = array();
		foreach($this->Labels as $label){
			$tmp[$label]= $array[$label];
		}
		

		$this->Products[$this->Labels[0]] = $tmp; 
		return true;
	}

/**
* DeleteProduct / Delete Product from Product Object
*
* @param string ProductId
* @return bool Result Delete Product From Product Object;
*/

	function DeleteProduct($key = null){
		if($key === null){
			throw new ErrorException('DeleteProduct Function Require key');
		} 
		if(array_key_exists($key,$this->Products)){
			unset($this->Products[$key]);
			return true;
		} else {
			throw new ErrorException('Cannot find '.$key); 
		}

	}
/**
* UpdateProduct / Update Product information
*
* @param array Product Data(ProductId,Name,Price,Stock,...)
* @return bool Result of Update;
*/

	function UpdateProduct($data){

		if(!is_array($data)){
			throw new ErrorException('Invalid Arugment');
		} 

		$key = $data[$this->Labels[0]];
		
		if($key === null){
			throw new ErrorException('DeleteProduct Function Require key');
		} 
		if(array_key_exists($key,$this->Products)){

			//Create Data
			$tmp = array();
			foreach($this->Labels as $label){ 
				$tmp[$label] = $data[$label];
			}

			$this->Products[$key] = $tmp; 
			return true;

		} else {
			throw new ErrorException('Cannot find '.$key); 
		}

	}

/**
* DataWriter / Data output to CSV File   
*
* @access Private
* @return bool Result DataWriter;
*/

	private function DataWriter(){
		// FileWrite...
		$data = null;

		$first = true;
		foreach($this->Labels as $label){
			if($first){
				$data .= $label;
				$first = false;
			} else {
				$data .= ',' . $label;
			} 
		}
		$data .= "\r\n";

		foreach($this->Buffer as $Product){ 
			$first = true;
			foreach($this->Labels as $label){
				if($first){
					$data .= $Product[$label];
					$first = false;
				} else {
					$data .= ',' . $Product[$label];
				} 
			}
			$data .= "\r\n"; 
		}

		file_put_contents($this->FilePath,$data);
	}

	function __destruct(){
	}
}
?>
