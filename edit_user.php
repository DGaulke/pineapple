<?php namespace pineapple;
/**
 * David Gaulke
 * 2/21/2015
 * This page allows an administrator to edit an existing user.
 */
session_start();
include_once('include/functions.php');
include_once('include/SimplePage.php');
include_once('include/Customer.php');

authorizeAdmin();

	$page = new SimplePage();
$page->title = 'pineapple - edit user';
$page->addCSS(['main.css', 'form.css']);
$page->setUserLogin(getUserLoginInfo());
$page->addMenuItems(getMenuItems());


if (isset($_POST['select'])){
    header('Location: view_users.php');
} elseif(isset($_POST['search']) || isset($_GET['id'])){
	cleanInput();
	try {
		if (isset($_GET['id'])){
			$key = 'customer_id';
			$value = intval($_GET['id']);
		} else {
			$key = $_POST['search_field'];
			$value = $_POST['search_value'];
		}

		$customer = Customer::existingCustomer([$key => $value]);
		$editForm = "";
		include('include/user_form.php');
			$page->displayContent($editForm);
	} catch (\Exception $ex){
		$error = $ex->__toString();
	}
}

$searchForm = "";
include('include/search_form.php');
$content = $searchForm.$error;
$page->displayContent($content);


?>

