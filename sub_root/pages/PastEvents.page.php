<?php
class PastEvents extends page
{
	function  DisplayBody()
	{
		$this->xtemp->parse('MAIN');
		$this->xtemp->out('MAIN');
	}

	function  Load()
	{
		$this->addCSS("style.css");
		$this->addCSS('past.css');
		$this->addCSS('highslide.css');
		$this->addJS('dojo.js');
		$this->addJS('ajax.js');
		$this->addJS('highslide-with-gallery.js');
		$this->cur_pg	= array('events', 'past');
	}

	function Bio($id)
	{
		$this->Display();
	}

	function GetEventImages($block)
	{
		$this->xtemp->assign('IMAGE_PATH', __PATH_IMAGES__);
		$this->xtemp->parse($block);
		$this->xtemp->out($block);
	}
}
?>