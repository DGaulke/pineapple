<?php namespace pineapple;
/* The Page class represents all required elements for every page 
 * in the site */
abstract class Page{
	const contentType = "<meta http-equiv='Content-Type' 
			content='text/html; charset=UTF-8' />";
    const logo = "logo.png";

    public $title;
    private $userLogin = "";
    private $justLoggedOut = false;
    private $styles = array();
    private $scripts = array();
    private $menuItems = array();

    /* Echoes page as html */
    public function display()    {
        $this->displayDocType();
        echo "<html>";
        $this->displayHead();
        $this->displayBody();
        echo "</html>";
    }
	private function displayDocType(){
        echo "<!DOCTYPE html>";
    }
    private function displayHead(){
        echo "<head>";
        $this->displayTitle();
        $this->displayContentType();
        $this->displayCSS();
        $this->displayJS();
        echo "</head>";
    }
    private function displayTitle(){
        echo "<title>$this->title</title>";
    }
    private function displayContentType(){
        echo page::contentType;
    }
    private function displayCSS(){
        foreach ($this->styles as $css) {
			echo "<link rel='stylesheet' type='text/css' href='".
					"include/$css'>";
        }
    }
    private function displayJS(){
        foreach ($this->scripts as $js) {
			echo "<script type='text/javascript' src='".
					"include/$js'></script>";
        }
    }
    private function displayBody(){
        echo "<body>";
        $this->displayHeader();
        $this->displaySection();
        $this->displayFooter();
        echo "</body>";
    }
    protected function displayHeader(){
        echo "<header>";
        echo "<div id='headerArea'>";
        $this->displayUserLogin();
        $this->displayLogo();
        $this->displayNavigationMenu();
        echo "</div>";
        echo "</header>";
    }
    private function displayUserLogin(){
        echo "<span id='user_login'>";
        if ($this->justLoggedOut) {
            echo $this->userLogin." logged out successfully";
        } elseif (strlen($this->userLogin)) {
            $itemCount = count($_SESSION['cart']);
			echo $this->userLogin.
                ($itemCount > 0 ? " :: <a href='view_cart.php'>shopping cart ($itemCount)</a>" : "").
                    " :: <a href='logout.php'>log out</a>";
        }
        echo "</span><br>";
    }
    private function displayLogo(){
        echo $this->isURLCurrentPage('/bakery/index.php') ?
            "<img id='logo' src='images/".page::logo."'>" :
			"<a href='index.php'><img id='logo' src='".
					"images/".page::logo."'></a>";
    }
    private function displayNavigationMenu(){
        echo "<ul id='menu-navigation'>";
        foreach ($this->menuItems as $link) {
            $this->displayMenuItem($link['url'], $link['item']);
        }
        echo "</ul>";
    }
    private function displayMenuItem($url, $item){
        echo "<li>".($this->isURLCurrentPage($url) ? $item :
                "<a href='$url'>".strtolower($item)."</a>")."</li>";
    }
    function setUserLogin($data){
        $this->userLogin = $data['userLogin'];
        $this->justLoggedOut = $data['justLoggedOut'];
    }
	/* Will vary by concrete implementation */
    abstract protected function displaySection();
	
	protected function displayFooter(){
?>
    <footer>
        <div id="footerArea">
            &copy; 2015
        </div>
    </footer>
<?php
    }
    function addCSS($styles){
        foreach($styles as $css) {
            array_push($this->styles, $css);
        }
    }
    function addJS($scripts){
        foreach($scripts as $js) {
            array_push($this->scripts, $js);
        }
    }
    function addMenuItems($links){
		$this->menuItems = $links;
    }
    protected function isURLCurrentPage($url){
        return (strpos($_SERVER['PHP_SELF'], $url) !== false ||
            strpos($_SERVER['REQUEST_URI'], $url) !== false) ? true: false;
    }
}
?>
