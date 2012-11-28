<?php
/**
 * Run Digital Sitemap
 * @author Robert Deutz (email contact@rdbs.net / site www.rdbs.de)
 * @version $Id: viewset.php 53 2009-06-22 11:09:43Z pascal $
 *
 * @copyright Copyright (C) 2005-2007 run-digital
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 *
 * This is free software
 **/

// no direct access
defined( 'RDCOMPONENT' ) or die( 'Restricted access' );

/**
 * class RdSitemapControllerViewset
 *
 * @package RD_Sitemap
 */
class RdSitemapControllerViewset extends RdController
{
	/**
	 * Constructor
	 */
	function __construct( $config = array() )
	{
		parent::__construct( $config );
		// Register Extra tasks
		$this->registerTask( 'add', 			'edit' );
		$this->registerTask( 'apply', 			'save' );
		$this->registerTask( 'unpublish', 		'publish' );
	}

	/**
	 * cancel
	 */
	function cancel()
	{
		$this->setRedirect( 'index.php?option='.RDCOMPONENT.'&section=viewset&task=view' );

		// Initialize variables
		$db		=& RdFactory::getDBO();
		$post	= RdRequest::get( 'post' );
		$row	=& RdTable::getInstance('viewset', 'Table');
		$row->bind( $post );
		$row->checkin();
	}

