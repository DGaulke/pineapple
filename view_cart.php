<?php namespace pineapple;
/**
 * David Gaulke
 * 2/21/2015
 * This page displays all of the user's order history.
 */
session_start();
include_once('include/functions.php');

authenticate();

include_once('include/SimplePage.php');
$page = new SimplePage();
$page->title = 'pineapple - view cart';
$page->addCSS(['main.css','table.css','form.css']);
$page->setUserLogin(getUserLoginInfo());
$page->addMenuItems(getMenuItems());

if (isCheckout()){
	header('Location: checkout.php');
} if (isItemAdded()){
	addToCart($_POST['product_id'], $_POST['quantity']);
} elseif(isCartUpdated()){
	updateQuantities();
}

$page->displayContent(viewCart());

function viewCart(){
	$cart = '<strong>Shopping Cart:</strong>';

	if (!isset($_SESSION['cart']) || count($_SESSION['cart']) === 0){
		$cart .= "<p>No items.</p>";
	} else {
		include_once('include/Product.php');
		$cart .= '<form action="'.$_SERVER['PHP_SELF'].'" method="post"
			id="update">
			<table>
			<tr>
			<th>Product Name</th>
			<th>Category</th>
			<th>Description</th>
			<th>Price</th>
			<th>Quantity</th>
			<th>Total</th>
			</tr>';
		$subtotal = 0;
		foreach ($_SESSION['cart'] AS $productId => $quantity) {
			$product = Product::existingProduct(['product_id' => $productId]);
			$cart .= "<tr>
			<td>$product->name</td>
			<td>$product->categoryName</td>
			<td>$product->shortDescription</td>
			<td>$product->price</td>
			<td><input type='number' name='$product->productId'
					class='quantity' value='$quantity' min='0'
					max='$product->quantity'></td>
			<td>$".($product->price * $quantity)."</td>
			</tr>";
			$subtotal += ($product->price * $quantity);
		}
		$cart .= '<tr><td><strong>Subtotal:</strong></td><td></td><td></td>'.
				'<td></td><td></td><td><strong>$'.$subtotal.'</strong></td>'.
				'</tr></table>'.
				'<input type="submit" name="update" value="update"><br>'.
				'<input type="submit" name="checkout" value="checkout">'.
				'</form>';

	}
	return $cart;
}
/* If new quantities were posted, update $_SESSION */
function updateQuantities(){
	foreach($_SESSION['cart'] AS $productId => $quantity){
		if (isset($_POST[$productId])){
			if (intval($_POST[$productId]) === 0){
				unset($_SESSION['cart'][$productId]);
			} else {
				$_SESSION['cart'][$productId] = $_POST[$productId];
			}
		}
	}
}

function isCheckout(){
	return isset($_POST['checkout']);
}

function isItemAdded(){
	return isset($_POST['add']) && $_POST['product_id'] > 0 && $_POST['quantity'] > 0;
}
function isCartUpdated(){
	return isset($_POST['update']);
}

function addToCart($productId, $quantity){
	if (!isset($_SESSION['cart'])){
		$_SESSION['cart'] = array();
	}
	$_SESSION['cart'][$productId] = $quantity + existingQuantity($productId);
}

function existingQuantity($productId){
	return isset($_SESSION['cart'][$productId]) ? $_SESSION['cart'][$productId] : 0;
}

?>




