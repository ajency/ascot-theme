<?php

// Prioritize loading of some necessary core modules
if ( file_exists( get_template_directory() . '/lib/modules/core.redux/module.php' ) ) :
	require_once get_template_directory() . '/lib/modules/core.redux/module.php';
endif;
if ( file_exists( get_template_directory() . '/lib/modules/core/module.php' ) ) :
	require_once get_template_directory() . '/lib/modules/core/module.php';
endif;
if ( file_exists( get_template_directory() . '/lib/modules/core.layout/module.php' ) ) :
	require_once get_template_directory() . '/lib/modules/core.layout/module.php';
endif;
if ( file_exists( get_template_directory() . '/lib/modules/core.images/module.php' ) ) :
	require_once get_template_directory() . '/lib/modules/core.images/module.php';
endif;

// Include some admin options.
require_once locate_template( 'lib/admin-options.php' );

/*
 * Add a less file from our child theme to the parent theme's compiler.
 * This uses the 'shoestrap_compiler' filter that exists in the shoestrap compiler
 */
add_filter( 'shoestrap_compiler', 'shoestrap_child_styles' );
function shoestrap_child_styles( $bootstrap ) {
	return $bootstrap . '
	@import "' . get_stylesheet_directory() . '/assets/less/child.less";';
}


/*
 * Changes the output of the compiled CSS.
 */
add_filter( 'shoestrap_compiler_output', 'shoestrap_child_hijack_compiler' );
function shoestrap_child_hijack_compiler( $css ) {
	// $css = str_replace( '', '', $css );
	return $css;
}


/*
 * Enqueue the style.css file.
 *
 * It is recommended to use a less file as per the shoestrap_child_styles() function above.
 *
 * To have styles compiled and added in the main stylesheet,
 * try using the shoestrap_child_styles() function instead,
 */
function shoestrap_child_load_stylesheet() {
	wp_enqueue_style( 'shoestrap_child_css', get_stylesheet_uri() , true, null );
	wp_enqueue_style('custom-css', get_stylesheet_directory_uri() . '/assets/css/custom.css');

}
// Uncomment the line below to enqueue the stylesheet
add_action('wp_enqueue_scripts', 'shoestrap_child_load_stylesheet', 1000);


/*
 * Enqueue the stylesheet created with Grunt
 */
function shoestrap_child_grunt_stylesheet() {
	wp_enqueue_style( 'shoestrap_child_grunt_css', get_stylesheet_directory_uri() . '/assets/css/style.css', false, null );
}
// Uncomment the line below to enqueue the stylesheet
// add_action('wp_enqueue_scripts', 'shoestrap_child_grunt_stylesheet', 100);


/*
 * Remove page titles
 */
function shoestrap_empty_page_title() {}

function shoestrap_remove_page_titles() {
	if ( shoestrap_getvariable( 'remove_page_titles' ) == 1 ) :
		add_action( 'shoestrap_page_header_override', 'shoestrap_empty_page_title' );
	endif;
}
add_action( 'wp', 'shoestrap_remove_page_titles' );

/*
 * Custom page titles
 */
function ascot_custom_page_header() {  

	if( !is_page('Home') ) { ?>
	
		<div class="ascot_bg">
			<h1><?php echo get_the_title(); ?></h1>
		</div>

	<?php }
	ascot_breadcrumb();
}
add_action( 'shoestrap_page_header_override', 'ascot_custom_page_header' );

/*
 * Is Tree Function
 */
function is_tree($pid) {      // $pid = The ID of the page we're looking for pages underneath
	global $post;         // load details about this page
	if(is_page()&&($post->post_parent==$pid||is_page($pid))) 
               return true;   // we're at the page or at a sub page
	else 
               return false;  // we're elsewhere
};

/*
 * Custom page content
 */
