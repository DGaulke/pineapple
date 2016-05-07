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

if (!isset($_POST['order']) || sizeof($_SESSION['cart']) ===0){
    header('Location: view_cart.php');
}

try {
	/* Create and persist order data */
	include_once('include/Order.php');
	$order = Order::newOrder(["orderDate" => date('Y-m-d H:i:s'),
			"customer" => $_SESSION['customer_id'],
			"shipAddr1" => $_SESSION['checkout']['addr1'],
			"shipAddr2" => $_SESSION['checkout']['addr2'],
			"shipCity" => $_SESSION['checkout']['city'],
			"shipState" => $_SESSION['checkout']['state'],
			"shipZip" => $_SESSION['checkout']['zip'],
			"nameOnCard" => $_SESSION['checkout']['nameOnCard'],
			"cardType" => $_SESSION['checkout']['cardType'],
			"cardNumber" => $_SESSION['checkout']['cardNumber'],
			"expMonth" => $_SESSION['checkout']['expMonth'],
			"expYear" => $_SESSION['checkout']['expYear'],
			"securityCode" => $_SESSION['checkout']['cvv'],
			"billAddr1" => $_SESSION['checkout']['b_addr1'],
			"billAddr2" => $_SESSION['checkout']['b_addr2'],
			"billCity" => $_SESSION['checkout']['b_city'],
			"billState" => $_SESSION['checkout']['b_state'],
			"billZip" => $_SESSION['checkout']['b_zip'],
			"products" => $_SESSION['cart']]);
	emptyShoppingCart();
	$page->displayContent(orderConfirmation($order));
} catch (\Exception $ex){
	$page->displayContent($ex->__toString());
}

function orderConfirmation($order){
    $confirmation = '<h1>Order Placed! Confirmation # '.$order->orderId.'</h1>
		<strong>Shipping to:</strong><br>
		<div>'.$order->shippingAddress.'</div><br>
		<strong>Charged to:</strong><br>
		<div>'.$order->paymentInfo.'</div>';

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
			<td><a style='color: #0000ff; text-decoration: underline'
					href='view_product.php?id=$product->productId'>
					$product->name</a></td>
			<td>$product->categoryName</td>
			<td>$product->shortDescription</td>
			<td>$product->price</td>
			<td>$product->purchasedQuantity</td>
			<td>$".($product->price * $product->purchasedQuantity)."</td>
			</tr>";
        $subtotal += ($product->price * $product->purchasedQuantity);
    }
    $confirmation .= '<tr><td>Shipping Cost:</td><td></td><td></td><td></td>'.
			'<td></td><td>$'.Order::shippingCost.'</td></tr><tr><td><strong>'.
			'Total:</strong></td><td></td><td></td><td></td><td></td><td>'.
			'<strong>$'.($subtotal + Order::shippingCost).'</strong></td>'.
			'</tr></table>';

    return $confirmation;
}



function paymentInfo($order) {
    return $order->nameOnCard.'<br>'.$order->cardType.' - XXXXXXXXXXXX'.substr($order->cardNumber, 12).'<br>Exp. '.$order->expMonth.'/'.$order->expYear;
}
function emptyShoppingCart(){
	unset($_SESSION['cart']);
	unset($_SESSION['checkout']);
}
?>
