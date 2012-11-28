<?php

/**
 * Run Digital Sitemap
 * @author Robert Deutz (email contact@rdbs.net / site www.rdbs.de)
 * @version $Id: rd_sitemap.php 13 2007-08-25 09:46:19Z deutz $
 * @package RD_Sitemap
 * @copyright Copyright (C) 2005-2007 run-digital
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 *
 * This is free software
 **/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// needed for apapterclasses
define('_FWADAPTER',1);

// Componentname
define('RDCOMPONENT','com_rd_sitemap');

// The componentdirectory
define('RDCOMPONENTDIR',dirname(__FILE__));

if (defined('JPATH_ROOT'))
{
	// This is Joomla
	// load apadter
	$files = glob(RDCOMPONENTDIR .DS.'adapter'.DS.'joomla'.DS. '*.php');
	foreach ($files as $f) {require_once( $f );}
}

$controllerName = RdRequest::getVar( 'section', 'sitemap' );
$task = RdRequest::getVar('task','view');

switch ($controllerName)
{
	// wenn nichts passt dann nimm dies
	default:
		$controllerName = 'sitemap';
	// der Rest ist bekannt
	case 'about';
	case 'viewset' :
	case 'view':
		require_once( RDCOMPONENTDIR.DS.'controllers'.DS.$controllerName.'.php' );
		$controllerName = 'RdSitemapController'.ucfirst($controllerName);
		// erzeuge controller
		$controller = new $controllerName();
		// Task ausfuehren
		$controller->execute( $task );
		// Redirect wenn gesetzt
		$controller->redirect();
		break;
}
/** EOF **/
?>
