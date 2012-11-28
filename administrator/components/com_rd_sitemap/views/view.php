<?php
/**
 * Run Digital Sitemap
 * @author Robert Deutz (email contact@rdbs.net / site www.rdbs.de)
 * @version $Id: view.php 53 2009-06-22 11:09:43Z pascal $
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
	 * Toolbar for the view list
	 *
	 * @return void
	 */
	function setListToolbar()
	{
		RdMenuBar::title( RdText::_( 'View Manager' ), 'generic.png' );
		RdMenuBar::customX( 'install', 'upload.png', 'upload_f2.png', 'Install', false );
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
	 * shows a list of views
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
		RdSitemapViewView::setListToolbar();
		
		$user =& RdFactory::getUser();
		/* overlib changed to jimport ... */
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
					<th nowrap="nowrap" class="title">
						<?php echo RdHTML::_('grid.sort',  'Type', 'v.type', @$lists['order_Dir'], @$lists['order'] ); ?>
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
				$link =  'index.php?option=com_rd_sitemap&section=view&task=edit&cid[]='. $row->id ;

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
							<a href="<?php echo $link; ?>" title="<?php echo RdText::_( 'Edit View' ); ?>">
								<?php echo $row->title; ?></a>
							<?php
						}
						?>
					</td>
					<td align="center">
						<?php echo $row->type; ?>
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

		<input type="hidden" name="section" value="view" />
		<input type="hidden" name="option" value="com_rd_sitemap" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $lists['order']; ?>" />
		<input type="hidden" name="filter_order_Dir" value="" />
		</form>
		<?php
	}

	/**
	 * Toolbar for the edit screen view/template
	 *
	 * @return void
	 */
	function setViewToolbar()
	{
		$cid = JRequest::getVar( 'cid', array(), 'method', 'array');
		
		RdMenuBar::title( empty( $cid ) ? RdText::_( 'New View' ) : RdText::_( 'Edit View' ), 'generic.png' );
		RdMenuBar::save( 'save' );
		RdMenuBar::apply('apply');
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


					<tr>
						<td width="20%" class="key">
							<label for="description">
								<?php echo RdText::_( 'Description' ); ?>:
							</label>
						</td>
						<td width="80%">
							<textarea class="inputbox" type="text" name="description" id="description" rows="5" cols="80" ><?php echo $row->description;?> </textarea>
						</td>
					</tr>
					<?php if ($row->type!='menu')  { ?>
					<tr>
						<td width="20%" class="key">
							<label for="content">
							<?php
								
									echo RdText::_( 'View' );
								
							?>:
							</label>
						</td>
						<td width="80%">
							<textarea class="inputbox" type="text" name="content" id="content" rows="20" cols="80" ><?php echo $row->content;?> </textarea>
						</td>
					</tr>
					<?php } else { ?>
					<input type="hidden" name="content" value="SELECT
  m.*
FROM
  #__menu as m
WHERE
  m.published = '1'
  AND m.access <= '{USER_GID}'
  {AND m.menutype = '{menutype}' }
  ORDER BY m.menutype,m.parent, m.sublevel, m.ordering" />
  <?php } ?>
					
					
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
		</div>
		<div class="col40">
		
		  <fieldset class="adminform">
				<legend><?php echo RdText::_( 'Parameters' ); ?></legend>

				<table  class="admintable">
					<tr>
						<td>

							<?php
								echo $params->render();
							  if ($row->type == 'view') echo $params->render('params','extra');
							 ?>
						</td>
					</tr>
				</table>
			</fieldset>
		
			<fieldset class="adminform">
				<legend><?php echo RdText::_( 'Informations' ); ?></legend>

				<table class="admintable">
					<tr>
						<td width="30%" class="key">
							<label for="id">
								<?php echo RdText::_( 'Id' ); ?>:
							</label>
						</td>
						<td width="70%">
							<?php echo $row->id;?>
						</td>
					</tr>
					<?php if (!$new) { ?>
						<tr>
							<td width="30%" class="key">
								<label for="type">
									<?php echo RdText::_( 'Type' ); ?>:
								</label>
							</td>
							<td width="70%">
								<?php echo $row->type; ?>#
							</td>
						</tr>
					<?php } ?>
					<tr>
						<td width="30%" class="key">
							<label for="author">
								<?php echo RdText::_( 'Author' ); ?>:
							</label>
						</td>
						<td width="70%">
							<?php echo $row->author;?>
						</td>
					</tr>
					<tr>
						<td width="30%" class="key">
							<label for="author_email">
								<?php echo RdText::_( 'Author Email' ); ?>:
							</label>
						</td>
						<td width="70%">
							<?php echo $row->author_email;?>
						</td>
					</tr>
					<tr>
						<td width="30%" class="key">
							<label for="author_url">
								<?php echo RdText::_( 'Author Url' ); ?>:
							</label>
						</td>
						<td width="70%">
							<?php echo $row->author_url;?>
						</td>
					</tr>
					<tr>
						<td width="30%" class="key">
							<label for="creation_date">
								<?php echo RdText::_( 'Creation Date' ); ?>:
							</label>
						</td>
						<td width="70%">
							<?php echo $row->creation_date;?>
						</td>
					</tr>
					<tr>
						<td width="30%" class="key">
							<label for="version">
								<?php echo RdText::_( 'Version' ); ?>:
							</label>
						</td>
						<td width="70%">
							<?php echo $row->version;?>
						</td>
					</tr>

				</table>
			</fieldset>
		</div>

		<div class="clr"></div>

		<input type="hidden" name="section" value="view" />
		<input type="hidden" name="option" value="com_rd_sitemap" />
		<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
		<input type="hidden" name="task" value="" />
		</form>
		<?php
	}

	/**
	 * Toolbar form the installation screen
	 *
	 * @return void
	 */
	function setInstallToolbar()
	{
		RdMenuBar::title( RdText::_('View Installer'), 'generic.png' );
		RdMenuBar::custom( 'doinstall', 'upload.png', 'upload_f2.png', 'Install',false );
		RdMenuBar::cancel( 'cancelinstall' );
	}

	/**
	 * Installation screen form installing a view or template
	 *
	 * @return void
	 */
	function install(  )
	{
		RdSitemapViewView::setInstallToolbar();
		RdRequest::setVar( 'hidemainmenu', 1 );

		?>
		<script language="javascript" type="text/javascript">
		<!--
		function submitbutton(pressbutton) {
			var form = document.adminForm;
			if (pressbutton == 'cancelinstall') {
				submitform( pressbutton );
				return;
			}
			// do field validation
			if (form.userfile.value == "") {
				alert( "<?php echo RdText::_( 'You must select a File', true ); ?>" );
			} else {
				submitform( pressbutton );
			}
		}
		//-->
		</script>
		<form enctype="multipart/form-data" action="index.php" method="post" name="adminForm">

		<div class="col100">
			<fieldset class="adminform">
				<legend><?php echo RdText::_( 'Install' ); ?></legend>

				<table class="admintable">
				<tbody>
					<tr>
						<td width="20%" class="key">
							<label for="userfile">
								<?php echo RdText::_( 'File Name' ); ?>:
							</label>
						</td>
						<td width="80%">
							<input class="uploadbox" type="file" name="userfile" id="userfile" size="50" value="" />
						</td>
					</tr>
				</tbody>
				</table>
			</fieldset>
		</div>
		<div class="clr"></div>

		<input type="hidden" name="section" value="view" />
		<input type="hidden" name="option" value="com_rd_sitemap" />
		<input type="hidden" name="task" value="" />
		</form>
		<?php
	}

}