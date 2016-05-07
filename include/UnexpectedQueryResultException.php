<?php namespace pineapple;
/**
 * David Gaulke
 * 3/12/2015
 * The UnexpectedQueryResultException object is thrown when a query returns
 * an unexpected number of records
 */
include_once('include/Customer.php');

class UnexpectedQueryResultException extends \Exception {
    public $object, $rowCount;
    function __construct($message, $rowCount, Persistent $object){
        $this->message = $message;
		$this->rowCount = $rowCount;
        $this->object = $object;
    }
	function __toString(){
		if ($this->object instanceof Customer){
			return '<p class="note">'.($this->rowCount === 0 ? 'Customer not found' : 'Multiple customer records found').'</p>';
		} else {
			return parent::__toString();
		}
	}
}
