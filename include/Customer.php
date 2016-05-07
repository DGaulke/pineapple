<?php namespace pineapple;
/**
 * David Gaulke
 * 3/1/2015
 * The Customer class is used to model registered users of the site.
 */
include_once('Controller.php');
include_once('Persistent.php');
include_once('functions.php');
include_once('InvalidObjectException.php');


class Customer extends Persistent {
	protected $customerId, $loginId, $firstName, $lastName, $addr1, $addr2,
		$city, $state, $zip, $phone, $dob, $email, $source, $subscribe,
	   	$regDate, $password, $comments, $admin = false;

	/* Constructor hidden - use static factory methods */
	private function __construct(){}

	/* Create a new Customer object and validate */
	static function newCustomer($data){
		$customer = new self();
		if (sizeof($data) != 17 && sizeof($data) != 16 && sizeof($data) != 14){
			array_push($customer->errors,
				"Wrong number of arguments supplied");
			throw new InvalidObjectException($customer);
		}
		return $customer->build($data);
	}
	/* Load from permanent storage and return an existing Customer object
	 * based on criteria in parameter */
	static function existingCustomer($criteria){
		$customer = new self();
		return $customer->load($criteria);

	}
	/* Return a list of all registered customers */
	static function allCustomers(){
		return parent::all(new self());
	}
	/* Ensures customer has entered info correctly, then tries to save record
	* to permanent storage */
	function register(){
		if (!Customer::isLoginAvailable($this->loginId)){
			array_push($this->errors,
					"Login '$this->loginId' is already in use");
		}	
		if (!$this->isValid()) {
			throw new InvalidObjectException($this);
		}
		$this->persist();
		$data = Controller::getInstance()->query("select @customer_id");
		$this->customerId = $data->fetch_row()[0];
	}
	/* Update the current object's attributes to permanent storage */
	function update(){
		$existingData = Controller::getInstance()->load(new self(), ["customer_id" => $this->customerId]);
		$existingLogin = $existingData->loginId;
		if ($this->loginId !== $existingLogin && !Customer::isLoginAvailable($this->loginId)){
			array_push($this->errors,
				"Login '$this->loginId' is already in use");
			throw new InvalidObjectException($this);
		}
		Controller::getInstance()->update($this);
		if ($existingLogin === $_SESSION['valid_user']){
			$_SESSION['valid_user'] = $this->loginId;
			$_SESSION['is_admin'] = $this->admin;
		}
	}
	/* Provide access to protected attributes */
	function __get($name){
		switch ($name){
			case 'customerId':
			case 'loginId':
			case 'firstName':
			case 'lastName':
			case 'addr1':
			case 'addr2':
			case 'city':
			case 'state':
			case 'zip':
			case 'phone':
			case 'dob':
			case 'email':
			case 'subscribe':
			case 'admin':
				return $this->$name;

		}
	}
	/* Provides write access to protected fields. Validates input and
	 * formats when necessary.
	 */
	function __set($name, $value){
		nullIf($value, '');
		switch ($name) {
 			case 'customerId':
				if($value == intval($value) && $value > 0){
					$this->$name = $value;
				} else {
					array_push($this->errors, "Invalid CustomerID");
				}
				break;
			case 'dob':
				if ($value === null){
					return;
				} elseif (preg_match('/^\d{1,2}((\-\d{1,2}\-)|(\/\d{1,2}\/))'.
							'(\d{2}|\d{4})$/', $value)) {
					$dateDelimiter = strpos($value, '/') ? '/' : '-';
					$date = \DateTime::createFromFormat('m'.$dateDelimiter.
							'd'.$dateDelimiter.'Y', $value);
					$this->$name = $date->format('Y-m-d');
				} else {
					array_push($this->errors, "Invalid Date of Birth");
				}
				break;
			case 'email':
				if (filter_var(strtolower($value), FILTER_VALIDATE_EMAIL)) {
					$this->$name = $value;
				} else {
					array_push($this->errors, "Invalid Email Address");
				}
				break;
			case 'subscribe':
				if ($value === 'subscribe'){
					$this->$name = true;
				} elseif ($value === null){
					$this->$name = false;
				} else {
					array_push($this_errors, "Invalid subscription choice");
				}
				break;
			case 'password':
				if (strlen($value) < 8){
					array_push($this->errors, 
							"Password must be at least 8 characters");
				} elseif (strlen($value) > 50) {
					array_push($this->errors, 
							"Password cannot exceed 50 characters");
				}  else {
					$this->$name = $value;
				}
				break;
			case 'confirmPassword':
				if ($value !== $this->password){
					array_push($this->errors, "Passwords do not match");
				}
				break;
			case 'comments':
				$this->$name = str_replace("\r\n", "|", $value);
				break;
			case 'regDate':
				$this->$name = $value;
				break;
			case 'admin':
				if ($value !== true && $value !== false){
					array_push($this->errors, "Admin must be true or false");
				} else{
					$this->$name = $value;
				}
				break;
			case 'loginId':
				if ($this->validateStrAttribute($name, $value, true, 30,
					ALPHANUM)){
					$this->$name = $value;
				}
				break;
			case 'firstName':
			case 'lastName':
				if ($this->validateStrAttribute($name, $value, true, 20,
					ALPHANUM)){
					$this->$name = $value;
				}
				break;
			case 'addr1':
			case 'addr2':
				if ($this->validateStrAttribute($name, $value, false, 30,
					ALPHANUM)){
					$this->$name = $value;
				}
				break;
			case 'city':
				if ($this->validateStrAttribute($name, $value, false, 20,
					ALPHANUM)){
					$this->$name = $value;
				}
				break;
			case 'state':
				if ($this->validateStrAttribute($name, $value, false, 2,
					'/^[A-Z]{2}$/')){
					$this->$name = $value;
				}
				break;
			case 'zip':
				if ($this->validateStrAttribute($name, $value, false, 10,
					'/^(\d{5}(-?\d{4})?)?$/')){
					$this->$name = $value;
				}
				break;
			case 'phone':
				if ($this->validateStrAttribute($name, $value, false, 12,
					'/(\d{3}\-\d{3}\-\d{4})?$/')){
					$this->$name = $value;
				}
				break;
			case 'source':
				if ($this->validateStrAttribute($name, $value, false,
					15, '/^[\w]*$/')){
					$this->$name = $value;
				}
				break;
			default:
				break;
		}
	}
	/* Checks if a loginId is unused */
	static function isLoginAvailable($loginId){
		$data = Controller::getInstance()->query(
				"select is_login_available('$loginId')");
		$output = boolval($data->fetch_row()[0]);
		$data->free();
		return $output;
	}
	function insertSQL(){
		buildParameterList($parameters, $this->loginId, STRING);
		buildParameterList($parameters, $this->firstName, STRING);
		buildParameterList($parameters, $this->lastName, STRING);
		buildParameterList($parameters, $this->addr1, STRING);
		buildParameterList($parameters, $this->addr2, STRING);
		buildParameterList($parameters, $this->city, STRING);
		buildParameterList($parameters, $this->state, STRING);
		buildParameterList($parameters, $this->zip, STRING);
		buildParameterList($parameters, $this->phone, STRING);
		buildParameterList($parameters, $this->dob, DATE);
		buildParameterList($parameters, $this->email, STRING);
		buildParameterList($parameters, $this->source, STRING);
		buildParameterList($parameters, $this->subscribe, BOOLEAN);
		buildParameterList($parameters, $this->regDate, STRING);
		buildParameterList($parameters, sha1($this->password), STRING);
		buildParameterList($parameters, $this->comments, STRING);
		
		return "call insert_customer($parameters, @customer_id);";
	}
	function updateSQL(){
		buildParameterList($parameters, $this->customerId, INTEGER);
		buildParameterList($parameters, $this->loginId, STRING);
		buildParameterList($parameters, $this->firstName, STRING);
		buildParameterList($parameters, $this->lastName, STRING);
		buildParameterList($parameters, $this->addr1, STRING);
		buildParameterList($parameters, $this->addr2, STRING);
		buildParameterList($parameters, $this->city, STRING);
		buildParameterList($parameters, $this->state, STRING);
		buildParameterList($parameters, $this->zip, STRING);
		buildParameterList($parameters, $this->phone, STRING);
		buildParameterList($parameters, $this->dob, DATE);
		buildParameterList($parameters, $this->email, STRING);
		buildParameterList($parameters, $this->subscribe, BOOLEAN);
		buildParameterList($parameters, sha1($this->password), STRING);
		buildParameterList($parameters, $this->admin, BOOLEAN);

		return "call update_customer($parameters);";
	}
	function getSQL(){
		return "select c.customer_id AS customerId, c.login_id as loginId, ".
				"c.first_name as firstName, c.last_name as lastName, ".
				"a.address_line_1 as addr1, a.address_line_2 as addr2, a.city,".
				"a.state_abrv as state, a.zip_code as zip, c.phone, ".
				"case when c.birthdate is null then null else date_format(c.birthdate, '%m-%d-%Y') end as dob, c.email, ".
				"s.source_name as source, c.registration_date as regDate, ".
				"c.password, c.password as confirmPassword, c.comments, ".
				"case when c.subscribe then 'subscribe' else null end ".
				"as subscribe, c.admin from customer as c ".
				"inner join address a on c.address_id = a.address_id ".
				"inner join source s on c.source_id = s.source_id";
	}
}
