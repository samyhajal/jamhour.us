<?php
class AboutUs extends page
{
	function  DisplayBody()
	{
		$this->xtemp->parse('MAIN');
		$this->xtemp->out('MAIN');
	}

	function  Load()
	{
		$this->addCSS("style.css");
		$this->addCSS('about.css');
		$this->cur_pg	= 'about';
	}
}

?>
