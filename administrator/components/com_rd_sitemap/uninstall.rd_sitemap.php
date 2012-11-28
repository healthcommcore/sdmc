<?php
/**
 * Run Digital Sitemap
 * @author Robert Deutz (email contact@rdbs.net / site www.rdbs.de)
 * @version $Id: uninstall.rd_sitemap.php 10 2007-08-24 14:19:42Z deutz $
 * @package RD_Sitemap
 * @copyright Copyright (C) 2005-2007 run-digital
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 *
 * This is free software
 **/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );


function com_uninstall ()
{
	echo "<p style=\"font-size:1.3em;\">Please keep in mind the database tables (#__rd_sitemap_*) are <strong>not</strong> deleted. You have to do this by hand, if you will deinstall the sitemap completly</p><br /><br />";
	echo "<p style=\"text-align:center;\">&copy; Copyright 2007 by Robert Deutz - <a href=\"http://www.run-digital.com\" target=\"_blank\">Run Digital</a></p>";
}
?>
