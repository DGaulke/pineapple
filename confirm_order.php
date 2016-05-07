<?php namespace pineapple;
/**
 * David Gaulke
 * 2/21/2015
 * This page displays all of the user's order history.
 */
session_start();
include_once('include/functions.php');
include_once('include/Order.php');

authenticate();

include_once('include/SimplePage.php');
$page = new SimplePage();
$page->title = 'pineapple - confirm order';
$page->addCSS(['main.css','table.css']);
$page->setUserLogin(getUserLoginInfo());

/* If not accessed via checkout.php, go back to cart */
if (!isset($_POST['next']) || sizeof($_SESSION['cart']) === 0){
	header('Location: view_cart.php');
} else {
/* Else format POST data and save in $_SESSION */
	if ($_POST['sameAddress'] === 'sameAddress'){
		$_POST['b_addr1'] = $_POST['addr1'];
		$_POST['b_addr2'] = $_POST['addr2'];
		$_POST['b_city'] = $_POST['city'];
		$_POST['b_state'] = $_POST['state'];
		$_POST['b_zip'] = $_POST['zip'];
	}
	cleanInput();
	$_SESSION['checkout'] = $_POST;
}

$page->displayContent(orderData());

function orderData(){
	$confirmation = '<h1>Confirm Order:</h1>
		<strong>Ship to:</strong><br>
		<div>'.shippingAddress().'</div><br>
		<strong>Charge to:</strong><br>
		<div>'.paymentInfo().'</div>';

		include_once('include/Product.php');
		$confirmation .= '<form action="confirmation.php" method="post" id="confirm">
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
			$confirmation .= "<tr>
			<td><a style='color: #0000ff; text-decoration: underline' href=
					'view_product.php?id=$product->productId'>
					$product->name</a></td>
			<td>$product->categoryName</td>
			<td>$product->shortDescription</td>
			<td>$product->price</td>
			<td>$quantity</td>
			<td>$".($product->price * $quantity)."</td>
			</tr>";
			$subtotal += ($product->price * $quantity);
		}
		$confirmation .= '<tr><td>Shipping Cost:</td><td></td><td></td><td>'.
				'</td><td></td><td>$'.Order::shippingCost.'</td></tr>'.
				'<tr><td><strong>Total:</strong></td><td></td><td></td><td>'.
				'</td><td></td><td><strong>$'.($subtotal+Order::shippingCost).
				'</strong></td></tr></table>'.
				'<input type="submit" name="order" value="place order">'.
				'</form>';

		return $confirmation;
	}


function shippingAddress(){
	return $_POST['addr1'].(strlen($_POST['addr2']) > 0 ? ', '.$_POST['addr2']
			: '').'<br>'.$_POST['city'].', '.$_POST['state'].' '.$_POST['zip'];
}
function paymentInfo() {
	return $_POST['nameOnCard'].'<br>'.$_POST['cardType'].' - XXXXXXXXXXXX'.
			substr($_POST['cardNumber'], 12).'<br>Exp. '.$_POST['expMonth'].
			'/'.$_POST['expYear'];
}
?>
