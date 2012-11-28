<?php
/**
 * Run Digital Sitemap
 * @author Robert Deutz (email contact@rdbs.net / site www.rdbs.de)
 * @version $Id: view.html.php 36 2007-10-20 10:22:46Z deutz $
 *
 * @copyright Copyright (C) 2005-2007 run-digital
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 *
 * This is free software
 **/
defined( 'RDCOMPONENT' ) or die( 'Restricted access' );
/**
 * class RdSitemapViewSitemap
 *
 *  @package RD_Sitemap
 *
 */
 class RdSitemapViewSitemap extends RdView
{
	/**
	 * Display the sitemap
	 */
	function display($tpl = null)
	{
		$menu =& RdSite::getMenu();
		$this->menuname = $menu->getActive();
		$this->menuname = $this->menuname->name;

		$this->data = &$this->get('data');
		$this->mparams = &$this->get('MenuParamsObj');
		$this->cparams = &$this->get('ComponentParamsObj');
		parent::display($tpl);
	}
}
?>
