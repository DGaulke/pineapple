<?php namespace pineapple;
/*
 *
 */
include_once('Controller.php');
include_once('Persistent.php');
include_once('Category.php');

/* The Product class represents an item for sale by the bakery */
class Product extends Persistent {
	protected $productId, $name, $category, $price, $shortDescription,
			$description, $img, $quantity;

	/* Constructor hidden to force use of static factory methods */
	private function __construct() {}

	/* Create a new Product object and validate */
	static function newProduct($data) {
		$product = new self();
		if (!Product::isProductAvailable($data['name'])) {
			array_push($product->errors, "Product ".$data['name'].
					" already exists");
		}
		foreach ($data as $name => $value){
			$product->__set($name, $value);
		}
		if ($product->isValid()) {
			return $product;
		} else {
			throw new InvalidObjectException($product);
		}
	}
	/* Return an array of all Product types */
	static function allProducts(){
		return parent::all(new self());
	}

	/* Return an existing Product object for given criteria */
	static function existingProduct($criteria){
		$product = new self();
		return $product->load($criteria);
	}

	function update(){
		Controller::getInstance()->update($this);
	}
	/* Provides access to protected attributes */
	function __get($name){
		switch ($name){
			case 'productId':
			case 'name':
			case 'category':
			case 'shortDescription':
			case 'description':
			case 'quantity':
			case 'price':
			case 'purchasedQuantity':
			case 'img':
				return $this->$name;
			case 'categoryId':
				return $this->category->categoryId;
			case 'categoryName':
				return $this->category->name;
			default:
				throw new \BadFunctionCallException("$name does not exist or is not visible.");
		}
	}
	/* Provides write access to protected fields. Validates input and
	 * formats when necessary.
	 */
	function __set($name, $value) {
		nullIf($value, '');
		switch ($name){
			case 'productId':
				if($value == intval($value) && $value > 0){
					$this->$name = $value;
				} else {
					array_push($this->errors, "Invalid ProductID");
				}
				break;
			case 'name':
				if ($this->validateStrAttribute($name, $value, true, 30,
					ALPHANUM)){
					$this->$name = $value;
				}
				break;
			case 'category':
				if($value == intval($value) && $value > 0){
					try {
						$category = Category::existingCategory(['category_id' => $value]);
						$this->$name = $category;
					} catch (UnexpectedQueryResultException $ex){
						array_push($this->errors, "Invalid CategoryID");
					} catch (\Exception $ex) {
						throw $ex;
					}
				} else {
					array_push($this->errors, "Invalid CategoryID");
				}
				break;
			case 'price':
				$value = str_replace('$','',str_replace(' ','',$value));
				if (is_numeric($value)){
					$this->$name = floatval($value);
				} else {
					array_push($this->errors, "Invalid Price");
				}
				break;
			case 'shortDescription':
				if ($this->validateStrAttribute($name, $value, true, 30,
					ALPHANUM)){
					$this->$name = $value;
				}
				break;
			case 'description':
				if ($this->validateStrAttribute($name, $value, true, 256,
					ALPHANUM)){
					$this->$name = $value;
				}
				break;
			case 'img':
				if ($_FILES['img']['error'] > 0){
					array_push($errors, "File error");
				}
				if (is_uploaded_file($_FILES['img']['tmp_name'])) {
					$upfile = 'images/' . $_FILES['img']['name'];
					if (!move_uploaded_file($_FILES['img']['tmp_name'], $upfile)) {
						array_push($errors, "File move error");
					}
					$this->$name = $_FILES['img']['name'];
				} elseif (!file_exists("images/$value")){
					array_push($errors, "File does not exist");
				}
				break;
			case 'quantity':
				if($value != intval($value)){
					array_push($this->errors, "Quantity must be an integer");
				} elseif ($value < 0){
					array_push($this->errors, "Quantity must be positive");
				} else {
					$this->$name = $value;
				}
				break;
			default:
				$this->$name = $value;
		}
	}
	/* Return all Products that contain the given categoryId */
	static function getProductsByCategory($categoryId){
		$products = self::allProducts();
		return array_filter($products, function($p) use ($categoryId)
				{return intval($p->categoryId) === $categoryId; });
	}
	/* Checks if product name is unused */
	static function isProductAvailable($product_name){
		$data = Controller::getInstance()->query(
			"select is_product_available('$product_name')");
		$output = boolval($data->fetch_row()[0]);
		$data->free();
		return $output;
	}
	function insertSQL(){
		buildParameterList($parameters,$this->name, STRING);
		buildParameterList($parameters,$this->categoryId, INTEGER);
		buildParameterList($parameters,$this->price, STRING);
		buildParameterList($parameters,$this->shortDescription, STRING);
		buildParameterList($parameters,$this->description, STRING);
		buildParameterList($parameters,$this->img, STRING);
		buildParameterList($parameters,$this->quantity, INTEGER);
		return "insert into product (product_name, category_id, price, ".
				"product_short_desc, product_desc, img, quantity) ".
				"values ($parameters)";
	}
	function getSQL(){
		return "select product_id as productId, product_name as name, ".
			"category_id as category, price, product_short_desc as ".
			"shortDescription, product_desc as description, img, quantity ".
			"from product";
	}
	function updateSQL(){
		return "update product set quantity = $this->quantity where product_id = $this->productId";
	}
}
