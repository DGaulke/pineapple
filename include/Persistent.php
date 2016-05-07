<?php namespace pineapple;
/*
 * David Gaulke
 * 3/8/2015
 * Abstract representation of an object that can be persisted to and retrieved
 * from a database
 */
abstract class Persistent {
	protected $errors = array();
	/* Returns an array of all instances of the given object type */
	static function all(Persistent $object){
		return Controller::getInstance()->loadAll($object);
	}
	/* Sets all attributes from $data array */
	function build($data){
		foreach ($data as $name => $value){
			$this->__set($name, $value);
		}
		if ($this->isValid()) {
			return $this;
		} else {
			throw new InvalidObjectException($this);
		}
	}
	function load($criteria){
		return Controller::getInstance()->load($this, $criteria);
	}
	function persist(){
		Controller::getInstance()->persist($this);
	}
	/* Helper function to check that a String attribute is given in the
	* correct format
	*/
	protected function validateStrAttribute($name, $value, $required,
			$maxLength, $pattern){
		if ($required && $value === null){
			array_push($this->errors, "$name is required");
			return false;
		} else {
			if ($maxLength > 0 && strlen($value) > $maxLength){
				array_push($this->errors, "$name is over $maxLength characters");
				return false;
			}
			if ($value !== null && !preg_match($pattern, $value)){
				array_push($this->errors, "Invalid $name format");
				return false;
			}
		}
		return true;
	}
	function isValid(){
		return sizeOf($this->errors) === 0;
	}
	function displayErrors() {

		return implode(";\n", $this->errors) . ";";
	}
	function strip_slashes(){
		foreach($this as $attribute => $value){
			if (is_string($this->$attribute)){
				$this->$attribute = stripslashes($value);
			}
		}
	}
	/* After object is reconstructed with mysqli_fetch_object, all of the
	* attributes are run through __set to validate format
	*/
	function resetAttributes(){
		foreach($this as $attribute => $value){
			$this->__set($attribute, $value);
		}
	}
	/* SQL will vary by implementation */
	abstract function insertSQL();
	abstract function updateSQL();
	abstract function getSQL();
	abstract function __set($name, $value);
}
