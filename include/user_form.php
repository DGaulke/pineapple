<?php
/* Form for site registration and customer record updates  */
$edit = (\pineapple\isAdmin() && isset($customer));
if ($edit){
	$form = '<form action="update_user.php" method="post" id="update" onsubmit="return isFormValid(this)">
		<fieldset id="customer_info">
		<legend>Customer Information</legend>
		<input type="hidden" name="customer_id" id="customer_id" value="'.$customer->customerId.'">';
} else {
	$form = '<form action="create_user.php" method="post" id="register" onsubmit="return isFormValid(this)">
		<fieldset id="customer_info">
		<legend>Customer Information</legend>';
}

$form .= '<label for="first_name" class="req">Name:</label>
		<input type="text" maxlength="20" name="first_name" id="first_name" '.($edit ? 'value="'.$customer->firstName.'"' : 'placeholder="First"').' onblur="validate(this)" required>
		<label for="last_name"  class="req"></label>
		<input type="text" maxlength="20" name="last_name" id="last_name" '.($edit ? 'value="'.$customer->lastName.'"' : 'placeholder="Last"').' onblur="validate(this)" required>
		<span id="nameError" class="invalid"></span>
		<br>

		<label for="addr1">Address:</label>
		<input type="text" maxlength="30" name="addr1" '.($edit ? 'value="'.$customer->addr1.'"' : 'placeholder="Line 1"').'>
		<br>

		<input type="text" maxlength="30" name="addr2" '.($edit ? 'value="'.$customer->addr2.'"' : 'placeholder="Line 2"').'>
		<br>

		<label for="city">City:</label>
		<input type="text" maxlength="30" name="city" '.($edit ? 'value="'.$customer->city.'"' : 'placeholder="City"').'>


<label for="state">State:</label>
		<select name="state">
			<option label="Choose" value=""'.($edit ? '' : 'selected').'>State</option>';

include_once("Controller.php");
$stateData = pineapple\Controller::getInstance()->query('select state_abrv, state_name from state');
while ($state = $stateData->fetch_assoc()){
	$form .= '<option label="'.$state['state_name'].'" value="'.$state['state_abrv'].'" '.($state['state_abrv'] === $customer->state ? 'selected' :'').'>'.$state['state_name'].' ('.$state['state_abrv'].')</option>';
}

$form .= '</select>

		<label for="zip">Zip Code:</label>
		<input type="text" maxlength="10" name="zip" '.($edit ? 'value="'.$customer->zip.'"' : 'placeholder="Zip Code"').'>
		<br>

		<label for="phone">Phone Number:</label>
		<input type="tel" maxlength="12" name="phone" id="phone" '.($edit ? 'value="'.$customer->phone.'"' : 'placeholder="###-###-####"').' onblur="validate(this)">
		<span id="phoneError" class="invalid"></span>
		<br>

		<label for="dob">Date of Birth:</label>
		<input type="date" maxlength="10" name="dob" id="dob" '.($edit ? 'value="'.date("m/d/Y", strtotime($customer->dob)).'"' : 'placeholder="mm/dd/yyyy"').' onblur="validate(this)">
		<span id="dobError" class="invalid"></span>
		<br>

		<label for="email" class="req">Email Address:</label>
		<input type="email" name="email" id="email" '.($edit ? 'value="'.$customer->email.'"' : 'placeholder="user@domain.com"').' onblur="validate(this)" required>
		<span id="emailError" class="invalid"></span>

	</fieldset>';

	if (!$edit){
		$form .= '<label for="source" class="req" >How did you hear about us?</label>
				<input type="radio" name="source" id="source" value="ICS325" required>ICS325
				<input type="radio" name="source" value="Other">Other
				<br>';
	}

	$form .= '<fieldset id="credentials">
		<legend>Login Information</legend>
		<label for="login_id" class="req">Login ID:</label>
		<input type="text" maxlength="30" name="login_id" id="login_id" '.($edit ? 'value="'.$customer->loginId.'"' : 'placeholder="Login ID"').' onblur="validate(this)" required>
		<span id="loginError" class="invalid"></span>
		<br>

		<label for="password">Password:</label>
		<input type="password" maxlength="50" name="password" id="password" placeholder="********" onblur="validate(this)" '.($edit ? '' : 'required').'>
		<span id="passwordError" class="invalid"></span>
		<br>

		<label for="confirmPassword">Re-enter Password:</label>
		<input type="password" maxlength="50" name="confirmPassword" id="confirmPassword" onblur="validate(this)" placeholder="********" '.($edit ? '' : 'required').'>
		<span id="confirmPasswordError" class="invalid">/span>
	</fieldset>';

if (!$edit){
	$form .= '<label for="comments">Comments</label>
	<br>
	<textarea name="comments" maxlength="4000" rows=3 placeholder="Additional Comments"></textarea>
	<br>';
}

$form .= '<input type="checkbox" name="subscribe" value="subscribe" '.($edit && $customer->subscribe  ? 'checked' : '').'>
	Subscribe to our mailing list
	<br>';

if ($edit){
	$form .= '<input type="checkbox" name="admin" value="admin" '.($customer->admin  ? 'checked' : '').'>
	Give admin priviledges
	<br>';
}

$form .= '<input type="submit" name="submit" value='.($edit ? '"Update"' : '"Register"').'>
</form>
<span class="note">*Indicates Required Field</span>';

if ($edit){
	$editForm = $form;
} else {
	$registerForm = $form;
}

?>
