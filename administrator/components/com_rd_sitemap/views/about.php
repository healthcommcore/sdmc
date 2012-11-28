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

// no direct access
defined( 'RDCOMPONENT' ) or die( 'Restricted access' );

/**
 * class RdSitemapViewAbout
 *
 * @package RD_Sitemap
 */
class RdSitemapViewAbout
{
	function setToolbar ()
	{

		RdMenuBar::title( JText::_( 'About' ), 'generic.png' );
		RdMenuBar::cancel( 'cancel' );
	}

	function display ()
	{
		RdSitemapViewAbout::setToolbar();
		?>
		<script language="javascript" type="text/javascript">
		<!--
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}
		}
		//-->
		</script>
<?php
		echo '<form action="index.php" method="post" name="adminForm">';

		echo "<div style=\"text-align:left;padding-left:5px;padding-right:5px;\">";
		echo "<p><img src=\"../administrator/components/com_rd_sitemap/images/sitemap.gif\" alt=\"RUN DIGITAL SITEMAP LOGO\" /></p>";

		echo "<p style=\"font-size:120%;font-weight:bold;font-color:#650000;\">Run Digital</p>";
		echo "<p style=\"padding-left:10px;padding-right:10px;\">We build websites that are easy to manage, easy to use and simple to extend. Our approach is a good designed and well accessible Website for all people. </p>";

		echo "<p style=\"font-size:120%;font-weight:bold;font-color:#650000;\">Program</p>";
		echo "<p style=\"padding-left:10px;padding-right:10px;\">RD Site Map is a high configurable sitemap component for mambo. " .
				"It shows all menus item, content items and achived content items or only the items you like. " .
				"You could design the output by using css via classes or contestselctors." .
				"If you have any wishes or have found a bug, please contact the authors by mail: contact at run-digital dot com";

		echo "<p style=\"font-size:120%;font-weight:bold;font-color:#650000;\">Warranty</p>";
		echo "<p style=\"padding-left:10px;padding-right:10px;\">This program is distributed in the hope that it will be useful, " .
				"but WITHOUT ANY WARRANTY; without even the implied warranty of " .
				"MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.</p>";
		echo "<p style=\"font-size:120%;font-weight:bold;font-color:#650000;\">Informations</p>";
		echo "<p style=\"padding-left:10px;padding-right:10px;\">For more inforamtions visit <a href=\"http://www.run-digital.com\" >run digital</a> or the <a href=\"http://service.run-digital.com\" >service site</a></p>";


		echo "<p style=\"text-align:center;\">Copyright 2005-2007 by <a href=\"http://www.run-digital.com\" >run digital</a> </p>";
		echo "</div>";
		echo '<input type="hidden" name"section" value="'.RdRequest::setVar( 'section',''  ).'"';
		echo "<input type=\"hidden\" name=\"option\" value=\"com_rd_sitemap\" />";
		echo "<input type=\"hidden\" name=\"task\" value=\"\" />";
		echo '<form>';

	}

}
?>
