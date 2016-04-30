<?php namespace pineapple;
/*
 * David Gaulke
 * 2/8/2015
 * This is the main index with links to available admin function 
 */
session_start();
include_once('include/functions.php');

authorizeAdmin();

include_once('include/OptionPage.php');

$page = new OptionPage();
$page->title = 'pineapple - admin functions';
$page->addCSS(['main.css', 'menu.css']);
$page->setUserLogin(getUserLoginInfo());
$page->addMenuItems(getMenuItems());

$page->addOption('view_users.php', 'view all users');
$page->addOption('edit_user.php', 'edit user');
$page->addOption('add_category.php', 'add category');
$page->addOption('add_product.php', 'add product');
$page->addOption('all_orders.php', 'view all orders');

$page->setDetail( "<img id='img_menu' src='images/admin.png'>");

$page->display();





?>
