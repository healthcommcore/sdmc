<?php
/**
 * Run Digital Sitemap
 * @author Robert Deutz (email contact@rdbs.net / site www.rdbs.de)
 * @version $Id: default.php 53 2009-06-22 11:09:43Z pascal $
 *
 * @copyright Copyright (C) 2005-2007 run-digital
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 *
 * This is free software
 *
 * Default template for the Sitemap
 **/
defined( 'RDCOMPONENT' ) or die( 'Restricted access' );


$classdiv = $this->mparams->get('classdiv','');
if ($classdiv != '')
{
	echo '<div class="'.$classdiv.'">';
}
else
{
	echo '<div>';
}
// title
$mshowtitle = $this->mparams->get('show_title','');
$cshowtitle = $this->cparams->get('show_title',1);
$headerlevel = $this->mparams->get('headerlevel',$this->cparams->get('headerlevel',1));

if ($mshowtitle == 1 OR ($mshowtitle == '' AND $cshowtitle == 1))
{
	$title=$this->mparams->get('defaulttitle',$this->cparams->get('defaulttitle','Sitemap Component by Run Digital'));
	echo '<h'.$headerlevel.'>'.$title.'</h'.$headerlevel.'>';
}
else
{
	echo '<h'.$headerlevel.'>'.$this->menuname.'</h'.$headerlevel.'>';
}

$section_headerlevel=
$categorie_headerlevel= $headerlevel+2;
$title_headerlevel= $headerlevel+3;

// views

