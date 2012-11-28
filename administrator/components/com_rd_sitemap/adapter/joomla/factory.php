<?php
/**
* @version $Id: factory.php 10 2007-08-24 14:19:42Z deutz $
* @copyright * 2007 * Robert Deutz Business Solution * www.rdbs.de *
*
*/

/** ensure this file is being included by a parent file */
defined('_FWADAPTER') or die('Restricted access');

jimport( 'joomla.factory' );

/**
 * class RdFactory
 *
 * @package adapter
 * @subpackage joomla
 *
 */
class RdFactory extends JFactory
{


	function getApplication()
	{
		global $mainframe;
		return $mainframe;
	}

}
?>
