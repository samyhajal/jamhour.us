<?php
class Confirmation extends page
{
	function  Load()
	{
		$this->addCSS('style.css');
	}
	function  DisplayBody()
	{
		var_dump2($_GET);
	}

	function Donate()
	{
		$this->xtemp->parse('MAIN');
		$this->xtemp->out('MAIN');
	}
}

?>
