<?php namespace pineapple;
/**
 * David Gaulke
 * 3/12/2015
 * The DBInsertException object is thrown when an object cannot be persisted
 * to the database.
 */

class DBInsertException extends \Exception {
    private $object;
    function __construct(Persistent $object){
        $this->object = $object;
    }
    function __toString(){
        return "<p class='note'><strong>".get_class($this->object)." could not be created at this time. ".
                "Please try again later.</strong></p><br>".
                $this->getTrace();
    }

}