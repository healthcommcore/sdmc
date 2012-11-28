<?php
/**
* @version $Id: parameter.php 10 2007-08-24 14:19:42Z deutz $
* @copyright * 2007 * Robert Deutz Business Solution * www.rdbs.de *
*
*/

/** ensure this file is being included by a parent file */
defined('_FWADAPTER') or die('Restricted access');

jimport('joomla.html.parameter');

/**
 * class RdParameter
 *
 * @package adapter
 * @subpackage joomla
 *
 */
class RdParameter extends JParameter
{

	function loadSetupString($str)
	{
		$result = false;

		if ($str)
		{
			$xml = & JFactory::getXMLParser('Simple');

			if ($xml->loadString($str))
			{
				if ($params = & $xml->document->params) {
					foreach ($params as $param)
					{
						$this->setXML( $param );
						$result = true;
					}
				}
			}
		}
		else
		{
			$result = true;
		}

		return $result;
	}


}
?>