	/**
	 * Copies one or more viewsets
	 */
	function copy()
	{
		$this->setRedirect( 'index.php?option='.RDCOMPONENT.'&section=viewset&task=view' );

		$cid	= RdRequest::getVar( 'cid', null, 'post', 'array' );
		$db		=& RdFactory::getDBO();
		$table	=& RdTable::getInstance('viewset', 'Table');
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
					$nid = $table->id;

					$query = "SELECT view, ordering FROM  #__rd_sitemap_rel_viewset_has_view WHERE viewset = $id";
					$db->setQuery($query);
					$rel = $db->loadObjectList();
					$statement = '';
					foreach ($rel as $r )
					{
						$statement .= "INSERT INTO #__rd_sitemap_rel_viewset_has_view (`view`,`viewset`,`ordering`) VALUES ($r->view,$nid,$r->ordering);\n";
					}

					$db->setQuery($statement);
					//echo $db->getQuery();

					if (!$db->queryBatch(true,true))
					{
						return RdError::raiseWarning( 500, $db->stderr() );
					}


				}
				else {
					return RdError::raiseWarning( 500, $table->getError() );
				}
			}
		}
		else {
			return RdError::raiseWarning( 500, RdText::_( 'No items selected' ) );
		}
		$this->setMessage( JText::sprintf( 'Items copied', $n ) );
	}

	/**
	 * Display the list of viewset
	 */
	function display()
	{
		$mainframe= RdFactory::getApplication();
		$db =& RdFactory::getDBO();

		$context			= RDCOMPONENT.'.viewset.list.';
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
		. "\n FROM #__rd_sitemap_viewset AS v"
		. $where
		;
		$db->setQuery( $query );
		$total = $db->loadResult();


		$pageNav = new RdPagination( $total, $limitstart, $limit );

		$query = "SELECT v.*"
		. "\n FROM #__rd_sitemap_viewset AS v"
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

		require_once(RDCOMPONENTDIR.DS.'views'.DS.'viewset.php');
		RdSitemapViewViewset::viewList( $rows, $pageNav, $lists );
	}

	/**
	 * edit a viewset
	 */
	function edit()
	{
		$db		=& RdFactory::getDBO();
		$user	=& RdFactory::getUser();

		$cid 	= RdRequest::getVar('cid', array(0), 'method', 'array');
		$option = RdRequest::getVar('option');

		$lists = array();

		$row =& RdTable::getInstance('Viewset', 'Table');
		$row->load( $cid[0] );

		// Check New item
		$new = (int) $row->id == 0;

		if ($new)
		{
			$used_views = array();
			$query = "SELECT title as text, id as value " .
					 "\n FROM #__rd_sitemap_view " .
					 "\n WHERE type = 'view' OR type = 'menu'";
			$db->setQuery($query);
			$views = $db->loadObjectList();
		}
		else
		{
			// checkoutprocessing ??
			if ($row->checked_out && $user->get('id') != $row->checked_out) {
				return RdError::raiseWarning( 500, RdText::_( 'Item checkout by an other user selected' ) );
			} else {
				$row->checkout( $user->get('id') );
			}
			$query = "SELECT v.title as text, v.id as value " .
					 "\n FROM #__rd_sitemap_view as v, #__rd_sitemap_rel_viewset_has_view as r" .
					 "\n WHERE r.view = v.id AND  r.viewset = $row->id" .
					 "\n ORDER BY ordering ";
			$db->setQuery($query);
			$used_views = $db->loadObjectList();

			$query = "SELECT v.title as text, v.id as value " .
					 "\n FROM #__rd_sitemap_view as v LEFT JOIN #__rd_sitemap_rel_viewset_has_view as r ON v.id = r.view AND r.viewset = $row->id " .
					 "\n WHERE (v.type = 'view' OR v.type = 'menu') AND r.id is null";
			$db->setQuery($query);
			$views = $db->loadObjectList();
		}

		// published
		$lists['published'] = JHTML::_('select.booleanlist',  'published', '', $row->published );

		$lists['views']		= '<select name="views[]" id="views" class="inputbox"  size="20"  style="padding: 6px; width: 250px;">'."\n";
		foreach ($views as $elm)
		{
			$lists['views']	.= '<option class="leftoption" value="'.$elm->value.'" >'.$elm->text.'</option>'."\n";
		}
		$lists['views']	.= '</select>';

		$lists['used_views']	= '<select name="used_views[]" id="used_views" multiple="multiple" class="inputbox"  size="20"  style="padding: 6px; width: 250px;">'."\n";
		foreach ($used_views as $elm)
		{
			$lists['used_views']	.= '<option class="rightoption" value="'.$elm->value.'" >'.$elm->text.'</option>'."\n";
		}
		$lists['used_views']	.= '</select>';

		require_once(RDCOMPONENTDIR.DS.'views'.DS.'viewset.php');
		RdSitemapViewViewset::viewset( $row, $lists );
	}
	/**
	 * Publish/Unpublish a viewset
	 */
	function publish()
	{
		$this->setRedirect( 'index.php?option='.RDCOMPONENT.'&section=viewset&task=view' );

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

		$query = "UPDATE #__rd_sitemap_viewset"
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
	 * remove a viewset
	 */
	function remove()
	{
		$this->setRedirect( 'index.php?option='.RDCOMPONENT.'&section=viewset&task=view' );

		// Initialize variables
		$db		=& RdFactory::getDBO();
		$cid	= RdRequest::getVar( 'cid', array(), 'post', 'array' );
		$n		= count( $cid );
		$undeleted = array();
		$message = 'Item(s) removed';

		foreach ($cid as $elm)
		{
			$link = 'index.php?option=com_rd_sitemap&view=sitemap&id='.$elm;
			$query = "SELECT count(*) FROM #__menu WHERE link = '$link'";
			$db->setQuery( $query );
			$noway = $db->loadResult();

			if ($noway == 0)
			{
				$query = "DELETE FROM #__rd_sitemap_viewset WHERE id = $elm";
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
		$this->setRedirect( 'index.php?option='.RDCOMPONENT.'&section=viewset&task=view' );

		// Initialize variables
		$db =& RdFactory::getDBO();

		$post	= RdRequest::get( 'post' );

		$row =& RdTable::getInstance('viewset', 'Table');

		if (!$row->bind( $post )) {
			return RdError::raiseWarning( 500, $row->getError() );
		}

		// Check New item
		$new = (int) $row->id == 0;

		$task = JRequest::getVar( 'task' );
		if (!$row->check()) {
			return RdError::raiseWarning( 500, $row->getError() );
		}

		if (!$row->store()) {
			return RdError::raiseWarning( 500, $row->getError() );
		}
		$row->checkin();

		// process relations
		$newRelations =	RdRequest::getVar( 'used_views', array(), 'method', 'array');

		// Prepare statement
		$statement = '';
		if (!$new)
		{
			/** get relations form the database
			 * we do not make a compare, it is easier to to delete the relations
			 * and make new relations, a little bit dirty but it works
			 */
			$statement .= "DELETE FROM #__rd_sitemap_rel_viewset_has_view WHERE viewset = $row->id; \n";
		}
		foreach ($newRelations as $key => $val)
		{
			$statement .= "INSERT INTO #__rd_sitemap_rel_viewset_has_view (`view`,`viewset`,`ordering`) VALUES ($val,$row->id,$key);\n";
		}

		$db->setQuery($statement);
		//echo $db->getQuery();

		if (!$db->queryBatch(true,true))
		{
			return RdError::raiseWarning( 500, $db->stderr() );
		}

		switch ($task)
		{
			case 'apply':
				$link = 'index.php?option='.RDCOMPONENT.'&section=viewset'.'&task=edit&cid[]='. $row->id .'&hidemainmenu=1';
				print_r($rows);
				break;

			case 'save':
			default:
				$link = 'index.php?option='.RDCOMPONENT.'&section=viewset&task=view';
				break;
		}
		$this->setRedirect( $link, RdText::_( 'Item Saved' ) );
	}
}