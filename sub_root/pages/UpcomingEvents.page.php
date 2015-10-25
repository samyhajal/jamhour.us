<?php
class UpcomingEvents extends page
{
	function  DisplayBody()
	{
		$this->xtemp->parse('MAIN');
		$this->xtemp->out('MAIN');
	}

	function  Load()
	{
		$this->addCSS("style.css");
		$this->addCSS('upcoming.css');
		$this->cur_pg	= array('events', 'upcoming');
	}

	function Bio()
	{
		$this->Load();
		$this->xtemp->parse('BIO');
		$this->DisplayMeta();
		$this->DisplayHeader();
		$this->xtemp->out('BIO');
		$this->DisplayFooter();
	}
}
?>