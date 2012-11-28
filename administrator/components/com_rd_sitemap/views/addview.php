<?php
/**
 * Run Digital Sitemap
 * @author Robert Deutz (email contact@rdbs.net / site www.rdbs.de)
 * @version $Id: view.php 10 2007-08-24 14:19:42Z deutz $
 *
 * @copyright Copyright (C) 2005-2007 run-digital / www.run-digital.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 *
 * This is free software
 **/

// no direct access
defined( 'RDCOMPONENT' ) or die( 'Restricted access' );

/**
 * class RdSitemapViewView
 *
 * @package RD_Sitemap
 */
class RdSitemapViewView extends RdView
{

	/**
	 * Toolbar for the edit screen view/template
	 *
	 * @return void
	 */
	function setViewToolbar()
	{
		$cid = JRequest::getVar( 'cid', array(), 'method', 'array');
		
		RdMenuBar::title( empty( $cid ) ? RdText::_( 'New View' ) : RdText::_( 'Edit View' ), 'generic.png' );
		RdMenuBar::cancel( 'cancel' );
	}

	/**
	 * Edit screen for a view or ...
	 *
	 * @param object 	the item to edit
	 * @param array 	select lists, radio button, ...
	 * @param object 	parameters for the item
	 *
	 * @return void
	 */
	function viewitem( &$row, &$lists, &$params )
	{
 	  
 		// Check New item
		$new = (int) $row->id == 0;
	
		RdSitemapViewView::setViewToolbar();
		RdRequest::setVar( 'hidemainmenu', 1 );



		?>
		<script language="javascript" type="text/javascript">
		<!--
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}
			// do field validation
			if (form.title.value == "") {
				alert( "<?php echo RdText::_( 'You must provide a View name', true ); ?>" );
			} else {
				submitform( pressbutton );
			}
		}
		//-->
		</script>


		<form action="index.php" method="post" name="adminForm">

		<div class="col60">
			<fieldset class="adminform">
				<legend><?php echo RdText::_( 'Details' ); ?></legend>

				<table class="admintable">
				<tbody>
					<tr>
						<td width="20%" class="key">
							<label for="title">
								<?php echo RdText::_( 'View Name' ); ?>:
							</label>
						</td>
						<td width="80%">
							<input class="inputbox" type="text" name="title" id="title" size="50" value="<?php echo $row->title;?>" />
						</td>
					</tr>
					<?php if ($new) { ?>
						<tr>
							<td width="20%" class="key">
								<label for="type">
									<?php echo RdText::_( 'Type' ); ?>:
								</label>
							</td>
							<td width="80%"> 
								<?php echo $lists['type']; ?>
							</td>
						</tr>
					<?php } ?>


					</tbody>
					</table>
					</div>

		<div class="clr"></div>

		
		<input type="hidden" name="section" value="view" />
		<input type="hidden" name="option" value="com_rd_sitemap" />
		<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
		<input type="hidden" name="task" value="" />
		</form>
		<?php
	}


}