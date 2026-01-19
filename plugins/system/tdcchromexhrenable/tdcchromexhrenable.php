<?php
// no direct access
defined( '_JEXEC' ) or die;

class plgSystemTdcchromexhrenable extends JPlugin
{
	/**
	 * Load the language file on instantiation. Note this is only available in Joomla 3.1 and higher.
	 * If you want to support 3.0 series you must override the constructor
	 *
	 * @var    boolean
	 * @since  3.1
	 */
	protected $autoloadLanguage = true;

	/**
	 * Plugin method with the same name as the event will be called automatically.
	 */
	 public function onBeforeCompileHead()
		{
		   $document = JFactory::getDocument();
		   $document->setMetaData('origin-trial', 'AgNhvvH0BL+TkgK1opH4u9eKRm8pnBHnpEfUV19I+8DuYG/s9P/mroxxa6Ym7OB+UfHBOEaahHqzEfNAJgcWyQ4AAACAeyJvcmlnaW4iOiJodHRwczovL3d3dy5kZW50YWwtY2hhbm5lbC5jby51azo0NDMiLCJmZWF0dXJlIjoiQWxsb3dTeW5jWEhSSW5QYWdlRGlzbWlzc2FsIiwiZXhwaXJ5IjoxNjE0MTI0Nzk5LCJpc1N1YmRvbWFpbiI6dHJ1ZX0=','http-equiv');
		}
}
?>