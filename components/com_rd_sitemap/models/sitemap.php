<?php
/**
 * Run Digital Sitemap
 * @author Robert Deutz (email contact@rdbs.net / site www.rdbs.de)
 * @version $Id: sitemap.php 10 2007-08-24 14:19:42Z deutz $
 *
 * @copyright Copyright (C) 2005-2007 run-digital
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 *
 * This is free software
 **/
defined( 'RDCOMPONENT' ) or die( 'Restricted access' );
/**
 * class RdSitemapModelSitemap
 *
 *  @package RD_Sitemap
 *
 */
class RdSitemapModelSitemap extends RdModel
{
	/** values to replace in the sql **/
	var $basicValues = array();

	/** component parameter object **/
	var $cparams = null;

	/** the data **/
	var $data = array();

	/** menu item parameter object **/
	var $mparams = null;

	/** parameter object for the actuall view **/
	var $viewParameter = null;

	/** the views to process **/
	var $views = array();

	/** id viewset **/
	var $viewset = 0;

	/**
	 * Constructor
	 */
	function __construct()
	{
		$this->getComponentParamsObj();

		// set up basis Values
		$this->setUpBasisValues();

		parent::__construct();
	}

	/**
	 * Get the component params
	 *
	 * @return parameter object Component Parameters
	 */
	function getComponentParamsObj()
	{
		if ($this->cparams === null )
		{
			$db =& RdFactory::getDBO();
			$query = "SELECT params " .
					"\n FROM #__components" .
					"\n WHERE parent = 0 AND `option` = '".RDCOMPONENT."' ";
			$db->setQuery($query);
			$this->cparams = new RdParameter($db->loadResult());
		}
		return $this->cparams;
	}

	/**
	 * Get the Data
	 *
	 * @return mixed the data
	 */
	function getData()
	{
		// get the view
		$this->views = $this->getViews();
		$data = array();
		// get data for a view
		foreach ($this->views as $view)
		{
			$data[]=$this->getViewData($view);
		}
		$this->data = $data;

		return $data;
	}


	/**
	 * Get a menu parameter for this menu item
	 * fetch the information out of the model
	 *
	 * @param string the parameter key to get
	 * @param string default value
	 *
	 */
	function getMenuParams($parameter, $default='')
	{
		if ($this->mparams == null)
		{
			$this->mparams=$this->_state->get('parameters.menu');
		}
		return $this->mparams->get($parameter,$default);
	}

	/**
	 * Get the menu params for this menu item
	 * fetch the information out of the model
	 *
	 *  @return parameter object Menu Parameters
	 */
	function getMenuParamsObj()
	{
		if ($this->mparams == null)
		{
			$this->mparams=$this->_state->get('parameters.menu');
		}
		return $this->mparams;
	}

	/**
	 * Get the Data for a view
	 *
	 * @param object the view sql source
	 * @return object the view plus additional data
	 */
	function getViewData($view)
	{
		$db	 					=& RdFactory::getDBO();
		$result 				= new stdClass();
		$result->title 			= $view->title;
		$result->type			= $view->type;
		$result->params 		= new RdParameter($view->params);
		$this->viewParameter 	= $result->params;
		$querybase 				= $view->content;
		$query = preg_replace_callback ("/\{.+\}/",array($this,"replaceValues"),$querybase);
		$db->setQuery($query);
		//	echo $db->getQuery().'<br>';
		$r=$db->loadObjectList();
		if ($db->getErrorNum()) {
			RdError::raiseWarning( 500, $db->getErrorMsg() .'<br /><br />'.RdText::_('This was your SQL:') .'<br /><br />'.$db->getQuery() .'<br /><br />'.RdText::_('End of SQL') );
		}

		if ($view->type == 'menu')
		{
			$r = $this->sortMenu($r);
		}
		$result->data= $r;
		return $result;
	}

