var images	= new Array();
var t;
var path;
var the_four;
dojo.addOnLoad(function(){
	getImageList();
})

function getImageList()
{
	var four = new Array();
	path	= dojo.attr('image_root', 'value');
	images_list	= eval( '{'+ dojo.attr('image_list', 'value') +'}');
	for ( var i=0; i<images_list.length; i++ )
	{
		images[i]		= new Image();
		images[i].src	= path + images_list[i];
	}

	four	= generateFour(four)
	t	= setTimeout( 'swapImages()', 5000);
}
function generateFour(tmp_four)
{
	the_four	= ( typeof tmp_four == 'undefined' ) ? new Array() : tmp_four;
	var i		= Math.floor(Math.random()*(images.length));
	var free	= true;
	for ( var x=0; x<the_four.length; x++ )
	{
		if ( the_four[x] == i )
		{
			free = false;
		}
	}
	if (the_four.length<5 )
	{
		if ( free )
		{
			the_four.push(i);
		}
		the_four	= generateFour(the_four);
	}
	else
	{
		return the_four;
	}
	return the_four;
}
function swapImages()
{
	/**
	 * Assign the footer images new sources
	 */
	dojo.byId('footer_img_0').src	= images[the_four[0]].src;
	dojo.byId('footer_img_1').src	= images[the_four[1]].src;
	dojo.byId('footer_img_2').src	= images[the_four[2]].src;
	dojo.byId('footer_img_3').src	= images[the_four[3]].src;
	dojo.byId('footer_img_4').src	= images[the_four[4]].src;
	/**
	 * Generate 4 new indexes
	 */
	the_four	= generateFour();
	/**
	 * Set timeout for swap images to occur every 5 seconds (5000miliseconds)
	 */
	t	= setTimeout( 'swapImages()', 5000 );
}