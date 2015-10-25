dojo.addOnLoad(function(){
	Donate();
});

function Donate()
{
	var register	= dojo.byId('register_too');
	var choice		= dojo.byId('payment_method');
	dojo.connect(choice, 'change', function(e){
		e.preventDefault();
		if ( choice.value == 1 )
		{
			alert("After filling out the information to the right and clicking Donate you will be redirected to the PayPal website.");
		}
		else if ( choice.value == 2 )
		{
			alert("After filling out the information to the right and clicking Donate you will be redirected to a page with information regarding where to send your check.");
		}
	})
	var frm_donate	= dojo.byId('frm_donate');
	dojo.connect(dojo.byId('donate'), 'click', function(e){
		e.preventDefault();
		var form_submit	= false;
		if ( register.checked == true )
		{
			form_submit	= required();
		}
		else
		{
			form_submit	= true;
		}
		if ( form_submit == true )
		{
			var choice_val	= dojo.byId('payment_method').value;
			if ( choice_val == 1 )
			{
				dojo.attr(frm_donate, 'action', '/Donate/Process/');
				frm_donate.submit();
			}
			else if ( choice_val == 2 )
			{
				dojo.attr(frm_donate, 'action', '/Donate/Process/ByCheck/');
				frm_donate.submit();
			}
			else if ( choice_val == 0 )
			{
				alert('Please select a payment method before continuing.');
			}
		}
	})

	
	dojo.connect(register, 'change', function(e){
		if ( register.checked == true )
		{
			dojo.query('#donate_right input, #donate_right select').forEach(function(item, i){
				dojo.removeAttr(item, 'disabled');
				dojo.byId('last_name').focus();
			})
		}
		else
		{
			dojo.query('#donate_right input, #donate_right select').forEach(function(item, i){
				dojo.attr(item, 'disabled', 'disabled');
			})
		}
	})
}

function required()
{
	var patt	= /^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/;
	var filled	= true;
	dojo.query('.required').forEach(function(item, i){
		if ( item.value.length <= 0 )
		{
			filled	= false;
			return;
		}
	})
	if ( filled == true )
	{
		filled	= patt.test(dojo.byId('email').value);
		if ( filled == false )
		{
			alert('The email address entered in an invalid format. Please check the address entered.');
		}
	}
	else
	{
		alert('Please enter the details on the right prior to proceeding to payment.');
	}
	return filled;
}