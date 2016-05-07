<?php namespace pineapple;
/**
 * David Gaulke
 * 2/8/2015
 * This page allows a user to register as a user of the site,
 * and they will then appear on the site's View All Users page.
 */
include_once('include/functions.php');
session_start();
if (isLoggedIn()){
    header('Location: index.php');
}

include ('include/SimplePage.php');

$page = new SimplePage();
$page->title = 'pineapple - registration';
$page->addCSS(['main.css','form.css']);
$page->addJS(['valid_form.js']);
$page->setUserLogin(getUserLoginInfo());
$page->addMenuItems(getMenuItems());

$registerForm = "";
include('include/user_form.php');
$page->displayContent($registerForm);

?>
