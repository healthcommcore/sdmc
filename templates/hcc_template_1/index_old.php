<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" >
<head>

<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/hcc_template_1/css/template.css" type="text/css" />
<!--[if IE 7]>
<link href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template?>/css/ie7.css" rel="stylesheet" type="text/css" />	
<![endif]-->	
<!--[if lte IE 6]>
<link href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/ie6.css" rel="stylesheet" type="text/css" />
<![endif]-->

<?php if($this->direction == 'rtl') : ?>
	<link href="<?php echo $this->baseurl ?>/templates/rhuk_milkyway/css/template_rtl.css" rel="stylesheet" type="text/css" />
<?php endif; ?>

</head>
<body>
	<div id="wrapper">
    <?php if($this->countModules('home_header')) : ?>
        	<div id ="home_header" class="home_header">
                <jdoc:include type="modules" name="home_header" style="rounded" />
            </div>
        <?php else: ?>
        	<div id ="small_header">
            	<jdoc:include type="modules" name="small_header" style="rounded" />
            </div>
        <?php endif; ?>
        
        <!-- MENUBAR -->
        
        <div id="menubar" class="menubar">
        	<?php if($this->countModules('horiz_menu')) : ?>
            	<div id ="horiz_menu" class="horiz_menu">
                	<jdoc:include type="modules" name="horiz_menu" style="rounded" />
                </div>
            <?php endif; ?>
            <div id ="search">
            	<jdoc:include type="modules" name="search" style="rounded" />
            </div>
        </div>
        
        <div class="main">
        	<div id="center" class="column">
            	<jdoc:include type="component" />
            </div>
            
            <div id="left" class="column">
            LEFT COLUMN
            </div>
            
            
            <div id="right" class="column">
            RIGHT COLUMN
            </div>
            <div class="clr">
        </div>
        </div>
    </div>	
</body>
</html>
