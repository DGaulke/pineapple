<?php
/* Form to search for customer records */
$searchForm = '<p><strong>Edit User</strong></p>
	<form action="'.$_SERVER['PHP_SELF'].'" method="post" id="select">
		<p>
			Select user from list ---->
			<input type="submit" name="select" value="Go">
		</p>
	</form>
	<p>OR</p>
	<form action="'.$_SERVER['PHP_SELF'].'" method="post" id="search">
		<p>
			Find user where
			<select name="search_field">
				<option value="login_id">LoginId</option>
				<option value="customer_id">CustomerId</option>
			</select> is
			<input type="text" maxlength="30" name="search_value" id="search_value" placeholder="value" required>
			---->
			<input type="submit" name="search" value="Go">
		</p>
	</form>';
?>
