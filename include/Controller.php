<?php namespace pineapple;
/*
 * David Gaulke
 * 3/8/2015
 * The Controller class is a singleton that controls the storage and retrieval
 *  of objects from the database
 */
include_once('Persistent.php');
include_once('functions.php');
include_once('DBInsertException.php');

class Controller{
	private $db;
	/* Maintain Singleton pattern */
	private function __construct(){}
	private function __clone(){}
	/* Retrieve sole instance of Controller */
	static function getInstance() {
		static $instance = null;
		if (null === $instance) {
			$instance = new self();
		}
		return $instance;
	}
	/* Store Persistent object to database */
	function persist(Persistent $object) {
		$outcome = $this->query($object->insertSQL());
		if (!$outcome){
			throw new DBInsertException($object);
		}
	}
	/* Update Persistent object in database */
	function update(Persistent $object){
		$outcome = $this->query($object->updateSQL());
		if (!$outcome){
			throw new DBUpdateException($object);
		}
	}
	/* Retrieve a Persistent object from the database with the specified
	 * criteria.  Must return unique record.
	 */
	function load(Persistent $object, $criteria){
		$sql = $object->getSQL().expandCriteria($criteria);

		$data = $this->query($sql);
		if (!$data){
			throw new \Exception("An error has occurred.");
		} elseif ($data->num_rows !== 1){
			include_once('UnexpectedQueryResultException.php');
			throw new UnexpectedQueryResultException('Rows expected=1;'.
				' Rows returned='.$data->num_rows, $data->num_rows, $object);
		}
		return rebuildObject($data, $object);

	}
	/* Retrieve an array of all Persistent objects of a given type */
	function loadAll(Persistent $object) {

		$data = $this->query($object->getSQL());
		if (!$data){
			throw new \Exception("An error has occurred.");
		}
		$output = array();
		while ($element = rebuildObject($data, $object)) {
			array_push($output, $element);
		}
		$data->free();
		return $output;
	}
	/* Execute a query against the database */
	function query($sql){
		if (!isset($this->db)){
			@ $this->db = new \mysqli('localhost', 'apache', 'raspberry', 'pineapple');
		}
		if (mysqli_connect_errno() !== 0){
			throw new DBConnectException(mysqli_connect_error());
		}
		$this->db->real_escape_string($sql);
		$output = $this->db->query($sql);
		return $output;

	}
	/* Close db connection when Controller is destroyed */
	function __destruct(){
		mysqli_close($this->db);
	}
}
