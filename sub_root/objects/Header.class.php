<?php
class Header
{
	public function Display($cur_pg = null)
	{
		$xtemp		= new XTemplate("header.html",__PATH_HTML__);
		if ( is_array($cur_pg) )
		{
			$xtemp->assign('MENU_'. $cur_pg[0], 'current');
			$xtemp->assign('SUBMENU_'. $cur_pg[1], 'current');
		}
		else
		{
			$xtemp->assign('MENU_'. $cur_pg, 'current');
		}
		$xtemp->assign("SITE_ROOT",__SITE__);
		$xtemp->assign("IMAGE_PATH",__PATH_IMAGES__);
		$xtemp->parse("MAIN");
		$xtemp->out("MAIN");
	}
}
?>