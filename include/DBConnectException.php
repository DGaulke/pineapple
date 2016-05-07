<?php
/**
 * David Gaulke
 * 3/12/2015
 * Self-explanatory class name
 */

namespace pineapple;


class DBConnectException extends \Exception {
    function __toString(){
        return "<p class='note'><strong>There was an error connecting to the database. ".
				"Please try again later.</strong></p>";
    }
}