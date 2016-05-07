<?php namespace pineapple;
/*
 * David Gaulke
 * 2/8/2015
 * This page displays info about the baker.
 */
session_start();
include_once('include/functions.php');
include_once("include/SimplePage.php");

$page = new SimplePage();
$page->title = 'pineapple - about';
$page->addCSS(['main.css','about.css']);
$page->setUserLogin(getUserLoginInfo());
$page->addMenuItems(getMenuItems());

$page->content = '<figure>'.
        '<img id="img_about" src="images/lisa_pineapple.png">'.
        '<figcaption>Lisa and Pineapple</figcaption>'.
        '</figure>';

$page->display();
?>