<?php namespace pineapple;
/*
 * David Gaulke
 * 2/8/2015
 * This is the main index with links to available site pages.
 */
session_start();
include_once('include/functions.php');
include_once('include/SimplePage.php');

$page = new SimplePage();
$page->title = 'pineapple - a bakery';
$page->addCSS(['main.css']);
$page->setUserLogin(getUserLoginInfo());
$page->addMenuItems(getMenuItems());

$page->displayContent("<img id='img_home' src='images/home.png'>");
?>
