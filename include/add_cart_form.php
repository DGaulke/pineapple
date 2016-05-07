<?php
/* Form for adding product to cart */
$cart_form = '<form action="view_cart.php" method="post" id="add_cart">
	<input type="hidden" name="product_id" id="product_id" value="'.$product->productId.'" required>
	<input type="number" name="quantity" id="quantity" value="1" min="1" max="'.$product->quantity.'" required>
	<br>

	<input type="submit" name="add" value="Add to Cart">
	</form>';
?>
