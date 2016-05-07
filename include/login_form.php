<?php
/* Form for site login */
$login_form = '<form action="login.php" method="post" id="login" onsubmit="return isFormValid(this)">
	<fieldset>
		<img src="images/profile.png" id="profile">
		<label for="login_id">Login ID:</label>
		<input type="text" name="login_id" id="login_id" placeholder="Login ID" onblur="validate(this)" required>
		<span id="loginIdError" class="invalid"></span>
		<br>

		<label for="password">Password:</label>
		<input type="password" name="password" id="password" placeholder="********" onblur="validate(this)" required>
		<span id="passwordError" class="invalid"></span>
		<br>

		<input type="submit" name="login" value="Log In">
		<span id="register"><a href="register.php">New Customer?</a></span>
		<br>' .((isset($failed_login) && $failed_login) ?
				"<span class='note'>*Invalid Login ID or Password</span>" : "").
'</fieldset>
</form>';
?>
