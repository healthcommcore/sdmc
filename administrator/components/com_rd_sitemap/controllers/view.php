<?php
/**
 * Run Digital Sitemap
 * @author Robert Deutz (email contact@rdbs.net / site www.rdbs.de)
 * @version $Id: view.php 53 2009-06-22 11:09:43Z pascal $
 *
 * @copyright Copyright (C) 2005-2007 run-digital
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 *
 * This is free software
 **/

// no direct access
defined( 'RDCOMPONENT' ) or die( 'Restricted access' );

/**
 * class RdSitemapControllerView
 *
 * @package RD_Sitemap
 */
class RdSitemapControllerView extends RdController
{
	/**
	 * Constructor
	 */
	function __construct( $config = array() )
	{
		parent::__construct( $config );
		// Register Extra tasks
		$this->registerTask( 'cancelinstall',	'cancel' );
		$this->registerTask( 'add', 			'makenew' );
		$this->registerTask( 'apply', 			'save' );
		$this->registerTask( 'unpublish', 		'publish' );
		$this->registerTask( 'weiter', 		'save' );//PASCAL
	}

	/**
	 * Cancel working
	 */
	function cancel()
	{
		$this->setRedirect( 'index.php?option='.RDCOMPONENT.'&section=view&task=view' );

		// Initialize variables
		$db		=& RdFactory::getDBO();
		$post	= RdRequest::get( 'post' );
		$row	=& RdTable::getInstance('view', 'Table');
		$row->bind( $post );
		$row->checkin();
	}

	/**
	 * Copies one or more items
	 */
	function copy()
	{
		$this->setRedirect( 'index.php?option='.RDCOMPONENT.'&section=view&task=view' );

		$cid	= RdRequest::getVar( 'cid', null, 'post', 'array' );
		$db		=& RdFactory::getDBO();
		$table	=& RdTable::getInstance('view', 'Table');
		$user	= &RdFactory::getUser();
		$n		= count( $cid );

		if ($n > 0)
		{
			foreach ($cid as $id)
			{
				if ($table->load( (int)$id ))
				{
					$table->id				= 0;
					$table->title			= 'Copy of ' . $table->title;
					$table->published		= 0;

					if (!$table->store()) {
						return RdError::raiseWarning( $table->getError() );
					}
					// copy relations

				}
				else {
					return RdError::raiseWarning( 500, $table->getError() );
				}
			}
		}
		else {
			return RdError::raiseWarning( 500, RdText::_( 'No items selected' ) );
		}
		$this->setMessage( RdText::sprintf( 'Items copied', $n ) );
	}