	/**
	 * Get the views for a viewset
	 *
	 * @return array sql result (array of objects)
	 */
	function getViews()
	{
		$db =& RdFactory::getDBO();

		$viewsetId = $this->getViewset();

		if ( $viewsetId != 0 )
		{
			$query = "SELECT v.* " .
					"\n FROM " .
					"\n  #__rd_sitemap_rel_viewset_has_view as r, " .
					"\n  #__rd_sitemap_view as v," .
					"\n  #__rd_sitemap_viewset as vs " .
					"\n WHERE " .
					"\n  v.id = r.view " .
					"\n  AND r.viewset = vs.id " .
					"\n  AND r.viewset = $viewsetId " .
					"\n  AND v.published = '1' " .
					"\n  AND vs.published = '1' " .
					"\n ORDER BY r.ordering";
			$db->setQuery($query);

			$this->views = $db->loadObjectList();
			return $this->views;
		}
		else
		{
			RdError::raiseWarning( 500, RdText::_('Can not get the Viewset'));
		}
		return null;
	}

	/**
	 * Get the viewset for this request
	 *
	 * @return int the viewset id
	 */
	function getViewset()
	{
		$this->viewset = (int) RdRequest::getVar('id',0);
		return $this->viewset ;
	}

	/**
	 * Replace values, callbacl function for getViewData
	 *
	 * @param array the search result
	 * @return string the relaced value or an empty string if the replacment fails
	 */
	function replaceValues($m)
	{
		/**
		 * {NOW} -> Replace NOW
		 *
		 * or {Something {VAR}} -> REPLACE VAR, if not exists return an empty string
		 */
		$str = trim($m[0],'{}');
		if (strpos($str,'{') === false)
		{
			//{NOW}
			$r=trim($str);
			if(array_key_exists($r,$this->basicValues))
			{
				$result = $this->basicValues["$r"];
				return $result;
			}
		}
		else
		{
			// {Something {VAR}}
			$r=trim($str,'{}');
			$s=strpos($r, '{');
			$e=strpos($r, '}');
			if ($s!==false AND $e!==false)
			{
				$t1=substr($r,0,$s);
				$t2=trim(substr($r,$s,$e-$s+1));
				$t3=substr($r,$e+1);
				$t2=trim(trim($t2,'{}'));
				$param = $this->viewParameter->get($t2);
				if ($param != '')
				{
					$result = $t1.$param.$t3;
					return $result;
				}
			}
		}
		return '';
	}

	/**
	 * set up some values
	 */
	function setUpBasisValues()
	{
		// NOW
		$this->basicValues['NOW']=gmdate("Y-m-d H:i:s");

		// USER
		$user = RdFactory::getUser();
		$this->basicValues['USER_ID']	=$user->id;
		$this->basicValues['USER_GID']	=$user->gid;

		// ....
	}

	/**
	 * sort a menu view
	 *
	 * @param array the menu
	 * @return array the sorted menu
	 */
	function sortMenu($m)
	{
		$rootlevel = array();
		$sublevels = array();
		$r = 0;
		$s = 0;
		foreach ($m as $item)
		{
			if ($item->parent == 0)
			{
				//rootlevel
				$item->ebene = 0;
				$rootlevel[$r] = $item;
				$r++;
			}
			else
			{
				//sublevel
				$item->ebene = 1;
				$sublevels[$s] = $item;
				$s++;
			}
		}
		$maxlevels=$this->viewParameter->get('maxlevels','5');
		$z = 0;
		if ($s != 0 AND $maxlevels != 0) {
			foreach ($rootlevel as $elm) {
				$newmenuitems[$z] = $elm;
				$z++;
				$this->sortMenuRecursive($z,$elm->id,$sublevels,1,$maxlevels,$newmenuitems);
			}
		} else {
			$newmenuitems = $rootlevel;
		}
		return $newmenuitems;
	}

	/**
	 * sort a menu view Recursive through the tree
	 *
	 * @param int element number to work with
	 * @param int the parent id
	 * @param array the sublevels
	 * @param int the level
	 * @param int the maximun depth for the search
	 * @param array new menu
	 */
	function sortMenuRecursive(&$z,$id,$sl,$ebene,$maxlevels, &$nm)
	{
		if ($ebene > $maxlevels) {return true;}
		foreach ($sl as $selm) {
			if ($selm->parent == $id) {
				$selm->ebene = $ebene;
				$nm[$z] = $selm;
				$z++;
				$nebene = $ebene + 1;
				$this->sortMenuRecursive($z,$selm->id,$sl,$nebene,$maxlevels,$nm);
			}
		}
		return true;
	}
}
?>
