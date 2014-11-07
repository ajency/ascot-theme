<?php

// Do not edit this file.
// If you need to override this template you can use the 'shoestrap_header_top_navbar_override' hook.
?>
<header class="banner <?php echo shoestrap_navbar_class(); ?>" role="banner">
  <div class="logo-bar">


	
	<div class="row">
	<div class="logo-bar">
		  <?php
      if ( shoestrap_getVariable( 'navbar_brand' ) != 0 ) :
        echo '<a class="navbar-brand ' . shoestrap_branding_class( false ) . '" href="' . home_url() . '/">';

        if ( shoestrap_getVariable( 'navbar_logo' ) == 1 )
          shoestrap_logo();
        else
          bloginfo( 'name' );
        echo '</a>';
      endif;
      ?>
	</div>
	<div class="impact fix"><div class="impact-desc"><h6>Want more information or need advice?</h6><h4>Call: 0800 7723147 / 01344 851250</h4></div></div>
	<div class="up_login"> <a href="http://ascotwm.com/index.php/clients/"><button type="button" class="btn"> <i class="el-icon-torso"></i> &nbsp;Client Login</button></a> &nbsp;<a href="http://ascotwm.com/index.php/clients/"><button type="button" class="btn"> <i class="el-icon-group"></i> &nbsp;Advisor Login</button></a></div>
	</div>

    
    

    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".nav-main, .nav-extras">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
    </div>
	    <!--<div class="nav-extras">
      <?php //do_action( 'shoestrap_pre_main_nav' ); ?>
    </div>-->

	<?php do_action( 'shoestrap_post_main_nav' ); ?>
    <nav class="nav-main navbar-collapse collapse" role="navigation">
       <?php
        do_action( 'shoestrap_inside_nav_begin' );
        if (has_nav_menu('primary_navigation')) :
          wp_nav_menu( array( 'theme_location' => 'primary_navigation', 'menu_class' => shoestrap_nav_class_pull(),'walker'=> new Roots_Nav_Walker() ) );
        endif;
        do_action( 'shoestrap_inside_nav_end' );
       ?>
	   </nav>
	   
	     </div>
</header>