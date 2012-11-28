<?php
/**
 * Run Digital Sitemap
 * @author Robert Deutz (email contact@rdbs.net / site www.rdbs.de)
 * @version $Id: viewset.php 10 2007-08-24 14:19:42Z deutz $
 *
 * @copyright Copyright (C) 2005-2007 run-digital
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 *
 * This is free software
 **/

// no direct access
defined( 'RDCOMPONENT' ) or die( 'Restricted access' );

/**
 * class RdSitemapViewViewset
 *
 * @package RD_Sitemap
 */
class RdSitemapViewViewset extends RdView
{

	/**
	 * Toolbar for the viewset list
	 *
	 * @return void
	 */
	function setListToolbar()
	{
		RdMenuBar::title( RdText::_( 'Viewset Manager' ), 'generic.png' );
 		RdMenuBar::publishList();
		RdMenuBar::unpublishList();
		RdMenuBar::customX( 'copy', 'copy.png', 'copy_f2.png', 'Copy' );
		RdMenuBar::deleteList();
		RdMenuBar::editListX();
		RdMenuBar::addNewX();
		RdMenuBar::preferences('com_rd_sitemap', '300');
		RdMenuBar::customX( 'about', 'default.png', 'default_f2.png', 'About', false );
	}

	/**
	 * shows a list of viewssets
	 *
	 * @param array 	rows to show
	 * @param object 	pagination object
	 * @param array 	select lists, radio button, ...
	 *
	 * @return void
	 *
	 */
	function viewList( &$rows, &$pageNav, &$lists )
	{
		RdSitemapViewViewset::setListToolbar();
		$user =& RdFactory::getUser();

		RdHTML::_('behavior.tooltip');
		?>
		<form action="index.php" method="post" name="adminForm">
		<table>
		<tr>
			<td align="left" width="100%">
				<?php echo RdText::_( 'Filter' ); ?>:
				<input type="text" name="search" id="search" value="<?php echo $lists['search'];?>" class="text_area" onchange="document.adminForm.submit();" />
				<button onclick="this.form.submit();"><?php echo RdText::_( 'Go' ); ?></button>
				<button onclick="getElementById('search').value='';this.form.submit();"><?php echo RdText::_( 'Reset' ); ?></button>
			</td>
			<td nowrap="nowrap">
				<?php
				echo $lists['state'];
				?>
			</td>
		</tr>
		</table>

			<table class="adminlist">
			<thead>
				<tr>
					<th width="20">
						<?php echo RdText::_( 'Num' ); ?>
					</th>
					<th width="20">
						<input type="checkbox" name="toggle" value=""  onclick="checkAll(<?php echo count( $rows ); ?>);" />
					</th>
					<th nowrap="nowrap" class="title">
						<?php echo RdHTML::_('grid.sort',  'Name', 'v.title', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
					<th width="5%" nowrap="nowrap">
						<?php echo RdHTML::_('grid.sort',   'Published', 'v.published', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
					<th width="1%" nowrap="nowrap">
						<?php echo RdHTML::_('grid.sort',   'ID', 'v.id', @$lists['order_Dir'], @$lists['order'] ); ?>
					</th>
				</tr>
			</thead>
			<?php
			$k = 0;
			for ($i=0, $n=count( $rows ); $i < $n; $i++) {
				$row = &$rows[$i];
				$link =  'index.php?option=com_rd_sitemap&amp;section=viewset&amp;task=edit&amp;cid[]='. $row->id ;

				$published 		= RdHTML::_('grid.published', $row, $i );
				$checked 		= RdHTML::_('grid.checkedout',   $row, $i );
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td align="center">
						<?php echo $pageNav->getRowOffset($i); ?>
					</td>
					<td align="center">
						<?php echo $checked; ?>
					</td>
					<td>
						<?php
						if ( $row->checked_out && ( $row->checked_out != $user->get ('id') ) ) {
							echo $row->title;
						} else {
							?>
							<a href="<?php echo $link; ?>" title="<?php echo RdText::_( 'Edit Viewset' ); ?>">
								<?php echo $row->title; ?></a>
							<?php
						}
						?>
					</td>
					<td align="center">
						<?php echo $published;?>
					</td>
					<td align="center">
						<?php echo $row->id; ?>
					</td>
				</tr>
				<?php
				$k = 1 - $k;
			}
			?>
			<tfoot>
				<td colspan="13">
					<?php echo $pageNav->getListFooter(); ?>
				</td>
			</tfoot>
			</table>

		<input type="hidden" name="section" value="viewset" />
		<input type="hidden" name="option" value="com_rd_sitemap" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $lists['order']; ?>" />
		<input type="hidden" name="filter_order_Dir" value="" />
		</form>
		<?php
	}

	/**
	 * Toolbar for the edit screen viewset
	 *
	 * @return void
	 */
	function setViewsetToolbar()
	{
		$cid = RdRequest::getVar( 'cid', array(), 'method', 'array');

		RdMenuBar::title( empty( $cid ) ? RdText::_( 'New Viewset' ) : RdText::_( 'Edit Viewset' ), 'generic.png' );
		RdMenuBar::save( 'save' );
		RdMenuBar::apply('apply');
		RdMenuBar::cancel( 'cancel' );
	}

	/**
	 * Edit screen for a viewset
	 *
	 * @param object 	the item to edit
	 * @param array 	select lists, radio button, ...
	 *
	 * @return void
	 */
	function viewset( &$row, &$lists )
	{
		RdSitemapViewViewset::setViewsetToolbar();
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
			if (form.name.value == "") {
				alert( "<?php echo RdText::_( 'You must provide a Viewset name', true ); ?>" );
			} else {
				for (i = 0; i < form.used_views.length; ++i)
    				form.used_views.options[i].selected = true
				submitform( pressbutton );
			}
		}
		//-->
		</script>

		<script language="javascript" type="text/javascript">
		<!--
			window.addEvent('domready', function(){ rdinit() });

			function rdinit() {	updateLeft();updateRight(); };

			function updateLeft()
			{
				var right = $('used_views');
				var left = $('views');

				$$('.leftoption').each(function(item)
				{
					item.removeEvents('dblclick');
					item.addEvent('dblclick',function(e) {
		                //alert('tach links');

						item.removeClass('leftoption');
						item.addClass('rightoption');

						item.inject(right);
						updateRight();
					});
				});
			 }

			function updateRight()
			{
				var right = $('used_views');
				var left = $('views');

				$$('.rightoption').each(function(item)
				{
					item.removeEvents('dblclick');
					item.addEvent('dblclick',function(e) {
		                //alert('tach rechts');
						item.removeClass('rightoption');
						item.addClass('leftoption');
						item.inject(left);

						updateLeft();
					});
				});
				new Sortables(right);
			 }

		//-->
		</script>


		<form action="index.php" method="post" name="adminForm" >

		<div class="col100">
			<fieldset class="adminform">
				<legend><?php echo RdText::_( 'Details' ); ?></legend>

				<table class="admintable">
				<tbody>
					<tr>
						<td width="20%" class="key">
							<label for="name">
								<?php echo RdText::_( 'Viewset Name' ); ?>:
							</label>
						</td>
						<td width="80%">
							<input class="inputbox" type="text" name="title" id="title" size="50" value="<?php echo $row->title;?>" />
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo RdText::_( 'Pubished' ); ?>:
						</td>
						<td>
							<?php echo $lists['published']; ?>
						</td>
					</tr>
				</tbody>
				</table>
			</fieldset>

			<fieldset class="adminform">
				<legend><?php echo RdText::_( 'View Management' ); ?></legend>

				<table class="admintable">
				<tbody>
					<tr>
					<td colspan="2">
						<?php
						echo RdText::_('Doubleclick to move item in the other select box');
						echo '<br>';
						echo RdText::_('Drag item to sort used views');
						?>
					</td>
					</tr>
					<tr>
						<td width="50%">
							<label for="name">
								<?php echo RdText::_( 'Available Views' ); ?>:
							</label>
							<br />
							<?php echo $lists['views']; ?>
						</td>
						<td width="50%">
							<label for="name">
								<?php echo RdText::_( 'Used Views' ); ?>:
							</label>
							<br />
							<?php echo $lists['used_views']; ?>
						</td>
					</tr>
				</tbody>
				</table>
		</div>
		<div class="clr"></div>

		<input type="hidden" name="section" value="viewset" />
		<input type="hidden" name="option" value="com_rd_sitemap" />
		<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
		<input type="hidden" name="task" value="" />
		</form>
		<?php
	}
}