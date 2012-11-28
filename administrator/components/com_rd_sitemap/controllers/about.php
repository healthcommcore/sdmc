<?php
/**
 * Run Digital Sitemap
 * @author Robert Deutz (email contact@rdbs.net / site www.rdbs.de)
 * @version $Id: about.php 10 2007-08-24 14:19:42Z deutz $
 *
 * @copyright Copyright (C) 2005-2007 run-digital
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 *
 * This is free software
 **/

defined( 'RDCOMPONENT' ) or die( 'Restricted access' );

/**
 * class RdSitemapControllerAbout
 *
 * @package RD_Sitemap
 */
class RdSitemapControllerAbout extends RdController
{
	/**
	 * Display the list of viewset
	 */
	function display()
	{
		require_once(JPATH_COMPONENT.DS.'views'.DS.'about.php');
		RdSitemapViewAbout::display( );
	}

	function cancel ()
	{
		$this->setRedirect( 'index.php?option=com_rd_sitemap' );
	}

}
?>
