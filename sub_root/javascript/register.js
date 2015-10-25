dojo.addOnLoad(function(){
	Register();
});

function Register()
{
	var tickets	= dojo.query('.ticket');
	dojo.forEach(tickets, function(item, i){
		dojo.connect(item, 'change', function(e){
			e.preventDefault();
			var total_val = 0;
			var val;
			var count;
			tickets.forEach(function(k){
				val			= Number(dojo.byId(k.id +'_val').value);
				count		= Number(k.value);
				total_val	= total_val + (val*count);
			})
			dojo.byId('total').value	= total_val;
		})
	});

	var choice	= dojo.byId('payment_method');
	dojo.connect(choice, 'change', function(e){
		e.preventDefault();
		if ( choice.value == 1 )
		{
			alert("After filling out the information to the right and clicking Register you will be redirected to the PayPal website.");
		}
		else if ( choice.value == 2 )
		{
			alert("After filling out the information to the right and clicking Register you will be redirected to a page with information regarding where to send your check.");
		}
	})

	var frm_register	= dojo.byId('frm_registration');
	dojo.connect(frm_register, 'submit', function(e){
		e.preventDefault();
		var total		= Number(dojo.byId('total').value);
		var choice_val	= dojo.byId('payment_method').value;
		var email		= dojo.byId('email').value;
		var	action		= null;
		var	submit		= true;
		var guests		= null;
		tickets.forEach(function(item, i){
			guests	+= Number(item.value);
		})
		if ( !(total > 0) )
		{
			alert('Please enter ticket quantity.');
			return;
		}
		if ( choice_val == 1 )
		{
			action	= '/Register/Process/';
		}
		else if ( choice_val == 2 )
		{
			action	= '/Register/Process/ByCheck/';
		}
		else if ( choice_val == 0 )
		{
			alert('Please select a payment method before continuing.');
			return;
		}

		var submit	= required();
		if ( submit == true )
		{
			var patt	= /^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/;
			var match	= false;
			var msg		= null;
			dojo.some(dojo.query('input[name="guest_email[]"]'), function(item, i){
				if ( i < (guests-1) )
				{
					if ( (item.value.length > 0) )
					{
						if ( patt.test(item.value) == false )
						{
							submit	= false;
							msg		= "One of you guest's email address is incorrect format, please check and correct them.";
						}
					}
				}
			});
			if ( submit == false )
			{
				alert(msg);
			}
		}
		if ( submit == true )
		{
			dojo.attr(frm_register, 'action', action);
			frm_register.submit();
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