dojo.addOnLoad(function(){
	JoinUs();
})

function JoinUs()
{
	var join	= dojo.byId('join');
	dojo.connect(join, 'click', function(e){
		e.preventDefault();
		var submit	= required();
		if ( submit == true )
		{
			dojo.byId('frm_join_us').submit();
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
		alert('Please fill out all required information.');
	}
	return filled;
}