<?php
class Missing extends page
{
	function  DisplayBody()
	{
		$this->xtemp->parse('MAIN');
		$this->xtemp->out('MAIN');
	}

	function  Load()
	{
		$this->addCSS('style.css');
	}
}
?>