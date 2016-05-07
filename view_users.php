<?php namespace pineapple;
/**
 * David Gaulke
 * 2/10/2015
 * This page displays all users registered with the site
 */
session_start();
include_once('include/functions.php');

authorizeAdmin();

include_once('include/SimplePage.php');

$page = new SimplePage();
$page->title = 'pineapple - view all users';
$page->addCSS(['main.css','table.css']);
$page->setUserLogin(getUserLoginInfo());
$page->addMenuItems(getMenuItems());

/* Get collection of all registered customers */
include_once('include/Customer.php');
try {
    $customers = Customer::allCustomers();
    $page->displayContent(displayCustomers($customers));
} catch (\Exception $ex){
    $page->displayContent($ex->__toString());
}
/* Display all customers in table */
function displayCustomers($customers){
    $content = "<table>
    <tr>
    <th>UserID</th>
    <th>First Name</th>
    <th>Last Name</th>
    <th>City</th>
    <th>State</th>
    <th>Email</th>
    </tr>";

    foreach($customers as $customer){
        $content .= "<tr>";
        $content .= "<td style='color: #0000ff; text-decoration: underline'><a href='".("edit_user.php?id=$customer->customerId")."'>".$customer->loginId."</a></td>";
        $content .= "<td>".$customer->firstName."</td>";
        $content .= "<td>".$customer->lastName."</td>";
        $content .= "<td>".$customer->city."</td>";
        $content .= "<td>".$customer->state."</td>";
        $content .= "<td>".$customer->email."</td>";
        $content .= "</tr>";
    }
    $content .= "</table>";
    return $content;
}
?>

