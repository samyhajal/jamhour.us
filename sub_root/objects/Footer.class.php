<?php
class Footer
{
	private	$footer_images	= array();
	public function Display($footer_images = array() )
	{
		$xtemp	= new XTemplate("footer.html",__PATH_HTML__);
		$xtemp->assign("IMAGE_PATH",__PATH_IMAGES__);
		if ( !empty($footer_images) )
		{
			$this->footer_images	= $footer_images;
			$images					= array();
			for ( $i = 0; $i<=4; $i++ )
			{
				$images[$i]	= $this->getFooterImage($images);
			}
			$xtemp->assign('IMAGE_LIST', json_encode($this->footer_images));
			$xtemp->assign('FOOTER_IMG', $images);
			$xtemp->parse('MAIN.PHOTOS');
		}
		$xtemp->assign('YEAR', date('Y'));
		$xtemp->parse("MAIN");
		$xtemp->out("MAIN");
	}

	function getFooterImage($existing)
	{
		$x		= rand(0, sizeof($this->footer_images)-1);
		$ret	= null;
		if ( in_array($this->footer_images[$x], $existing) )
		{
			$ret	= $this->getFooterImage($existing);
		}
		else
		{
			$ret	= $this->footer_images[$x];
		}
		return $ret;
	}
}
?>