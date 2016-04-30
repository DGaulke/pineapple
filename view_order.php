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
$page->title = 'pineapple - order placed';
$page->addCSS(['main.css','table.css']);
$page->setUserLogin(getUserLoginInfo());
$page->addMenuItems(getMenuItems());

if (!isset($_GET['id'])){
    header('Location: view_cart.php');
}

try {
	include_once('include/Order.php');
	/* Load existing order and display */
	$order = Order::existingOrder(isAdmin() ? ["order_id" => $_GET['id']] :
			["order_id" => $_GET['id'],
			"customer_id" => $_SESSION['customer_id']]);
		$page->displayContent(viewOrder($order));
} catch (UnexpectedQueryResultException $ex){
	$page->displayContent("<p>Invalid OrderID!</p>");
} catch (\Exception $ex){
	$page->displayContent($ex->__toString());
}
/* Display $order in a table */
function viewOrder($order){
    $confirmation = '<h1>Order # '.$order->orderId.'</h1>
		<strong>Shipped to:</strong><br>
		<div>'.shippingAddress($order).'</div><br>
		<strong>Charged to:</strong><br>
		<div>'.paymentInfo($order).'</div>';

    include_once('include/Product.php');
    $confirmation .= '<table>
			<tr>
			<th>Product Name</th>
			<th>Category</th>
			<th>Description</th>
			<th>Price</th>
			<th>Quantity</th>
			<th>Total</th>
			</tr>';
    $subtotal = 0;
    foreach ($order->products AS $product) {
        $confirmation .= "<tr>
			<td><a style='color: #0000ff; text-decoration: underline' href='view_product.php?id=$product->productId'>$product->name</a></td>
			<td>$product->categoryName</td>
			<td>$product->shortDescription</td>
			<td>$product->price</td>
			<td>$product->purchasedQuantity</td>
			<td>$".($product->price * $product->purchasedQuantity)."</td>
			</tr>";
        $subtotal += ($product->price * $product->purchasedQuantity);
    }
    $confirmation .= '<tr><td>Shipping Cost:</td><td></td><td></td><td></td><td></td><td>$'.Order::shippingCost.'</td></tr>
    		<tr><td><strong>Total:</strong></td><td></td><td></td><td></td><td></td><td><strong>$'.($subtotal + Order::shippingCost).'</strong></td></tr>
			</table>';

    return $confirmation;
}


function shippingAddress($order){
    return $order->shipAddr1.(strlen($order->shipAddr2) > 0 ? ', '.$order->shipAddr2 : '').'<br>'.$order->shipCity.', '.$order->shipState.' '.$order->shipZip;
}
function paymentInfo($order) {
    return $order->nameOnCard.'<br>'.$order->cardType.' - XXXXXXXXXXXX'.substr($order->cardNumber, 12).'<br>Exp. '.$order->expMonth.'/'.$order->expYear;
}
function emptyShoppingCart(){
	$_SESSION['cart'] = array();
}
?>
