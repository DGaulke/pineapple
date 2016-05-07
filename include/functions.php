<?php namespace pineapple;
/**
 * David Gaulke
 * 2/23/2015
 * This page contains common functions
 */

const ALPHANUM = '/^[a-zA-Z0-9\s\p{P}]/';
const INTEGER = 0;
const STRING = 1;
const BOOLEAN = 2;
const DATE = 3;

/* Checks if a user is currently logged in */
function isLoggedIn(){
    return isset($_SESSION['valid_user']);
}
/* Checks if a user has just logged out */
function justLoggedOut(){
    return isset($_SESSION['logout_user']);
}
/* Return the loginId of the user that just logged out */
function getLogoutUser(){
    $output = justLoggedOut() ? $_SESSION['logout_user'] : null;
    unset($_SESSION['logout_user']);
    return $output;
}
/* Ensure that a user is logged in. If not, sends them to login page */
function authenticate(){
    if(!isLoggedIn()){
        echo '<p>You must log in to access this page!</p>';
        header('refresh:2, url=login.php');
        exit;
    }
}
/* Check if logged-in user is an admin */
function isAdmin(){
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'];
}
/* Ensure that logged in user is an admin. If not, sends them to the
* login page */
function authorizeAdmin(){
    if(!isAdmin()){
        $_SESSION['req_admin'] = true;
        header('Location: login.php');
        exit;
    }
}
/* Get information about current login status for display */
function getUserLoginInfo(){
    if (isLoggedIn()) {
        return ['userLogin'=>$_SESSION['valid_user'], 'justLoggedOut'=>false];
    } elseif (justLoggedOut()) {
        return ['userLogin'=>getLogoutUser(), 'justLoggedOut'=>true];
    }
}
/* Conditionally determine which items to display in navigation menu */
function getMenuItems(){
	$output = array();
    array_push($output,['url'=>'menu.php', 'item'=>'menu']);
    array_push($output,['url'=>'about.php', 'item'=>'about']);
    array_push($output,['url'=>'mailto:gaulda@metrostate.edu?subject=".
            "ICS325-01 Project (GAULKE)', 'item'=>'contact']);
    if (isLoggedIn()) { // Logged-in users can view past orders
        array_push($output,['url'=>'view_orders.php', 'item'=>'orders']);
    } else { // Non-logged-in users can go to login page
        array_push($output,['url'=>'login.php', 'item'=>'log in']);
    }
    if (isAdmin()) {  // Admins can use admin page */
        array_push($output,['url'=>'admin.php', 'item'=>'admin']);
    }
	return $output;
}
/* Converts user-input date format to database-acceptable format */
function reformatDate($date){
    if ($date != '' && preg_match('/^(\d{1,2}((\-\d{1,2}\-)|'.
            '(\/\d{1,2}\/))(\d{2}|\d{4}))?$/',$date)){
        $dateDelimiter = strpos($date, '/') ? '/' : '-';
        $date = \DateTime::createFromFormat('m'.$dateDelimiter.'d'.
                $dateDelimiter.'Y', $date);
        return $date->format('Y-m-d');
    } else {
        return $date;
    }
}
/* Expands an array of criteria into a string delimited with "and" */
function expandCriteria($criteria){
    if (count($criteria)){
        $expansion = array();
        foreach ($criteria as $key => $value) {
            if (strtolower(substr($key, strlen($key) -2)) === "id" &&
                    is_numeric($value)){
                array_push($expansion, "$key = $value");
            } else {
                array_push($expansion, "$key = '$value'");
            }
        }
        return " where ".implode(' and ', $expansion);
    } else {
        return "";
    }
}
/* Encode html tags from POST data as literal */
function cleanInput() {
    foreach ($_POST as $name => $value) {
        $_POST[$name] = htmlspecialchars($value);
    }
}
/* Sets value1 to null if it equals value2 */
function nullIf(&$value1, $value2){
    if ($value1 === $value2){
        $value1 = null;
    }
}
/* Formats $field in format expected by mysql according to $type and appends to
* $list
*/
function buildParameterList(&$list, $field, $type){
	if (strlen($list) > 0){
		$list .= ', ';
	}
	switch ($type){
		case INTEGER:
			$list .= intval($field);
			break;
		case STRING:
			$list .= "'".(get_magic_quotes_gpc() ? $field :
                    addslashes($field))."'";
			break;
		case BOOLEAN:
			$list .= $field ? "true" : "false";
			break;
        case DATE:
            if ($field === null){
                $list .= "null";
            } else {
                $list .= "'".(get_magic_quotes_gpc() ? $field :
                        addslashes($field))."'";
            }
	}
}
/* Runs object attributes through __set method to validate */
function rebuildObject(\mysqli_result $data, Persistent $object){
    $output = $data->fetch_object(get_class($object));
    if (!$output){
        return false;
    }
    $output->resetAttributes();
    if (!get_magic_quotes_gpc()){
        $output->strip_slashes();
    }
    return $output;
}

?>