function ascot_custom_page_content() {
  while (have_posts()) : the_post();

  	if ( is_page_template('template-page.php') ) {
	  ?>
	  	<div class="row">
	  		<div class="col-sm-3 page-menu">
	  			<?php 
	  				if ( is_page( array( 'market-news', 'upcoming-events' ) ) ) {
	  					
	  					global $post;
			  			$id = get_the_ID();
						$defaults = array(
							'menu'            => 'sidebar menu',
							'container'       => 'div',
							'menu_class'      => 'menu',
							'echo'            => true,
							'fallback_cb'     => 'wp_page_menu',
							'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s</ul>',
							'depth'           => 1,
						);
						
						wp_nav_menu( $defaults );

	  				} else {
			  			
			  			global $post;
			  			$parent_id = $post->post_parent;

			  			$args = array(
							'authors'      => '',
							'child_of'     => $parent_id,
							'date_format'  => get_option('date_format'),
							'depth'        => 0,
							'echo'         => 1,
							'exclude'      => '',
							'include'      => '',
							'link_after'   => '',
							'link_before'  => '',
							'post_type'    => 'page',
							'post_status'  => 'publish',
							'show_date'    => '',
							'sort_column'  => 'menu_order, post_title',
							'title_li'     => __(''), 
							'walker'       => ''
						); 

						echo '<h3 class="widget-title">'. get_the_title($parent_id) .'</h3>';
						
						echo '<ul>';
			  			wp_list_pages( $args );
			  			echo '</ul>';
			  		}
				?>
	  		</div>
	  		<div class="<?php if (is_tree(236)) { ?>col-sm-9 <?php } else { ?>col-sm-7<?php } ?> ascot-content">
	  			<?php the_content(); ?>
	  		</div>
	  		<?php if (is_tree(236)) { 
	  			// Full Page Width
	  		} else { ?>
		  		<div class="col-sm-2">
		  			<div class="contact_side">
						<h4>Get in touch with us for a no-obligation assessment.</h4>
						<h6>Call us: <strong>0800 7723147 / 01344 851250</strong></h6>
						<p>OR<br>
						</p><h6><a href="<?php echo site_url(); ?>/index.php/request-a-no-obligation-assessment/" title="Request a No-Obligation Assessment">Click Here</a> to fill out your details.</h6>
						<p>We will get in touch as soon as possible. We would love to be able to review your objectives and have a discussion with you. </p>
					</div>
		  		</div>
	  		<?php } ?>
	  	</div>
	  <?php
	} else {
		the_content();
		echo '<div class="clearfix"></div>';
	}
  endwhile;
}
add_action( 'shoestrap_content_page_override', 'ascot_custom_page_content' );

/*
 * Custom breadcrumbs
 */
function ascot_breadcrumb() {
	$delimiter = '&raquo;';
	$name = 'Home'; //text for the 'Home' link
	$currentBefore = '<span class="current">';
	$currentAfter = '</span>';
	if ( !is_home() && !is_front_page() || is_paged() ) {
		echo '<div id="crumbs">';
		global $post;
		$home = get_bloginfo('url');
		echo '<a href="' . $home . '">' . $name . '</a> ' . $delimiter . ' ';
		if ( is_category() ) {
			global $wp_query;
			$cat_obj = $wp_query->get_queried_object();
			$thisCat = $cat_obj->term_id;
			$thisCat = get_category($thisCat);
			$parentCat = get_category($thisCat->parent);
			if ($thisCat->parent != 0) echo(get_category_parents($parentCat, TRUE, ' ' . $delimiter . ' '));
			echo $currentBefore . 'Archive by Category &#39;';
			single_cat_title();
			echo '&#39;' . $currentAfter;
		} elseif ( is_day() ) {
			echo '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';
			echo '<a href="' . get_month_link(get_the_time('Y'),get_the_time('m')) . '">' . get_the_time('F') . '</a> ' . $delimiter . ' ';
			echo $currentBefore . get_the_time('d') . $currentAfter;
		} elseif ( is_month() ) {
			echo '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';
			echo $currentBefore . get_the_time('F') . $currentAfter;
		} elseif ( is_year() ) {
			echo $currentBefore . get_the_time('Y') . $currentAfter;
		} elseif ( is_single() ) {
			$cat = get_the_category(); $cat = $cat[0];
			echo get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');
			echo $currentBefore;
			the_title();
			echo $currentAfter;
		} elseif ( is_page() && !$post->post_parent ) {
			echo $currentBefore;
			the_title();
			echo $currentAfter;
		} elseif ( is_page() && $post->post_parent ) {
			$parent_id = $post->post_parent;
			$breadcrumbs = array();
			while ($parent_id) {
				$page = get_page($parent_id);
				$breadcrumbs[] = '<a href="' . get_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a>';
				$parent_id = $page->post_parent;
			}
			$breadcrumbs = array_reverse($breadcrumbs);
			foreach ($breadcrumbs as $crumb) echo $crumb . ' ' . $delimiter . ' ';
			echo $currentBefore;
			the_title();
			echo $currentAfter;
		} elseif ( is_search() ) {
			echo $currentBefore . 'Search results for &#39;' . get_search_query() . '&#39;' . $currentAfter;
		} elseif ( is_tag() ) {
			echo $currentBefore . 'Posts tagged &#39;';
			single_tag_title();
			echo '&#39;' . $currentAfter;
		} elseif ( is_author() ) {
			global $author;
			$userdata = get_userdata($author);
			echo $currentBefore . 'Articles posted by ' . $userdata->display_name . $currentAfter;
		} elseif ( is_404() ) {
			echo $currentBefore . 'Error 404' . $currentAfter;
		}
		if ( get_query_var('paged') ) {
			if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) echo ' (';
			echo __('Page') . ' ' . get_query_var('paged');
			if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) echo ')';
		}
		echo '</div>';
	}
}

