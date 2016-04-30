<?php namespace pineapple;
/*
 * David Gaulke
 * 2/8/2015
 * This page displays the categories of baked goods available for purchase
 * along with a stock image.
 */
session_start();
include_once('include/functions.php');
include_once('include/OptionPage.php');
include_once('include/Category.php');

$page = new OptionPage();
$page->title = 'pineapple - menu';
$page->addCSS(['main.css','menu.css']);
$page->setUserLogin(getUserLoginInfo());
$page->addMenuItems(getMenuItems());

/* Display all categories as options */
$categories = Category::allCategories();
foreach($categories as $category){
	$page->addOption('view_category.php?id='.$category->categoryId,
			$category->name, $category->description);
}

$page->setDetail("<img id='img_menu' src='images/cake.png'>");
$page->display();
?>