foreach ($this->data as $view)
{
	$hlevel			= $headerlevel+1;
	$viewparams		= $view->params;
	$showtitle 		= $viewparams->get('show_title',1);
	$showsection 	= $viewparams->get('show_section',1);
	$showcategory 	= $viewparams->get('show_category',1);
	$title 			= $viewparams->get('title',$view->title);
	if ($showtitle)
	{
		$hlevel_title = $hlevel;
		$hlevel++;
	}
	if ($showsection)
	{
		$hlevel_section = $hlevel;
		$hlevel++;
	}
	if ($showcategory)
	{
		$hlevel_category = $hlevel;
		$hlevel++;
	}

	if ($view->type == 'view')
	{
		// get the mode 0=class; 1= contextselector
		$mode = $this->mparams->get('mode',0);
		if ($mode) {
			$ul= '<ul class="sitemapcontent">';
		   	$linkclass = ' class="sitemap" ';
		} else {
			$ul='<ul>';
			$linkclass = '';
		}
		$sectionsave 	= '';
		$catsave 		= '';
		$close			= '';

		if(count($view->data) != 0)
		{
			if ($showtitle)
			{
				echo '<h'.$hlevel_title.'>'.$title.'</h'.$hlevel_title.'>';
			}
			$first = true;
			foreach($view->data as $elm)
			{
				if ($elm->section != $sectionsave)
				{
					echo $close;
					$sectionsave = $elm->section;
					if ($showsection)
					{
						echo '<h'.$hlevel_section.'>'.$sectionsave.'</h'.$hlevel_section.'>';
					}
					$catsave = $elm->category;
					if($showcategory)
					{
						echo '<h'.$hlevel_category.'>'.$catsave.'</h'.$hlevel_category.'>';
					}
					echo $ul;
					$close = '</ul>';
				}
				else
				{
					if ($elm->category != $catsave)
					{
						echo $close;
						$catsave = $elm->category;
						if($showcategory)
						{
							echo '<h'.$hlevel_category.'>'.$catsave.'</h'.$hlevel_category.'>';
						}
						echo $ul;
						$close = '</ul>';
					}
				}
				if ($first AND $elm->section == '' AND $elm->category == '' )
				{
					$first = false;
					echo $ul;
					$close = '</ul>';
				}
				
				require_once( JPATH_BASE.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php' );
				
				//print_r($elm);
				$seolink=JRoute::_($elm->link);
				if ($elm->slug) {
					 $seolink=JRoute::_(ContentHelperRoute::getArticleRoute($elm->slug, $elm->catslug));
				}
				if ($elm->secid) {
					 $seolink=JRoute::_(ContentHelperRoute::getSectionRoute($elm->secid));
				}
				if ($elm->bcatid) {
					 $seolink=JRoute::_(ContentHelperRoute::getCategoryRoute($elm->bcatid, $elm->bsecid));
				}
				echo '<li>'.'<a'.$linkclass.' href="'.JRoute::_($seolink).'" >'.$elm->title.'</a></li>';
			}
			echo $close;
		}
	}
	else
	{
		// get the mode 0=class; 1= contextselector
		$mode = $this->mparams->get('mode',0);
		if ($mode) {
			$ul= '<ul class="sitemapebene0">';
		   	$linkclass = ' class="sitemap" ';
			//$spanclass= ' class="unsichtbar" ';
		} else {
			$ul='<ul>';
			$linkclass = '';
			//$spanclass= ' style="display:none;" ';
		}

		// type = menu
		if(count($view->data) != 0)
		{
			if ($showtitle)
			{
				echo "\n";
				echo '<h'.$hlevel_title.'>'.$title.'</h'.$hlevel_title.'>';
			}
			$lastlevel=0;
			$actlevel=0;
			echo $ul;
			$close='</ul>';
			$liclose = '';
			
			//print_r($view->data);
			
			foreach($view->data as $elm)
			{
			/* ##################################################### MOD ##################################################### */

		$link = $elm->link;
		if ( isset($elm->id) ) {
			switch( @$elm->type ) {
				case 'separator':
					break;
				case 'url':
					if ( preg_match( "#^/?index\.php\?#", $link ) ) {
						if ( strpos( $link, 'Itemid=') === FALSE ) {
							if (strpos( $link, '?') === FALSE ) {
								$link .= '?Itemid='.$elm->id;
							} else {
								$link .= '&amp;Itemid='.$elm->id;
							}
						}
					}
					break;
				default:
					if ( strpos( $link, 'Itemid=' ) === FALSE ) {
						$link .= '&amp;Itemid='.$elm->id;
					}
					break;
			}
		}
	
			if (strcasecmp( substr( $link, 0, 9), 'index.php' ) === 0 ){
				$link = JRoute::_($link);
			}
		
		
		if ( ($elm->link=='index.php') OR  strpos( $elm->link, 'view=frontpage') ) { //HOME Links
		$link = '';
		}
			
			
		if ($elm->type == 'component') {
		$link='index.php?Itemid='.$elm->id;
		}
		/* ################################################### MOD ENDE ################################################### */
			
			
				//$link = $elm->link;
				$link = JRoute::_($link);
				
				
				$actlevel=$elm->sublevel;
				if ($lastlevel==$actlevel)
				{
					echo $liclose;
					echo '<li>'.'<a'.$linkclass.' href="'.$link.'" >'.$elm->name.'</a>';
					$liclose = '</li>';
				}
				else
				{
					if ($lastlevel<$actlevel)
					{
						echo $mode ? "<ul class=\"sitemapebene$actlevel\">\n" : "<ul>\n";
						echo '<li>'.'<a'.$linkclass.' href="'.$link.'" >'.$elm->name.'</a>';
						$liclose = '</li>';
					}
					else
					{
						$diff = $lastlevel - $actlevel;
						for ($i=1; $i <= $diff; $i++) {
							echo "</li></ul>\n";
						}
						echo $liclose;
						echo '<li>'.'<a'.$linkclass.' href="'.$link.'" >'.$elm->name.'</a>';
						$liclose = '</li>';
					}
				}
				$lastlevel = $elm->sublevel;
				echo "\n";
			}
			if ($lastlevel == 0)
			{
				echo $liclose;
				echo $close;
			}
			else
			{
				for ($i=0; $i <= $lastlevel; $i++) {
					echo "</li> </ul>\n";
				}
			}
		}
	}
}

// footer
$mshowfooter = $this->mparams->get('show_rd_footer','');
$cshowfooter = $this->cparams->get('show_rd_footer',1);
if ( $mshowfooter == 1 OR ($mshowfooter == '' AND $cshowfooter == 1) )
{
	echo '<p class="small" style="text-align:center">'.RdText::_('RDFOOTERTEXT').'</p>';
}
echo '</div>';
?>