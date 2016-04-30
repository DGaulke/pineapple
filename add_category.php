<?php namespace pineapple;
/**
 * David Gaulke
 * 2/21/2015
 * This page allows an administrator to add a new category of products.
 */
session_start();
include_once('include/functions.php');

authorizeAdmin();

include_once('include/SimplePage.php');
$page = new SimplePage();
$page->title = 'pineapple - new category';
$page->addCSS(['main.css', 'form.css']);
$page->setUserLogin(getUserLoginInfo());
$page->addMenuItems(getMenuItems());

include_once('include/Category.php');
if (isset($_POST['submit'])){
	try {
		cleanInput();
		/* Create new Category object with POST data and store in database */
		$category = Category::newCategory(['name' => $_POST['category_name'],
				'description' => $_POST['category_desc']]);
		$category->persist();
		/* If successful, redirect after message is displayed */
		header('refresh:2, url=admin.php');
		$page->displayContent("<p><strong>Category added!</strong></p>");
	} catch (InvalidObjectException $ex){
		$error_message = $ex->__toString(); //used in form below
	} catch (\Exception $ex){
		$page->displayContent($ex->__toString());
	}
}

$form = "";
include('include/category_form.php');
$page->displayContent($form);
?>
