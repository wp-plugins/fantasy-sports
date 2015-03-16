function submitWithdrawalRequest()
{
	var userAmount = $('#withdrawal_amount').val().replace(/^\s\s*/, '').replace(/\s\s*$/, '');
	var userBalance = $('#Balance').text();
	if ( userBalance == "$")
	{
		alert("Please logout and login again. A security warning "
			+ "has been detected\nPlease contact support@fanvictor.com if issue persists");
		return false;
	}
	if ( (String(userAmount).indexOf(".") != -1) && (String(userAmount).indexOf(".") < String(userAmount).length - 3))
	{
		alert("Number can only be 2 decimal places at most");
		return false;
	}
	if ( isNaN(userAmount) )
	{
		alert("Please enter a valid withdrawal amount");
		return false;
	}
	if ( userAmount == "" )
	{
		alert("Please enter an amount to withdrawal");
		return false;
	}
	userBalance = userBalance.substr(1); // remove first character
	if ( parseFloat(userBalance) < parseFloat(userAmount) )
	{
		alert("Withdrawal amount of $" + userAmount +
			" is greater than your balance of $" +
			userBalance + "\n" + "Please enter" +
			" an amount less than $" + userBalance);
		return false;
	}
	// now validate the email address
	var emailRule  = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	var userEmail = $('#paypal_email').val();
	if (! userEmail.match(emailRule))
	{
		alert("Please enter a valid email address");
		return false;
	}
	return true;
}
