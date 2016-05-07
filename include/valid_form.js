/* Check if the given element's input is valid */
function validate(element){
	updateDisplay(element, getStatus(element));
	/* Check both password fields in sync */
	if (isPasswordElement(element))
		updateDisplay(passwordComplement(element));
}
/* Check if all required fields have valid input */
function isFormValid(form) {
	var required = requiredElements(form.id);
	for (var i = 0; i < required.length; i++)
		if (getStatus(required[i]) !== "")
			return false;
	return true;
}
/* Returns an array of inputs that are required to register */
function requiredElements(formID){
	var inputs = document.getElementById(formID);
	var required = [];
	for (var i = 0; i < inputs.length; i++)
		if (isRequired(inputs[i]))
			required[required.length] = inputs[i];
	return required;
}
/* Get the status of an element.  Returns blank if valid */
function getStatus(element){
	switch(element.getAttribute("id")){
		case "addr1": return !isAlphaNumeric(element) ? "Address must be alphanumeric" : "";
		case "addr2": return !isAlphaNumeric(element) ? "Address must be alphanumeric" : "";
		case "city": return !isAlphaNumeric(element) ? "Address must be alphanumeric" : "";
		case "zip": return !isZip(element) ? "Invalid Zip Code" : "";
		case "nameOnCard": return isAlphaNumeric("nameOnCard") ? "Name must be alphanumeric" : "";
		case "cardType": return isValidCardType(element) ? "Invalid Card Type" : "";
		case "cardNumber": return isCreditCardNumber(element) ? "Invalid Card Number" : "";
		case "cvv": return isCVV(element) ? "Invalid CVV" : "";
		case "first_name": return !isAlphaNumeric(element) || !isAlphaNumeric(document.getElementById("last_name")) ? "Name must be alphanumeric;" : isEmpty(element) ? "Name is required;" : "";
		case "last_name": return isEmpty(element) || isEmpty(document.getElementById("first_name")) ? "Name is required;" :
				!isAlphaNumeric(element) || !isAlphaNumeric(document.getElementById("first_name"))? "Name must be alphanumeric": "";
		case "login_id": return isEmpty(element) ? "Login is required;" : !isAlphaNumeric(element) ? "Login must be alphanumeric;" : "";
		case "dob": return isEmpty(element) || isDate(element) ? "" : "Please enter a valid date (i.e. mm/dd/yyyy);";
		case "email": return isEmail(element) ? "" : "Please enter a valid email address;";
		case "phone": return isEmpty(element) || isPhone(element) ? "" : "Please enter phone number in ###-###-#### format;";
		case "source": return !isEmpty(element) ? "" : "Please choose an option;";
		case "password":
		case "confirmPassword":
				var output = isPasswordValid(element) ? "" : "Password must be 8 alphanumeric characters or longer;";
				if (bothPasswordsEntered() && !passwordsMatch())
					output += "Passwords do not match;";
				return output;
		default: return "Element unknown;";
	}
}
/* Update element attribute to show or hide error messages */
function updateDisplay(element){
	var displayId = element.getAttribute("id").replace("first_name", "name").replace("last_name", "name") + "Error";
	var display = document.getElementById(displayId);
	var status = getStatus(element);
	display.style.visibility = (status.length === 0 ? "hidden" : "visible");
	display.innerHTML = status;
}
/* Check if element input is empty */
function isEmpty(element){
	return element.value.length === 0;
}
/* Check if element is required */
function isRequired(element){
	return element.getAttribute("required") !== null;
}
function isValidCardType(){
	return element.value == "VISA" || element.value == "MasterCard";
}
/* Check if element matches a regex */
function matchesPattern(element, pattern){
	return element.value.match(pattern) !== null;
}
/* Check if element is valid phone format */
function isPhone(element){
	return matchesPattern(element, /^\d{3}\-\d{3}\-\d{4}$/);
}
function isZip(element){
	return matchesPattern(element,/^\d{5}(\-?\d{4})?$/);
}
function isCreditCardNumber(element) {
	return matchesPattern(element,/^\d{16}$/);
}
function isCVV(element){
	return matchesPattern(element,/^\d{3}$/);
}
/* Check if element is valid date format */
function isDate(element){
	return matchesPattern(element, /^\d{1,2}((\-\d{1,2}\-)|(\/\d{1,2}\/))(\d{2}|\d{4})$/);
}
/* Check if element is valid email format */
function isEmail(element){
	return matchesPattern(element,/[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?/);
}
/* Check if element is alphanumeric */
function isAlphaNumeric(element){
	return matchesPattern(element,/^[\'\-\_\:\;\,\s\w]*$/);
}
/* Check if element is valid password format*/
function isPasswordValid(element){
	return isEmpty(element) || (element.value.length >= 8 && isAlphaNumeric(element));
}
/* Check if element is a password input */
function isPasswordElement(element){
	return element.type.toLowerCase() === 'password';
}
/* Get the other password element */
function passwordComplement(element) {
	return element.getAttribute("id") === "password" ?
			document.getElementById("confirmPassword") :
			document.getElementById("password");
}
/* Check if password inputs are equal */
function passwordsMatch(){
	return document.getElementById("password").value ===
			document.getElementById("confirmPassword").value;
}
/* Check if element is valid */
function bothPasswordsEntered(){
	return document.getElementById("password").value.length > 0 &&
		document.getElementById("confirmPassword") !== null &&
			document.getElementById("confirmPassword").value.length > 0;
}
function updateAddress(){
	if(document.getElementById("sameAddress").checked){
		document.getElementById("b_addr1").value = document.getElementById("addr1").value;
		document.getElementById("b_addr1").disabled = true;
		document.getElementById("b_addr2").value = document.getElementById("addr2").value;
		document.getElementById("b_addr2").disabled = true;
		document.getElementById("b_city").value = document.getElementById("city").value;
		document.getElementById("b_city").disabled = true;
		document.getElementById("b_state").value = document.getElementById("state").value;
		document.getElementById("b_state").disabled = true;
		document.getElementById("b_zip").value = document.getElementById("zip").value;
		document.getElementById("b_zip").disabled = true;
	} else {
		document.getElementById("b_addr1").value = "";
		document.getElementById("b_addr1").disabled = false;
		document.getElementById("b_addr2").value = "";
		document.getElementById("b_addr2").disabled = false;
		document.getElementById("b_city").value = "";
		document.getElementById("b_city").disabled = false;
		document.getElementById("b_state").value = "";
		document.getElementById("b_state").disabled = false;
		document.getElementById("b_zip").value = "";
		document.getElementById("b_zip").disabled = false;
	}
}