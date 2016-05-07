<?php
/* Form for adding new product categories */
$form = '<p><strong>Add Product Category</strong></p>
	<form action="'.$_SERVER['PHP_SELF'].'" method="post" id="add_category">
		<fieldset>
			<label for="category_name">Category Name</label>
			<input type="text" maxlength=20 name="category_name" id="category_name" placeholder="Category Name" required>
			<br>

			<label for="category_desc">Category Description</label>
			<input type="text" maxlength=50 name="category_desc" id="category_desc" placeholder="Category Description" required>
			<br>
			<input type="submit" name="submit" value="Add">
			<br>'.(isset($error_message) ? "<span class='note'>*".$error_message."</span>" : "").
			'</fieldset>
	</form>';

