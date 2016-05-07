<?php namespace pineapple;
/**
 * David Gaulke
 * 2/25/2015
 * The Category class represents the different types of bakery items. They
 * have an id, a name, and a description.
 */

include_once('Controller.php');
include_once('Persistent.php');
include_once('InvalidObjectException.php');

class Category extends Persistent {
	protected $categoryId, $name, $description;

	/* Constructor hidden to force use of static factory methods */
	private function __construct(){}

	/* Create a new Category object and validate */
	static function newCategory($data){
		$category = new self();
		if (!Category::isCategoryAvailable($data['name'])) {
			array_push($category->errors, "Category ".$data['name']
					." already exists");
		}
		if (sizeof($data) != 2){
			array_push($category->errors,
				"Wrong number of argument supplied");
			throw new InvalidObjectException($category);
		}
		return $category->build($data);
	}

	/* Return all existing Category objects */
	static function allCategories(){
		return parent::all(new self());
	}
	/* Return an existing Category object for a given categoryId */
	static function existingCategory($criteria){
		$category = new self();
		return $category->load($criteria);
	}
	/* Provides read access to protected fields */
	function __get($name){
		switch ($name){
			case 'categoryId':
			case 'name':
			case 'description':
				return $this->$name;
			default:
				return null;
		}
	}
	/* Provides write access to protected fields. Validates input and
	 * formats when necessary.
	 */
	function __set($name, $value){
		nullIf($value, '');
		switch ($name){
			case 'categoryId':
				if($value == intval($value) && $value > 0){
					$this->$name = $value;
				} else {
					array_push($this->errors, "Invalid CategoryID");
				}
				break;
			case 'name':
				if ($this->validateStrAttribute($name, $value, true, 30,
						ALPHANUM)){
					$this->$name = $value;
				}
				break;
			case 'description':
				if ($this->validateStrAttribute($name, $value, true, 50,
						ALPHANUM)){
					$this->$name = $value;
				}
				break;
		}
	}
	/* Checks database to see if a category name has been used */
	static function isCategoryAvailable($category_name){
		$data = Controller::getInstance()->query(
			"select is_category_available('$category_name')");
		$output = boolval($data->fetch_row()[0]);
		$data->free();
		return $output;
	}
	function insertSQL(){
		buildParameterList($parameters, $this->name, STRING);
		buildParameterList($parameters, $this->description, STRING);

		return "insert into category (category_name, category_desc) ".
				"values ($parameters)";
	}
	function getSQL(){
		return "select category_id AS categoryId, category_name as name, ".
				"category_desc as description from category";
	}
	function updateSQL(){
		throw new \BadFunctionCallException("Update not implemented");
	}
	function __toString() {
		if ($this->isValid()) {
			return "Category: " . $this->name;
		} else {
			return parent::displayErrors();
		}
	}

}
