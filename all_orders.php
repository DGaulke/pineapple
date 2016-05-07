<?php namespace pineapple;
/**
 * David Gaulke
 * 2/21/2015
 * This page displays all of the user's order history.
 */
session_start();
include_once('include/functions.php');

authorizeAdmin();

include_once('include/SimplePage.php');
$page = new SimplePage();
$page->title = 'pineapple - view orders';
$page->addCSS(['main.css', 'table.css']);
$page->setUserLogin(getUserLoginInfo());
$page->addMenuItems(getMenuItems());



include_once('include/Order.php');
/* Retrieve array of all orders from the database */
$orders = Order::allOrders();

$page->displayContent(count($orders) === 0 ? '<p>No orders.</p>' :
        ordersTable($orders));

/* Iterate through $orders and compile in a table */
function ordersTable($orders){
    $table = '<strong>All Orders:</strong>
            <table>
			<tr>
			<th>Customer Name</th>
			<th>Order Number</th>
			<th>Order Date</th>
			<th>Number of Items</th>
			<th>Total Cost</th>
			</tr>';
    foreach ($orders as $order) {
        $table .= "<tr><td><a style='color: #0000ff; text-decoration: underline' href='view_order.php?id=$order->orderId'>$order->orderId</a></td>
            <td>".$order->customer->firstName." ".$order->customer->lastName."</td>
            <td>".date("m/d/Y", strtotime($order->orderDate))."</td>
            <td>$order->itemCount</td>
            <td>$$order->total</td></tr>";
    }
    $table .= '</table>';
    return $table;
}
?>




