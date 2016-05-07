<?php namespace pineapple;
/*
 * David Gaulke
 * 2/8/2015
 * This page allows a user to register themselves as a customer,and they
 * will then be allowed to log in to the site, and also appear on the site's
 * View All Users page.
  If a submitted form hasn't been posted, send back to register page */
if (!isset($_POST['submit'])) {
	header('Location: register.php');
}

session_start();
include_once('include/functions.php');
include_once('include/SimplePage.php');
$page = new SimplePage();
$page->title = 'pineapple - creating account';
$page->addCSS(['main.css','table.css']);
$page->setUserLogin(getUserLoginInfo());
$page->addMenuItems(getMenuItems());

/* Instantiate a customer from the submitted form data */
include_once('include/Customer.php');
try {
	cleanInput();
	$customer = Customer::newCustomer([
		"loginId" => $_POST['login_id'], "firstName" => $_POST['first_name'],
		"lastName" => $_POST['last_name'], "addr1" => $_POST['addr1'],
		"addr2" => $_POST['addr2'], "city" => $_POST['city'],
		"state" => $_POST['state'], "zip" => $_POST['zip'],
		"phone" => $_POST['phone'], "dob" => $_POST['dob'],
		"email" => $_POST['email'], "source" => $_POST['source'],
		"subscribe" => $_POST['subscribe'], "comments" => $_POST['comments'],
		"password" => $_POST['password'],
		"confirmPassword" => $_POST['confirmPassword'],
		"regDate" => date('Y-m-d H:i:s')]);

	$customer->register();
	$_SESSION['valid_user'] = $customer->loginId;
	$_SESSION['customer_id'] = $customer->customerId;
	$_SESSION['is_admin'] = $customer->admin;
	/* If successful, redirect after message is displayed */
	header('refresh:2, url=index.php');
	$page->displayContent("<p><strong>Registration successful. Thank you!</strong></p>");
} catch (\Exception $ex) {
	$page->displayContent($ex->__toString());
}
?>
