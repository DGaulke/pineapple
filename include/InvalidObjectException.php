<?php
/**
 * David Gaulke
 * 3/12/2015
 * The InvalidObjectException object is thrown when an object cannot be
 * created because of invalid attributes
 */

namespace pineapple;


class InvalidObjectException extends \Exception {
    private $object;
    function __construct(Persistent $object){
        $this->object = $object;
    }
    function __toString() {
        $output = "<p class='note'><strong>".nl2br($this->object->displayErrors()).
                "<br>Please try again.</strong></p>";
        if ($this->object instanceof Customer) {
            $output .= isset($this->object->customerId) ? "<a href='edit_user.php?id=".$this->object->customerId."'>Return to edit user</a>" : "<a href='register.php'>Return to registration</a>";
        } elseif ($this->object instanceof Category){
            $this->message = $this->object->__toString();
        } elseif ($this->object instanceof Product){
            $output .= "<a href='../add_product.php'>Return to add product</a>";
        }
        return $output;
    }
}