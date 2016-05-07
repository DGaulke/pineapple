<?php namespace pineapple;
/**
 * David Gaulke
 * 3/12/2015
 * The DBUpdateException object is thrown when an existing object cannot be
 * updated to the database.
 */

class DBUpdateException extends \Exception {
    private $object;
    function __construct(Persistent $object){
        $this->object = $object;
    }
    function __toString(){
        return "<p class='note'><strong>".get_class($this->object)." could not be updated at this time. ".
                "Please try again later.</strong></p><br>".
                $this->getTrace();
    }

}