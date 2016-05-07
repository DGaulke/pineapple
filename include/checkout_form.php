<?php
/* Form for collecting customer address and payment information to check out*/
include_once('include/Customer.php');
$customer = \pineapple\Customer::existingCustomer(['customer_id' => $_SESSION['customer_id']]);
$form = '<form action="confirm_order.php" method="post" id="checkout" onsubmit="return isFormValid(this)">
		<fieldset id="customer_info">
		<legend>Shipping Information</legend>
		<input type="hidden" name="customer_id" id="customer_id" value="'.$customer->customerId.'">
		<label for="first_name">Name:</label>
		<input type="text" name="first_name" id="first_name" value="'.$customer->firstName.'" readonly>
		<label for="last_name"></label>
		<input type="text" name="last_name" id="last_name" value="'.$customer->lastName.'" readonly>
		<br>

		<label for="addr1">Address:</label>
		<input type="text" maxlength="30" name="addr1" id="addr1" value="'.$customer->addr1.'" required>
		<br>

		<input type="text" maxlength="30" name="addr2" id="addr2" value="'.$customer->addr2.'">
		<br>

		<label for="city">City:</label>
		<input type="text" maxlength="30" name="city" id="city" value="'.$customer->city.'" required>


<label for="state">State:</label>
		<select name="state" id="state">
			<option value="">State</option>';

$stateData = \pineapple\Controller::getInstance()->
		query('select state_abrv, state_name from state');
while ($state = $stateData->fetch_assoc()){
	$form .= '<option value="'.$state['state_abrv'].'" '.($state['state_abrv']
			=== $customer->state ? 'selected' :'').'>'.$state['state_name'].
			' ('.$state['state_abrv'].')</option>';
}

$form .= '</select>

		<label for="zip">Zip Code:</label>
		<input type="text" maxlength="10" name="zip" id="zip" value="'.$customer->zip.'" required>
		<br>

	</fieldset>
	<fieldset>
	<legend>Payment Information</legend>
		<label for="cardType" >Card Type:</label>
		<input type="radio" name="cardType" id="cardType" value="VISA" checked required><img src="images/visa.gif">
		<input type="radio" name="cardType" value="MasterCard"><img src="images/mastercard.gif">
		<br>

		<label for="nameOnCard">Name On Card:</label>
		<input type="text" maxlength="60" name="nameOnCard" id="nameOnCard" required>
		<br>

		<label for="cardNumber">Card Number:</label>
		<input type="text" pattern="[0-9]{16}" maxlength="16" name="cardNumber" id="cardNumber" required>
		<br>

		<label for="expMonth">Expiration Date:</label>
		<select name="expMonth">';
		foreach(range(1,12) as $i){
			$form .= '<option value="'.$i.'">'.str_pad($i, 2, "0", STR_PAD_LEFT).'</option>';
		}
		$form .= '
		</select>

		<select name="expYear">';
		foreach(range(0,9) as $i){
			$form .= '<option value="'.intval(date("Y") + $i).'">'.intval(date("Y") + $i).'</option>';
		}
		$form .= '</select>
		<br>

		<label for="cvv">CVV:</label>
		<input type="text" pattern="[0-9]{3}" maxlength="3" name="cvv" id="cvv" required>
		<br>
</fieldset>
<fieldset>
	<legend>Billing Address</legend>
		<input type="checkbox" name="sameAddress" id="sameAddress" value="sameAddress" onclick="updateAddress()">Same as shipping address
		<br>
		<input type="text" maxlength="30" name="b_addr1" id="b_addr1" placeholder="Address Line 1" required>
		<br>

		<input type="text" maxlength="30" name="b_addr2" id="b_addr2" placeholder="Address Line 2">
		<br>

		<label for="b_city">City:</label>
		<input type="text" maxlength="30" name="b_city" id="b_city" placeholder="City" required>


<label for="b_state">State:</label>
		<select name="b_state" id="b_state">
			<option value="">State</option>';

$stateData = \pineapple\Controller::getInstance()->
		query('select state_abrv, state_name from state');
while ($state = $stateData->fetch_assoc()){
	$form .= '<option value="'.$state['state_abrv'].'" 	>'.
			$state['state_name'].' ('.$state['state_abrv'].')</option>';
}

$form .= '</select>

		<label for="b_zip">Zip Code:</label>
		<input type="text" maxlength="10" name="b_zip" id="b_zip" placeholder="Zip" required>
		<br>

	</fieldset>

	<input type="submit" name="next" value="next">
</form>';

?>
