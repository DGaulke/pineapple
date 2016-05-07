<?php namespace pineapple;
include_once('Page.php');
/*
* David Gaulke
* 3/5/2015
* The OptionPage class is a concrete implementation of Page. In addition to the
 * navigation menu, it displays an additional list of options in the main
 * window.
*/
class OptionPage extends Page {
    private $options = array();
    private $detail = "";

    protected function displaySection(){
        echo "<section>";
        echo "<div id='sectionArea'>";

        /* Display each option from $options floating on left side */
        echo "<div id='sidebar'>";
        echo "<ul id='menu-items'>";
        $this->displayOptions();
        echo "</ul>";
        echo "</div>";

        /* Display content in remainder of space */
        $this->displayDetail();

        echo "</div>";
        echo "</section>";
    }
    /* Helper function to compile the list of options with HTML tags */
    protected function displayOptions(){
        foreach ($this->options as $url => $item) {
            echo "<li><span title=\"".$item['description']."\">".(parent::isURLCurrentPage($url) ?
                    "*".strtoupper($item['name'])."*" :
                    "<a href='".$url."'>".strtoupper($item['name'])."</a>")."</span></li>";
        }
    }
    /* Add option to display on site - URL and item name/description */
    public function addOption($url, $name, $description){
        $this->options[$url] = ["name" => $name, "description" => $description];
    }
    /* Add content to supplement options */
    public function setDetail($detail){
        $this->detail = $detail;
    }
    /* Show main content */
    protected function displayDetail(){
        echo "<div id='detail'>";
        echo $this->detail;
        echo "</div>";
    }
}

