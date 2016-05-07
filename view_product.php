<?php namespace pineapple;
/**
 * Created by PhpStorm.
 * User: david
 * Date: 2/25/15
 * Time: 6:55 PM
 */
session_start();
include_once('include/functions.php');
include_once('include/SimplePage.php');
include_once('include/Product.php');

if (!isset($_GET['id'])){
	header('Location: view_category.php');
}

$productId = intval($_GET['id']);
$product = Product::existingProduct(["product_id" => $productId]);

$page = new SimplePage();
$page->title = 'pineapple - '.$product->name;
$page->addCSS(['main.css','item.css','form.css']);
$page->setUserLogin(getUserLoginInfo());
$page->addMenuItems(getMenuItems());

$page->displayContent(!$product ? '<p>Product not found.</p>' :
		displayProduct($product));

function displayProduct($product){
	$content = "<nav class='breadcrumbs'>";
	$content .= "<< <a href='view_category.php?id=".$product->categoryId."'>".
		$product->categoryName."</a>";
	$content .= "</nav>";

	$content .= "<h3>Item #".$product->productId." - ".$product->name." - $".
		($product->price)."</h3>";
	$content .="<img id='img_item' src='images/".$product->img."'>";
	$content .="<p>".$product->description."</p>";
	if (isLoggedIn()){
		$cart_form = "";
		include('include/add_cart_form.php');
		$content .= $cart_form;
	}
	return $content;
}
?>
