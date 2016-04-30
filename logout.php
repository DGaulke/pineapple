<?php namespace pineapple;
/**
 * David Gaulke
 * 2/21/2015
 * This page logs a user out of an active session
 */
session_start();
/* Retain logged in user id to display logout message */
$previous_user = $_SESSION['valid_user'];
/* Erase sessions strorage */
$_SESSION = array();
session_destroy();
/* Begin clean session with logged out user id saved and return to main page */
session_start();
$_SESSION['logout_user'] = $previous_user;
header('Location: index.php');

?>
