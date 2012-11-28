<?php
/**
* @version $Id: table.php 10 2007-08-24 14:19:42Z deutz $
* @copyright * 2007 * Robert Deutz Business Solution * www.rdbs.de *
*
*/

/** ensure this file is being included by a parent file */
defined('_FWADAPTER') or die('Restricted access');

jimport( 'joomla.database.table' );

/**
 * class RdController
 *
 * @package adapter
 * @subpackage joomla
 *
 */
class RdTable extends JTable
{

	/**
	 * Returns a reference to the a Table object, always creating it
	 *
	 * @param type $type The table type to instantiate
	 * @param string A prefix for the table class name
	 * @return database A database object
	 * @since 1.5
	*/
	function &getInstance( $type, $prefix='RdTable' )
	{
		$false = false;

		$type = preg_replace('/[^A-Z0-9_\.-]/i', '', $type);
		$tableClass = $prefix.ucfirst($type);

		if (!class_exists( $tableClass ))
		{
			jimport('joomla.filesystem.path');
			if($path = JPath::find(RdTable::addIncludePath(), strtolower($type).'.php'))
			{
				require_once $path;

				if (!class_exists( $tableClass ))
				{
					RdError::raiseWarning( 0, 'Table class ' . $tableClass . ' not found in file.' );
					return $false;
				}
			}
			else
			{
				RdError::raiseWarning( 0, 'Table ' . $type . ' not supported. File not found.' );
				return $false;
			}
		}

		$db =& RdFactory::getDBO();
		$instance = new $tableClass($db);
		$instance->setDBO($db);

		return $instance;
	}


}
?>
