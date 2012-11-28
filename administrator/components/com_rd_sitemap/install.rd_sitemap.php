<?php
/**
 * Run Digital Sitemap
 * @author Robert Deutz (email contact@rdbs.net / site www.rdbs.de)
 * @version $Id: install.rd_sitemap.php 53 2009-06-22 11:09:43Z pascal $
 * @package RD_Sitemap
 * @copyright Copyright (C) 2005-2007 run-digital
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 *
 * This is free software
 **/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );


function com_install ()
{
	$db =& JFactory::getDBO();
    $i = 0;
	$sucess = 1;
	// check if tables installed rd_glossary
	$update = 0;
	$tables = $db->getTableList();

	foreach ($tables as $elm) {
		$update = $update + substr_count(strtolower($elm), '_rd_sitemap');
	}

	if ($update) {
		// noting to do fuer 2.0.J-RC1
	} else {
	   	// first install

		//create tables
		$query = "
		CREATE TABLE `#__rd_sitemap_rel_viewset_has_view` (
		  `id` int(11) unsigned NOT NULL auto_increment,
		  `viewset` int(11) unsigned NOT NULL,
		  `view` int(11) unsigned NOT NULL,
		  `ordering` int(11) unsigned NOT NULL,
		  PRIMARY KEY  (`id`),
		  KEY `id_viewset` (`viewset`),
		  KEY `id_view` (`view`)
		) ENGINE=MyISAM ;
        ";
		$db->setQuery($query);
		$what[$i] = "Create Table #__rd_sitemap_rel_viewset_has_view";
		$result[$i] = $db->query();
		$err=$db->getErrorMsg();
		if ($result[$i]) {$result[$i] = "sucess";} else {$result[$i] = "fail: $err";$sucess = 0;}
		$i++;

		$query = "
		CREATE TABLE `#__rd_sitemap_view` (
		  `id` int(11) unsigned NOT NULL auto_increment,
		  `type` varchar(100) NOT NULL,
		  `title` text NOT NULL,
		  `creation_date` varchar(255) NOT NULL,
		  `author` varchar(255) NOT NULL,
		  `copyright` varchar(255) NOT NULL,
		  `author_email` varchar(255) NOT NULL,
		  `author_url` varchar(255) NOT NULL,
		  `version` varchar(255) NOT NULL,
		  `description` text NOT NULL,
		  `content` text NOT NULL,
		  `checked_out` int(11) unsigned NOT NULL default '0',
		  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
		  `published` tinyint(1) NOT NULL default '0',
		  `params` text NOT NULL,
		  `control` text NOT NULL,
		  PRIMARY KEY  (`id`),
		  KEY `published` (`published`)
		) ENGINE=MyISAM ;
	    ";
		$db->setQuery($query);
		$what[$i] = "Create Table #__rd_sitemap_view";
		$result[$i] = $db->query();
		$err=$db->getErrorMsg();
		if ($result[$i]) {$result[$i] = "sucess";} else {$result[$i] = "fail: $err";$sucess = 0;}
		$i++;

		$query = "
		CREATE TABLE `#__rd_sitemap_viewset` (
		  `id` int(11) unsigned NOT NULL auto_increment,
		  `title` text NOT NULL,
		  `content` text NOT NULL,
		  `checked_out` int(11) unsigned NOT NULL default '0',
		  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
		  `published` tinyint(1) NOT NULL default '0',
		  `params` text NOT NULL,
		  PRIMARY KEY  (`id`),
		  KEY `published` (`published`)
		) ENGINE=MyISAM ;
	    ";
		$db->setQuery($query);
		$what[$i] = "Import Default Data in #__rd_sitemap_rel_viewset_has_view";
		$result[$i] = $db->query();
		$err=$db->getErrorMsg();
		if ($result[$i]) {$result[$i] = "sucess";} else {$result[$i] = "fail: $err";$sucess = 0;}
		$i++;

        $query = "
		INSERT INTO `#__rd_sitemap_rel_viewset_has_view` (`id`, `viewset`, `view`, `ordering`) VALUES
		(1, 1, 5, 0),
		(2, 1, 1, 1),
		(3, 1, 4, 2),
		(4, 2, 3, 0),
		(5, 2, 2, 1);
    	";
		$db->setQuery($query);
		$what[$i] = "Import Default Data in #__rd_sitemap_view";
		$result[$i] = $db->query();
		$err=$db->getErrorMsg();
		if ($result[$i]) {$result[$i] = "sucess";} else {$result[$i] = "fail: $err";$sucess = 0;}
		$i++;

    	$query = "
		INSERT INTO `#__rd_sitemap_view` (`id`, `type`, `title`, `creation_date`, `author`, `copyright`, `author_email`, `author_url`, `version`, `description`, `content`, `checked_out`, `checked_out_time`, `published`, `params`, `control`) VALUES
		(1, 'view', 'Content', '17 Aug 2007', 'Robert Deutz', 'This component in released under the GNU/GPL License', 'contact@run-digital.com', 'www.run-digital.com', '1.0', 'This is a View for the Run-Digital Sitemap Component, it shows the content', '\n        \nSELECT\n  c.title as title,\n  s.title as section,\n  cat.title as category,\n  CONCAT(''index.php?option=com_content&view=article&id='', c.id)  as link  , \nc.catid as catslug,
  \nCASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS('':'', c.id, c.alias) ELSE c.id END as slug\n		\nFROM\n  #__content as c,\n  #__sections as s,\n  #__categories as cat\nWHERE \n  c.sectionid = s.id \n  AND c.catid = cat.id\n  AND c.state = ''1''\n  AND s.published = ''1''\n  AND cat.published = ''1''\n  AND ( c.publish_up = ''0000-00-00 00:00:00'' OR c.publish_up <= ''{NOW}'' )\n  AND ( c.publish_down = ''0000-00-00 00:00:00'' OR c.publish_down >= ''{NOW}'' )\n  AND c.access <= ''{USER_GID}''\n  {AND c.sectionid IN ({sectionid}) }\n  ORDER BY  s.title, cat.title,c.title\n\n	', 0, '0000-00-00 00:00:00', 1, '', '<config><params group=\"extra\">\n		<param name=\"sectionid\" type=\"text\" default=\"\" label=\"SECTIONID\" description=\"DESCSECTIONID\"/>\n	</params></config>'),
		(2, 'view', 'Categories', '17 Aug 2007', 'Robert Deutz', 'This component in released under the GNU/GPL License', 'contact@run-digital.com', 'www.run-digital.com', '1.0', 'This is a View for the Run-Digital Sitemap Component, it shows the categories', '\n        \nSELECT\n  cat.title as title,\n  s.title as section,\n  '''' as category,\n  concat(''index.php?option=com_content&view=category&layout=blog&id='',cat.id) as link, cat.id as bcatid, s.id as bsecid		\nFROM\n    #__sections as s,\n    #__categories as cat\nWHERE\n  cat.section = s.id\n  AND s.published = ''1''\n  AND cat.published = ''1''\n  AND cat.access <= ''{USER_GID}''\n  AND s.access <= ''{USER_GID}''\n  {AND c.sectionid IN ({sectionid}) }\n  {AND cat.id IN ({catid}) }\n  ORDER BY  s.title, cat.title\n\n	', 0, '0000-00-00 00:00:00', 1, '', '<config><params group=\"extra\">\n		<param name=\"sectionid\" type=\"text\" default=\"\" label=\"SECTIONID\" description=\"DESCSECTIONID\"/>\n		<param name=\"catid\" type=\"text\" default=\"\" label=\"CATID\" description=\"DESCCATID\"/>\n	</params></config>'),
		(3, 'view', 'Sections', '17 Aug 2007', 'Robert Deutz', 'This component in released under the GNU/GPL License', 'contact@run-digital.com', 'www.run-digital.com', '1.0', 'This is a View for the Run-Digital Sitemap Component, it shows the sections', '\n        \nSELECT\n  s.title as title,\n  '''' as section,\n  '''' as category,\n  concat(''index.php?option=com_content&view=section&layout=blog&id='',s.id) as link,\ns.id as secid \nFROM\n    #__sections as s\nWHERE \n  s.published = ''1''\n  AND access <= ''{USER_GID}''\n  {AND c.sectionid IN ({sectionid}) }\n  ORDER BY  s.title\n\n	', 0, '0000-00-00 00:00:00', 1, '', '<config><params group=\"extra\">\n		<param name=\"sectionid\" type=\"text\" default=\"\" label=\"SECTIONID\" description=\"DESCSECTIONID\"/>\n	</params></config>'),
		(4, 'view', 'Content Archiv', '17 Aug 2007', 'Robert Deutz', 'This component in released under the GNU/GPL License', 'contact@run-digital.com', 'www.run-digital.com', '1.0', 'This is a View for the Run-Digital Sitemap Component, it shows the content archiv', '\n        \nSELECT\n  c.title as title,\n  s.title as section,\n  cat.title as category,\n  CONCAT(''index.php?option=com_content&view=article&id='', c.id)  as link, \nc.catid as catslug,
  \nCASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS('':'', c.id, c.alias) ELSE c.id END as slug\n		\nFROM\n  #__content as c,\n  #__sections as s,\n  #__categories as cat\nWHERE \n  c.sectionid = s.id \n  AND c.catid = cat.id\n  AND c.state = ''-1''\n  AND s.published = ''1''\n  AND cat.published = ''1''\n  AND ( c.publish_up = ''0000-00-00 00:00:00'' OR c.publish_up <= ''{NOW}'' )\n  AND ( c.publish_down = ''0000-00-00 00:00:00'' OR c.publish_down >= ''{NOW}'' )\n  AND c.access <= ''{USER_GID}''\n  {AND c.sectionid IN ({sectionid}) }\n  ORDER BY  s.title, cat.title,c.title        \n		\n	', 0, '0000-00-00 00:00:00', 1, '', '<config><params group=\"extra\">\n		<param name=\"sectionid\" type=\"text\" default=\"\" label=\"SECTIONID\" description=\"DESCSECTIONID\"/>\n	</params></config>'),
		(5, 'menu', 'Menu', '20 Aug 2007', 'Robert Deutz', 'This component in released under the GNU/GPL License', 'contact@run-digital.com', 'www.run-digital.com', '1.0', 'This is a View for the Run-Digital Sitemap Component, it shows menu', '\n        \nSELECT\n  m.*\nFROM\n  #__menu as m\nWHERE\n  m.published = ''1''\n  AND m.access <= ''{USER_GID}''\n  {AND m.menutype = ''{menutype}'' }\n  ORDER BY m.menutype,m.parent, m.sublevel, m.ordering\n      \n	', 0, '0000-00-00 00:00:00', 1, 'menutype=mainmenu', '<config><params group=\"extra\">\n		<param name=\"menutype\" type=\"text\" default=\"\" label=\"MENUTYPE\" description=\"DESCMENUTYPE\"/>\n	</params></config>');
	    ";
		$db->setQuery($query);
		$what[$i] = "Import Default Data in #__rd_sitemap_viewset";
		$result[$i] = $db->query();
		$err=$db->getErrorMsg();
		if ($result[$i]) {$result[$i] = "sucess";} else {$result[$i] = "fail: $err";$sucess = 0;}
		$i++;

        $query = "
		INSERT INTO `#__rd_sitemap_viewset` (`id`, `title`, `content`, `checked_out`, `checked_out_time`, `published`, `params`) VALUES
		(1, 'Default', '', 0, '0000-00-00 00:00:00', 1, ''),
		(2, 'Section/Category', '', 0, '0000-00-00 00:00:00', 1, '');
	    ";
		$db->setQuery($query);
		$what[$i] = "Create Table #__rd_sitemap_viewset";
		$result[$i] = $db->query();
		$err=$db->getErrorMsg();
		if ($result[$i]) {$result[$i] = "sucess";} else {$result[$i] = "fail: $err";$sucess = 0;}
		$i++;
	}

	if ($i != 0)
	{
		// Readmessage
		$msg = "";
		$msg .=  "<p style=\"font-size:1.3em;\">";
		$msg .= "<table width=\"100%\" border=\"0\">";
		$msg .= "<tr><td><br /><br />Installationresults:</td></tr>";
		// show installationresults
		for($zz = 0; $zz < $i ; $zz++) {
				$msg .= "<tr><td>ToDo: $what[$zz] Result: $result[$zz]";
				$msg .= "</td></tr>";
		}
		$msg .= "<tr><td><br/><font class=\"small\">&copy; Copyright 2007 by Robert Deutz - <a href=\"http://www.run-digital.com\" target=\"_blank\">Run Digital</a></font><br/>";
		$msg .= "<br/>";
		$msg .= "</td></tr></table></center>";
		$msg .= "<hr>";
		echo $msg;
		if ($sucess) {return true;} {return false;}
	}
	else
	{
		echo "<p style=\"font-size:1.3em;\">Noting to do!</p><br /><br />";
		echo "<p style=\"text-align:center;\">&copy; Copyright 2007 by Robert Deutz - <a href=\"http://www.run-digital.com\" target=\"_blank\">Run Digital</a></p>";
	}
}
?>
