<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
define('HOME', 1);
$itemID = JRequest::getVar('Itemid', '');
$page = JRequest::getURI();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" >
<head>
<jdoc:include type="head" />
<link href="<?php echo $this->baseurl ?>/images/favicon.ico" rel="shortcut icon" type="image/x-icon" />
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
<!----Google Analytics ---->
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-23343703-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
</head>
<body>
	<a class="invisible" href="<?php echo $page . '#main_content'; ?>">Skip to main content</a>
	<div id="wrapper">
    	<div id="main-stroke" class="main-stroke">
    
            <!-- HEADER -->
        
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
                 <?php if($this->countModules('search')) : ?>
                <div id ="search">
                    <jdoc:include type="modules" name="search" style="rounded" />
                </div>
                <?php endif; ?>
            </div>
            <div class="clr">
            </div>
            
            <!-- HOME MODULES -->
            
            <?php if($this->countModules('home_modules')) : ?>
                <div id="home_modules" class="home_modules">
                	<div class="center_position">
                        <jdoc:include type="modules" name="home_modules" style="rounded" />
                    </div>    
                </div>
            <?php endif; ?> 
               
		   <?php if ($itemID != HOME) : ?>
           <!--  MAIN CONTENT -->
         
                <div class="main">
                
                    <!-- MAIN COLUMN -->
                            
                      <div id="main_content" class="column">
                          <jdoc:include type="component" />
                      </div>
        
                    <!-- LEFT COLUMN -->
                
                      <div id="left_column" class="column">
                          <?php if($this->countModules('left_menu')) : ?>
                              <div id="left_menu">
                                  <jdoc:include type="modules" name="left_menu" style="rounded" />
                              </div>
                          <?php endif; ?>
                          <?php if($this->countModules('left_extra')) : ?>
                              <div id="left_extra">
                                  <jdoc:include type="modules" name="left_extra" style="rounded" />
                              </div>
                          <?php endif; ?>
                      </div>
                      
                      <!-- RIGHT COLUMN -->
                      
                      <div id="right_column" class="column">
                          <?php if($this->countModules('right_menu')) : ?>
                              <div id="right_menu">
                                  <jdoc:include type="modules" name="right_menu" style="rounded" />
                              </div>
                          <?php endif; ?>
                          <?php if($this->countModules('right_extra')) : ?>
                              <div id="right_extra">
                                  <jdoc:include type="modules" name="right_extra" style="rounded" />
                              </div>
                          <?php endif; ?>
                      </div>
                </div>
           <?php endif; ?>
            
           <!-- FOOTER -->
            
            <div id="footer" class="footer">
                <?php if($this->countModules('footer_top')) : ?>
                    <div id="footer_top">
                        <jdoc:include type="modules" name="footer_top" style="rounded" />
                    </div>    
                <?php endif; ?>
                <?php if($this->countModules('footer_bottom')) : ?>
                    <div id="footer_bottom">
                        <jdoc:include type="modules" name="footer_bottom" style="rounded" />
                    </div>    
                <?php endif; ?> 
            </div> 
        </div>
        <!-- BOTTOM -->
        
        <div id="bottom" class="bottom">
        	<?php if($this->countModules('bottom')) : ?>
            	<div id="bottom_module">
                	<jdoc:include type="modules" name="bottom_module" style="rounded" />
                </div>
            <? endif; ?>
       </div>                 
    </div>
</body>
</html>
