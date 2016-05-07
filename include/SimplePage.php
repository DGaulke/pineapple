<?php namespace pineapple;
include_once('Page.php');
/**
 * David Gaulke
 * 3/1/2015
 * The SimplePage class is a concrete implementation of page. It displays the
 * navigation menu that it inherits and displays a single focus of content
 */
class SimplePage extends Page {
    public $content;
	/* Displays the page's main content */
    protected function displaySection(){
        echo "<section>";
        echo "<div id='sectionArea'>";
        echo $this->content;
        echo "</div>";
        echo "</section>";
    }
	/* Shortcut method to display a given piece of content and then stop
	 * php script */
    public function displayContent($content){
        $this->content = $content;
        $this->display();
        exit;
    }
}

?>
