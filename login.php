<?php namespace pineapple;
/**
 * David Gaulke
 * 2/18/2015
 * This page allows a user to log in as a registered customer.
 */
session_start();
include_once('include/functions.php');
include_once('include/SimplePage.php');
include_once('include/Customer.php');

$page = new SimplePage();
$page->title = 'pineapple - log in';
$page->addCSS(['main.css','form.css']);
$page->addJS(['valid_form.js']);
$page->setUserLogin(getUserLoginInfo());
$page->addMenuItems(getMenuItems());

if(isLoggedIn()){
	$content = "";
	/* Users redirected here when access was denied elsewhere */
	if (failedAdminAuthorization()){
		unset($_SESSION['req_admin']);
		$content = "<p><strong>You must be an administrator to access this".
		   " feature!</strong></p>";
	}
	/* Display log out link if already logged in */
	$content .= "<p>Currently logged in as ".$_SESSION['valid_user'].
			" : <a href='logout.php'>Log out</a></p>";
	$page->displayContent($content);
}

if (isset($_POST['login'])) {
	cleanInput();
	try {
		/* Attempt to log user in using credentials from form */
		$customer = Customer::existingCustomer(
				["login_id" => $_POST['login_id'],
				"password" => sha1($_POST['password'])]);
		$_SESSION['valid_user'] = $customer->loginId;
		$_SESSION['customer_id'] = $customer->customerId;
		$_SESSION['is_admin'] = $customer->admin;
		/* Determine where to redirect based on user privileges */
		$url = isAdmin() ? 'view_users.php' : 'index.php';
		header('refresh:2, url='.$url);
		$page->displayContent("Thank you for logging in $customer->firstName ".
				"$customer->lastName!");
	} catch (UnexpectedQueryResultException $ex){
		$failed_login = true;
	} catch (\Exception $ex){
		$page->displayContent($ex->__toString());
	}
}

$login_form = "";
include('include/login_form.php');
$page->displayContent($login_form);

/* Check if user has been redirected to this page by an admin function */
function failedAdminAuthorization(){
	return isset($_SESSION['req_admin']) && $_SESSION['req_admin'];
}

?>
