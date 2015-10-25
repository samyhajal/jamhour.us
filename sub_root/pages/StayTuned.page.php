<?php
class StayTuned extends page
{
	public function Load()
	{
		$this->addCSS("style.css");
	}

	public function  DisplayBody()
	{
		$this->xtemp->parse('MAIN');
		$this->xtemp->out('MAIN');
	}
}

?>
