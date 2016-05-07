<?php namespace pineapple;
/**
 * David Gaulke
 * 3/5/2015
 * This is a menu page that displays a list of available baked goods in a
 * given category
 */
session_start();
include_once('include/functions.php');
include_once('include/OptionPage.php');
include_once('include/Product.php');

/* Return to menu page if no id specified */
if (!isset($_GET['id'])){ 
    header('Location: menu.php');
}
/* Get selected Category */
$categoryId = intval($_GET['id']); 
$category = Category::existingCategory(["category_id" => $categoryId]);

$page = new OptionPage();
$page->title = 'pineapple - '.$category->name;
$page->addCSS(['main.css','menu.css']);
$page->setUserLogin(getUserLoginInfo());
$page->addMenuItems(getMenuItems());

/* Add all categories as page options */
include_once('include/Category.php');
$categories = Category::allCategories();
foreach($categories as $category){
    $page->addOption('view_category.php?id=' . $category->categoryId,
            $category->name, $category->description);
}

$products = Product::getProductsByCategory($categoryId);
$products = array_filter($products, function ($p) {return $p->quantity > 0;});

/* Display all products in selected category */
$content = "";
if ($category === null || !$products){
    /* If invalid categoryId was submitted or no products in category */
    $content = "<p>No products found</p>";
} else {
    /* Iterate through products and add links to output for menu */
    foreach ($products as $product){
        $content .= "<a href='view_product.php?id=".
                intval($product->productId)."'>";
        $content .= "<figure>";
        $content .= "<div class='img_box'>";
        $content .= "<img class='img_thumb' src='images/".
                $product->img."'>";
        $content .= "</div>";
        $content .= "<span>";
        $content .= "<strong>".$product->name."</strong> - $".
                $product->price."<br>";
        $content .= $product->shortDescription;
        $content .= "</span>";
        $content .= "</figure>";
        $content .= "</a>";
    }
}
/* Add menu content and display page */
$page->setDetail($content);
$page->display();
?>
