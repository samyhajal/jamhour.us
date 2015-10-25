<?php
abstract class page
{
	protected	$xtemp;
	protected	$title;
	protected	$cur_pg	= null;
	protected	$cur_menu;
	protected	$footer_images	= array();
	private 	$css = array();
	private		$scripts = array();
	public 		$require_login = true;

	/**
	 * Public constructor for page object
	 * @final
	 *
	 */
	final public function __construct()
	{
		$this->xtemp	= new XTemplate(get_class($this) . ".html",__PATH_HTML__);
		$this->Load();
	}

	/**
	 * Abstract function to display the body of the page
	 *
	 */
	abstract protected function DisplayBody();

	/**
	 * Abstract function to preload any properties
	 *
	 */
	abstract protected function Load();

	/**
	 * Displays the page object meta, header, body, then footer
	 * @final
	 */
	final public function Display()
	{
		$this->DisplayMeta();
		$this->DisplayHeader();
		$this->DisplayBody();
		$this->DisplayFooter();
	}

	/**
	 * Display the HTML meta section
	 * @final
	 */
	final protected function DisplayMeta()
	{
		$meta	= new Meta();
		$meta->Display($this->scripts,$this->css);
	}

	/**
	 * Display the HTML header file
	 * @final
	 */
	final protected function DisplayHeader()
	{
		$header	= new Header();
		$header->Display($this->cur_pg);
	}

	/**
	 * Display the HTML footer file
	 * @final
	 */
	final protected function DisplayFooter()
	{
		$footer	= new Footer();
		$footer->Display($this->footer_images);
	}

	/**
	 * Adds a JS file to be displayed in the meta
	 *
	 * @param string $script Name of the JS file to include
	 * @final
	 */
	final protected function addJS($script)
	{
		$this->scripts[]	= $script;
	}

	/**
	 * Adds a CSS file to be displayed in the meta
	 *
	 * @param string $css Name of the JS file to include
	 * @final
	 */
	final protected function addCSS($css)
	{
		$this->css[]		= $css;
	}

	/**
	 *Assign a value to protected variable xtemp
	 * @param string $var
	 * @param string|int $val
	 */
	final public function assign($var, $val)
	{
		$this->xtemp->assign($var, $val);
	}
}
?>