	/**
	 * Display the list of views
	 */
	function display()
	{
		$mainframe= RdFactory::getApplication();
		$db =& RdFactory::getDBO();

		$context			= RDCOMPONENT.'.view.list.';
		$filter_order		= $mainframe->getUserStateFromRequest( $context.'filter_order', 	'filter_order', 	'v.title' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( $context.'filter_order_Dir',	'filter_order_Dir',	'' );
		$filter_state 		= $mainframe->getUserStateFromRequest( $context.'filter_state', 	'filter_state', 	'*' );
		$search 			= $mainframe->getUserStateFromRequest( $context.'search', 			'search', 			'' );

		$limit		= $mainframe->getUserStateFromRequest( $context.'limit', 'limit', $mainframe->getCfg('list_limit'), 0);
		$limitstart = $mainframe->getUserStateFromRequest( $context.'limitstart', 'limitstart', 0 );

		$where = array();

		if ( $filter_state )
		{
			if ( $filter_state == 'P' ) {
				$where[] = 'v.published = 1';
			}
			else if ($filter_state == 'U' ) {
				$where[] = 'v.published = 0';
			}
		}
		if ($search) {
			$where[] = 'LOWER(b.title) LIKE ' . $db->Quote( '%'.$search.'%' );
		}

		$where 		= count( $where ) ? "\nWHERE " . implode( ' AND ', $where ) : '';
		$orderby 	= "\n ORDER BY $filter_order $filter_order_Dir";

		// get the total number of records
		$query = "SELECT COUNT(*)"
		. "\n FROM #__rd_sitemap_view AS v"
		. $where
		;
		$db->setQuery( $query );
		$total = $db->loadResult();


		$pageNav = new RdPagination( $total, $limitstart, $limit );

		$query = "SELECT v.*"
		. "\n FROM #__rd_sitemap_view AS v"
		. $where
		. $orderby
		;
		$db->setQuery( $query, $pageNav->limitstart, $pageNav->limit );

		//echo $db->getQuery();

		$rows = $db->loadObjectList();

		// state filter
		$lists['state']	=  RdHTML::_('grid.state',  $filter_state );

		// table ordering
		$lists['order_Dir'] = $filter_order_Dir;
	
		$lists['order'] = $filter_order;

		// search filter
		$lists['search']= $search;

		require_once(RDCOMPONENTDIR.DS.'views'.DS.'view.php');
		RdSitemapViewView::viewList( $rows, $pageNav, $lists );
	}

	/**
	 * Install an item
	 */
	function doinstall()
	{

		$this->setRedirect( 'index.php?option='.RDCOMPONENT.'&section=view&task=view' );
		$db			=& RdFactory::getDBO();
		$doinstall	= true;
		$install	= true;
		$userfile 	= $_FILES['userfile']['tmp_name'];
		$type 		= $_FILES['userfile']['type'];
		$error 		= $_FILES['userfile']['error'];
		$size 		= $_FILES['userfile']['size'];

		// do some checks what we get
		if ($error !== 0)
		{
			RdError::raiseWarning( 500, RdText::_('Error uploading File, Errorcode: ').$error );
			$doinstall = false;
		}

		if ($type != "text/xml")
		{
			RdError::raiseWarning( 500, RdText::_('Wrong Filetype') );
			$doinstall = false;
		}

		if ($size > 30000)
		{
			RdError::raiseWarning( 500, RdText::_('File to Big') );
			$doinstall = false;
		}

		if ($doinstall)
		{
			if (function_exists('simplexml_load_file'))
			{
				// use simplexml 2 parse the file
				$simpleXML=true;
				$xmlstr=file_get_contents($userfile);
				$xml = new SimpleXMLElement($xmlstr);
				$params = $xml->xpath('/install/params');
				$type 		= strtolower($xml['type']);
				$subtype 	= strtolower($xml['subtype']);
				$version 	= strtoupper($xml['version']);
				$params= $xml->params[0];
				$params['group'] = 'extra';
			}
			else
			{
				$simpleXML=false;
				// use the JSimpleXML
				$xml = new JSimpleXML();
				$xml->loadFile($userfile);
				$type 		= strtolower($xml->document->attributes( 'type' ));
				$subtype 	= strtolower($xml->document->attributes( 'subtype' ));
				$version 	= strtoupper($xml->document->attributes( 'version' ));
				$params= $xml->document->params[0];
				$params->addAttribute('group', 'extra') ;
			}

			// check type, subtype and version
			if ($type == 'rdsitemap')
			{
				if ($subtype == 'view')
				{
					if ($version == '2.0.J')
					{
						// install
						if ($simpleXML)
						{
							$title          = (string) $xml->title;
							$creationDate   = (string) $xml->creationDate;
							$author         = (string) $xml->author;
							$copyright      = (string) $xml->copyright;
							$authorEmail    = (string) $xml->authorEmail;
							$authorUrl      = (string) $xml->authorUrl;
							$version        = (string) $xml->version;
							$description    = (string) $xml->description;
							$content		= (string) $xml->sql;
							$control		= '<config>'.$params->asXML().'</config>';
						}
						else
						{
							$title          = $xml->document->title[0]->data();
							$creationDate   = $xml->document->creationDate[0]->data();
							$author         = $xml->document->author[0]->data();
							$copyright      = $xml->document->copyright[0]->data();
							$authorEmail    = $xml->document->authorEmail[0]->data();
							$authorUrl      = $xml->document->authorUrl[0]->data();
							$version        = $xml->document->version[0]->data();
							$description    = $xml->document->description[0]->data();
							$content		= $xml->document->sql[0]->data();
							$control		= '<config>'.$params->toString().'</config>';
						}
					}
					else
					{
						RdError::raiseWarning( 500, RdText::_('Wrong Version') );
						$install = false;
					}
				}
				else
				{
					if ($subtype == 'menu')
					{
						if ($version == '2.0.J')
						{
							// install
							if ($simpleXML)
							{
								$title          = (string) $xml->title;
								$creationDate   = (string) $xml->creationDate;
								$author         = (string) $xml->author;
								$copyright      = (string) $xml->copyright;
								$authorEmail    = (string) $xml->authorEmail;
								$authorUrl      = (string) $xml->authorUrl;
								$version        = (string) $xml->version;
								$description    = (string) $xml->description;
								$content		= (string) $xml->sql;
								$control		= '<config>'.$params->asXML().'</config>';
							}
							else
							{
								$title          = $xml->document->title[0]->data();
								$creationDate   = $xml->document->creationDate[0]->data();
								$author         = $xml->document->author[0]->data();
								$copyright      = $xml->document->copyright[0]->data();
								$authorEmail    = $xml->document->authorEmail[0]->data();
								$authorUrl      = $xml->document->authorUrl[0]->data();
								$version        = $xml->document->version[0]->data();
								$description    = $xml->document->description[0]->data();
								$content		= $xml->document->sql[0]->data();
								$control		= '<config>'.$params->toString().'</config>';
							}
						}
						else
						{
							RdError::raiseWarning( 500, RdText::_('Wrong Version') );
							$install = false;
						}
					}
					else
					{
						RdError::raiseWarning( 500, RdText::_('Wrong Subtype: Must view or menu') );
						$install = false;
					}
				}
			}
			else
			{
				RdError::raiseWarning( 500, RdText::_('Wrong type: Must rdsitemap') );
				$install = false;
			}

			if (!$install)
			{
				RdError::raiseWarning( 500, RdText::_('Wrong Version') );
				return false;
			}
			else
			{
				// check if the view or menu exists
				$query = "SELECT COUNT(*) FROM #__rd_sitemap_view WHERE type = '$type' AND title = '$title'";
				$db->setQuery($query);
				if ($db->loadResult() == 0)
				{
					$row =& RdTable::getInstance('view', 'Table');
					$row->title			= $title;
					$row->creation_date = $creationDate;
					$row->author        = $author;
					$row->copyright     = $copyright;
					$row->author_email  = $authorEmail;
					$row->author_url    = $authorUrl;
					$row->version       = $version;
					$row->description   = $description;
					$row->content		= $content;
					$row->type			= $subtype;
					$row->control		= $control;

					if (!$row->check()) {
						return RdError::raiseWarning( 500, $row->getError() );
					}
					if (!$row->store()) {
						return RdError::raiseWarning( 500, $row->getError() );
					}
				}
				else
				{
					RdError::raiseWarning( 500, RdText::_('Item Exists') );
					return false;
				}
			}
			$this->setMessage( RdText::_( 'Installtion Ok ' ) );
			return true;
		}
	}

	/**
	 * Edit a view
	 */
  function makenew()
  {
	RdMenuBar::customX( 'weiter', 'forward.png', 'forward_f2.png', 'weiter', false ); //PASCAL
	
			$db		=& RdFactory::getDBO();
		$user	=& RdFactory::getUser();

		$cid 	= RdRequest::getVar('cid', array(0), 'method', 'array');
		$option = RdRequest::getVar('option');

		$lists = array();

		$row =& RdTable::getInstance('View', 'Table');
		$row->load( $cid[0] );

		// checkoutprocessing ??
		if ($row->checked_out && $user->get('id') != $row->checked_out) {
			return RdError::raiseWarning( 500, RdText::_( 'Item checkout by an other user selected' ) );
		} else {
			$row->checkout( $user->get('id') );
		}
		// Check New item
		$new = (int) $row->id == 0;
		if ($new)
		{
			// Type
			$lists['type'] = RdHTML::_('select.booleanlist',  'type', '', $row->type,'menu','view' );
			$params = new RdParameter($row->params,RDCOMPONENTDIR.DS.'m_standard.xml');
		}
		else
		{
			if ($row->type == 'view')
			{
				$params = new RdParameter($row->params,RDCOMPONENTDIR.DS.'v_standard.xml');
			}
			else
			{
				$params = new RdParameter($row->params,RDCOMPONENTDIR.DS.'m_standard.xml');
			}
		}
		// published
		$lists['published'] = RdHTML::_('select.booleanlist',  'published', '', $row->published );

		// Parameter handling
		$xmlstr= $row->control;
		$params->loadSetupString($xmlstr);

		require_once(RDCOMPONENTDIR.DS.'views'.DS.'addview.php');
		RdSitemapViewView::viewitem( $row, $lists,$params );
	
	}
	 
	function edit()
	{
		$db		=& RdFactory::getDBO();
		$user	=& RdFactory::getUser();

		$cid 	= RdRequest::getVar('cid', array(0), 'method', 'array');
		$option = RdRequest::getVar('option');

		$lists = array();

		$row =& RdTable::getInstance('View', 'Table');
		$row->load( $cid[0] );

		// checkoutprocessing ??
		if ($row->checked_out && $user->get('id') != $row->checked_out) {
			return RdError::raiseWarning( 500, RdText::_( 'Item checkout by an other user selected' ) );
		} else {
			$row->checkout( $user->get('id') );
		}
		// Check New item
		$new = (int) $row->id == 0;
		if ($new)
		{
			// Type
			$lists['type'] = RdHTML::_('select.booleanlist',  'type', '', $row->type,'menu','view' );
			$params = new RdParameter($row->params,RDCOMPONENTDIR.DS.'m_standard.xml');
		}
		else
		{
			if ($row->type == 'view')
			{
				$params = new RdParameter($row->params,RDCOMPONENTDIR.DS.'v_standard.xml');
			}
			else
			{
				$params = new RdParameter($row->params,RDCOMPONENTDIR.DS.'m_standard.xml');
			}
		}
		// published
		$lists['published'] = RdHTML::_('select.booleanlist',  'published', '', $row->published );

		// Parameter handling
		$xmlstr= $row->control;
		$params->loadSetupString($xmlstr);

		require_once(RDCOMPONENTDIR.DS.'views'.DS.'view.php');
		RdSitemapViewView::viewitem( $row, $lists,$params );
	}
	
	
	/**
	 * Installation screen
	 */
	function install()
	{
		require_once(RDCOMPONENTDIR.DS.'views'.DS.'view.php');
		RdSitemapViewView::install();
	}

	/**
	 * Publish/Unpublish an item
	 */
	function publish()
	{
		$this->setRedirect( 'index.php?option='.RDCOMPONENT.'&section=view&task=view' );

		// Initialize variables
		$db  		=& RdFactory::getDBO();
		$user		=& RdFactory::getUser();
		$cid 		= RdRequest::getVar( 'cid', array(), 'post', 'array' );
		$task		= RdRequest::getVar( 'task' );
		$publish	= ($task == 'publish');
		$n			= count( $cid );

		if (empty( $cid )) {
			return RdError::raiseWarning( 500, RdText::_( 'No items selected' ) );
		}

		$cids = implode( ',', $cid );

		$query = "UPDATE #__rd_sitemap_view"
		. "\n SET published = " . (int) $publish
		. "\n WHERE id IN ( $cids )"
		. "\n AND ( checked_out = 0 OR ( checked_out = " .(int) $user->get('id'). " ) )"
		;
		$db->setQuery( $query );
		if (!$db->query()) {
			return RdError::raiseWarning( 500, $row->getError() );
		}
		$this->setMessage( RdText::sprintf( $publish ? 'Items published' : 'Items unpublished', $n ) );
	}

	/**
	 * remove an item
	 */
	function remove()
	{
		$this->setRedirect( 'index.php?option='.RDCOMPONENT.'&section=view&task=view' );

		// Initialize variables
		$db		=& RdFactory::getDBO();
		$cid	= RdRequest::getVar( 'cid', array(), 'post', 'array' );

		$undeleted = array();
		$message = 'Item(s) removed';

		foreach ($cid as $elm)
		{
			$query = "SELECT count(*) FROM #__rd_sitemap_rel_viewset_has_view WHERE view = $elm";
			$db->setQuery( $query );
			$noway = $db->loadResult();
			if ($noway == 0)
			{
				$query = "DELETE FROM #__rd_sitemap_view WHERE id = $elm";
				$db->setQuery( $query );

				if (!$db->query()) {
					RdError::raiseWarning( 500, $db->getError() );
				}
			}
			else
			{
				$undeleted[] = $elm;
			}
		}
		if (count($undeleted) != 0)
		{
			$message = 'Some items could not be removed';
		}
		$this->setMessage( RdText::_( $message ) );
	}

	/**
	 * Save method
	 */
	function save()
	{
		// default redirect
		$this->setRedirect( 'index.php?option='.RDCOMPONENT.'&section=view&task=view' );
		// Initialize variables
		$db		=& RdFactory::getDBO();
		$post	= RdRequest::get( 'post' , 2);
		$row 	=& RdTable::getInstance('view', 'Table');
		$id 	= RdRequest::getVar( 'id', 0, 'post', 'int' );
		$row->load($id);
		if (!$row->bind( $post )) {
			return RdError::raiseWarning( 500, $row->getError() );
		}

		// Check New item
		$new = (int) $row->id == 0;

		if ($new)
		{
			$user =& RdFactory::getUser();
			$row->author 		= $user->name;
			$row->author_email 	= $user->email;
			$row->version 		= 1;
			$row->creation_date = date( 'j F Y');
			if ($row->type == 0)
			{
				$row->type = 'view';
			}
			else
			{
				$row->type = 'menu';
			}
		}
		else
		{
			$row->version = $row->version++;
		}

		$params		= RdRequest::getVar( 'params', null, 'post', 'array' );

		// Build parameter INI string
		if (is_array($params))
		{
			$txt = array ();
			foreach ($params as $k => $v) {
				$txt[] = "$k=$v";
			}
			$row->params = implode("\n", $txt);
		}


		if (!$row->check()) {
			return RdError::raiseWarning( 500, $row->getError() );
		}
		if (!$row->store()) {
			return RdError::raiseWarning( 500, $row->getError() );
		}
		$row->checkin();

		$task = JRequest::getVar( 'task' );
		switch ($task)
		{
			case 'weiter':
				$link = 'index.php?option='.RDCOMPONENT.'&section=view'.'&task=edit&cid='. $row->id .'&hidemainmenu=1';
				break;
				
			case 'apply':
				$link = 'index.php?option='.RDCOMPONENT.'&section=view'.'&task=edit&cid='. $row->id .'&hidemainmenu=1';
				break;

			case 'save':
			default:
				$link = 'index.php?option='.RDCOMPONENT.'&section=view&task=view';
				break;
		}
		$this->setRedirect( $link, RdText::_( 'Item Saved' ) );
	}
}