/*
 * Custom loop Structure
 */
function ascot_custom_loop() { ?>
	<div class="row">
  		<div class="col-sm-3 page-menu">
  			<h3 class="widget-title">News &amp; Events</h3>
  			<?php
	  			global $post;
	  			$id = get_the_ID();
				$defaults = array(
					'menu'            => 'sidebar menu',
					'container'       => 'div',
					'menu_class'      => 'menu',
					'echo'            => true,
					'fallback_cb'     => 'wp_page_menu',
					'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s</ul>',
					'depth'           => 1,
				);
				
				wp_nav_menu( $defaults );

			?>
  		</div>
  		<div class="col-sm-9 ascot-blog">
			<?php 
			// Start the loop
			if ( have_posts() ) : while ( have_posts() ) : the_post();
			?>
				<article id="post-<?php echo the_ID(); ?>" <?php post_class(); ?> >
				  	<div class="hentry-pad">
				  		<section class="post-meta fix post-nocontent  media">
				  			<?php if ( has_post_thumbnail() ) { ?>
					  			<a style="width: 25%; max-width: 150px" title="Link To <?php the_title(); ?>" rel="bookmark" href="<?php the_permalink(); ?>" class="post-thumb img fix">
					  				<span class="c_img">
					  					<?php echo get_the_post_thumbnail(); ?> 
					  				</span>
					  			</a>
				  			<?php } ?>
				  			<section class="bd post-header fix">
				  				<section class="bd post-title-section fix">
				  				<hgroup class="post-title fix">
				  					<h2 class="entry-title"><a rel="bookmark" title="<?php the_title(); ?>" href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
								</hgroup>
								<div class="metabar">
									<div class="metabar-pad">
										<em>
											By <span class="author vcard sc"><span class="fn"><?php the_author_link(); ?></span></span> 
											On <?php the_time('F j, Y'); ?> &middot; 
											<span class="post-comments sc"><a href="<?php comments_link(); ?> ">Add Comment</a></span> 
											[<?php edit_post_link( 'Edit' ); ?> ]
										</em>
									</div>
								</div>
								</section> 
								<aside class="post-excerpt">
									<?php the_excerpt(); ?> 
								</aside>
								<a title="<?php the_title(); ?>" href="<?php the_permalink(); ?>" class="continue_reading_link">Read Full Article &rarr;</a>
							</section>
						</section>
					</div>
				</article>
			<?php  
			endwhile; else: ?>
				<p><?php _e('Sorry, no posts found.'); ?></p>
			<?php endif;?>
		</div>
	</div>
<?php 
}
add_action( 'shoestrap_override_index_loop', 'ascot_custom_loop' );