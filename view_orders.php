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
$page->title = 'pineapple - view orders';
$page->addCSS(['main.css', 'table.css']);
$page->setUserLogin(getUserLoginInfo());
$page->addMenuItems(getMenuItems());




include_once('include/Order.php');
$orders = Order::getOrdersByCustomer($_SESSION['customer_id']);
$page->displayContent(count($orders) === 0 ? "<p>No orders.</p>" : displayOrders($orders));

function displayOrders($orders){
    $content = '<strong>My Orders:</strong>
            <table>
			<tr>
			<th>Order Number</th>
			<th>Order Date</th>
			<th>Number of Items</th>
			<th>Total Cost</th>
			</tr>';
    foreach ($orders as $order) {
        $content .= "<tr><td><a style='color: #0000ff; text-decoration: underline' href='view_order.php?id=$order->orderId'>$order->orderId</a></td>
            <td>".date("m/d/Y", strtotime($order->orderDate))."</td>
            <td>$order->itemCount</td>
            <td>$$order->total</td></tr>";
    }
    $content .= '</table>';
    return $content;
}
?>




