<?php
class Home extends page
{
	
	function  DisplayBody()
	{
		$this->xtemp->parse('MAIN');
		$this->xtemp->out('MAIN');
	}

	function  Load()
	{
		$this->addCSS("style.css");
		$this->addCSS('home.css');
		$this->addJS('dojo.js');
		$this->addJS('home.js');
		$this->cur_pg	= 'home';
		$this->footer_images[]	= 'rotation_boxing.jpg';
		$this->footer_images[]	= 'rotation_cafeteria.jpg';
		$this->footer_images[]	= 'rotation_chapel.jpg';
		$this->footer_images[]	= 'rotation_choir.jpg';
		$this->footer_images[]	= 'rotation_church.jpg';
		$this->footer_images[]	= 'rotation_church2.jpg';
		$this->footer_images[]	= 'rotation_clapping.jpg';
		$this->footer_images[]	= 'rotation_class_pic.jpg';
		$this->footer_images[]	= 'rotation_classroom.jpg';
		$this->footer_images[]	= 'rotation_flags.jpg';
		$this->footer_images[]	= 'rotation_fundraising.jpg';
		$this->footer_images[]	= 'rotation_halloween.jpg';
		$this->footer_images[]	= 'rotation_jumping1.jpg';
		$this->footer_images[]	= 'rotation_jumping2.jpg';
		$this->footer_images[]	= 'rotation_karate.jpg';
		$this->footer_images[]	= 'rotation_library.jpg';
		$this->footer_images[]	= 'rotation_market.jpg';
		$this->footer_images[]	= 'rotation_old_bus.jpg';
		$this->footer_images[]	= 'rotation_piano.jpg';
		$this->footer_images[]	= 'rotation_pool1.jpg';
		$this->footer_images[]	= 'rotation_pool2.jpg';
		$this->footer_images[]	= 'rotation_promo1.jpg';
		$this->footer_images[]	= 'rotation_promo2.jpg';
		$this->footer_images[]	= 'rotation_promo3.jpg';
		$this->footer_images[]	= 'rotation_sheikh.jpg';
		$this->footer_images[]	= 'rotation_singing2.jpg';
		$this->footer_images[]	= 'rotation_sports_basketball.jpg';
		$this->footer_images[]	= 'rotation_sports_blueteam.jpg';
		$this->footer_images[]	= 'rotation_sports_yellowteam.jpg';
		$this->footer_images[]	= 'rotation_uniform.jpg';
		$this->footer_images[]	= 'rotation_venue.jpg';
		$this->footer_images[]	= 'rotation_violin.jpg';
		$this->footer_images[]	= 'rotation_yearend1.jpg';
		$this->footer_images[]	= 'rotation_yearend2.jpg';
		$this->footer_images[]	= 'rotation_yearend3.jpg';
		$this->footer_images[]	= 'rotation_yearend4.jpg';
		$this->footer_images[]	= 'rotation_yearend5.jpg';
		$this->footer_images[]	= 'rotation_yearend7.jpg';
	}
}

?>
