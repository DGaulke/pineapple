<?php namespace pineapple;
/**
 * David Gaulke
 * 2/21/2015
 * This page displays all of the user's order history.
 */
session_start();
include_once('include/functions.php');

authenticate();
if (count($_SESSION['cart']) === 0){
    header('Location: view_cart.php');
}

include_once('include/SimplePage.php');
$page = new SimplePage();
$page->title = 'pineapple - checkout';
$page->addCSS(['main.css', 'form.css']);
$page->addJS(['valid_form.js']);
$page->setUserLogin(getUserLoginInfo());

$form = "";
include('include/checkout_form.php');
$page->displayContent($form);
?>




