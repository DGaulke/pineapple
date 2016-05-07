<?php
/* Form for adding new product categories */
$form = '<p><strong>Add Product</strong></p>
<form action="'.$_SERVER['PHP_SELF'].'" method="post" id="add_product" enctype="multipart/form-data">
    <fieldset>
        <label for="product_name">Product Name</label>
        <input type="text" maxlength=30 name="product_name" id="product_name" placeholder="Product Name" required>
        <br>

        <label for="category">Category:</label>
        <select name="category">
        <option label="Choose" value="selected">Category</option>';

include_once('Category.php');
$categories = \pineapple\Category::allCategories();
foreach ($categories as $category){
$form .= '<option label="'.$category->name.'" value="'.$category->categoryId.'">'.$category->name.'</option>';
}

$form .= '</select>
        <br>

        <label for="price">Price</label>
        <input type="number" min="0.01" step="0.01" max="10000" name="price" id="price" required>
        <br>

        <label for="short_desc">Short Description</label>
        <input type="text" maxlength=30 name="short_desc" id="short_desc" placeholder="Short Description" required>
        <br>

        <label for="description">Description</label>
        <input type="text" maxlength=50 name="description" id="description" placeholder="Full Description" required>
        <br>

        <input type="hidden" name="MAX_FILE_SIZE" value = "2000000">
        <label for="img">Select Image</label>
        <input type="file" name="img" id="img" required>
        <br>

        <label for="quantity">Quantity</label>
        <input type="number" name="quantity" id="quantity" min="1" required>
        <br>

        <input type="submit" name="submit" value="Add">
        <br>'.(isset($error_message) ? "<span class='note'>*".$error_message."</span>" : "").
'</fieldset>
</form>';

