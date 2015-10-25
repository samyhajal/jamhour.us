<?php
class Meta
{
	public function Display($scripts,$css)
	{
		$xtemp	= new XTemplate("meta.html",__PATH_HTML__);
		$xtemp->assign("SCRIPT_ROOT",__PATH_JAVASCRIPT__);
		$xtemp->assign("STYLE_ROOT",__PATH_CSS__);
		foreach ( $scripts as $script )
		{
			$xtemp->assign("FILE",$script);
			$xtemp->parse("MAIN.JAVASCRIPT");
		}
		foreach ( $css as $stylesheet)
		{
			$xtemp->assign("FILE",$stylesheet);
			$xtemp->parse("MAIN.STYLESHEET");
		}
		$xtemp->parse("MAIN");
		$xtemp->out("MAIN");
	}
}
?>