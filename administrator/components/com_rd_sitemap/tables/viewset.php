<?php
/**
 * Run Digital Sitemap
 * @author Robert Deutz (email contact@rdbs.net / site www.rdbs.de)
 * @version $Id: viewset.php 10 2007-08-24 14:19:42Z deutz $
 *
 * @copyright Copyright (C) 2005-2007 run-digital
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 *
 * This is free software
 **/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * class TableViewset
 *
 * @package RD_Sitemap
 */
class TableViewset extends RdTable
{
	/** @var int */
	var $id					= null;
	/** @var string */
	var $title				= '';
	/** @var string */
	var $content			= '';
	/** @var int */
	var $checked_out		= 0;
	/** @var date */
	var $checked_out_time	= 0;
	/** @var int */
	var $published			= 1;
	/** @var string */
	var $params				= null;

	function __construct( &$_db ) {
		parent::__construct( '#__rd_sitemap_viewset', 'id', $_db );
	}


	function check() {

		if(trim($this->title) == '') {
			$this->_error = RdText::_( 'ERR_VS_NAME' );
			return false;
		}

		return true;
	}
}
?>