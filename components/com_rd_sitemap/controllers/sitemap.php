<?php
/**
 * Run Digital Sitemap
 * @author Robert Deutz (email contact@rdbs.net / site www.rdbs.de)
 * @version $Id: sitemap.php 10 2007-08-24 14:19:42Z deutz $
 *
 * @copyright Copyright (C) 2005-2007 run-digital
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 *
 * This is free software
 **/

defined( 'RDCOMPONENT' ) or die( 'Restricted access' );
/**
 * class RdSitemapModelSitemap
 *
 *  @package RD_Sitemap
 */
class RdSitemapControllerSitemap extends RdController
{
	/**
	 * Constructor
	 */
	function __construct( $config = array() )
	{
		parent::__construct( $config );
		$this->registerTask( 'view',	'display' );

	}

	/**
	 * Display the Sitemap
	 */
	function display()
	{
		// load the model
		require_once(RDCOMPONENTDIR.DS.'models'.DS.'sitemap.php');
		RdRequest::setVar('view', 'sitemap');
		RdRequest::setVar('layout', 'default');
		parent::display();
	}
}
?>