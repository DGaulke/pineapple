<?php namespace pineapple;
/**
 * David Gaulke
 * 2/21/2015
 * This page allows an administrator to add a new product.
 */
session_start();
include include_once('include/functions.php');

authorizeAdmin();

include_once('include/SimplePage.php');
$page = new SimplePage();
$page->title = 'pineapple - new product';
$page->addCSS(['main.css', 'form.css']);
$page->setUserLogin(getUserLoginInfo());
$page->addMenuItems(getMenuItems());

include_once('include/Product.php');
if (isset($_POST['submit'])){
    try {
        cleanInput();
        /* Create new Product object with POST data and store in database */
        $product = Product::newProduct(['name' => $_POST['product_name'],
                'category' => $_POST['category'], 'price' => $_POST['price'],
                'shortDescription' => $_POST['short_desc'],
                'description' => $_POST['description'], 'img' => $_POST['img'],
                'quantity' => $_POST['quantity']]);
        $product->persist();
        /* If successful, redirect after message is displayed */
        header('refresh:2, url=admin.php');
        $page->displayContent("<p><strong>Product added!</strong></p>");
    } catch (InvalidObjectException $ex){
        $error_message = $ex->__toString();
    } catch (\Exception $ex){
        $page->displayContent($ex->__toString());
    }
}

$form = "";
include('include/product_form.php');
$page->displayContent($form);
?>