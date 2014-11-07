<?php
/*
Template Name: sidebar template
*/

if ( !has_action( 'shoestrap_page_header_override' ) )
get_template_part('templates/page', 'header');
else
 do_action( 'shoestrap_page_header_override' );

if ( !has_action( 'shoestrap_content_page_override' ) )
  get_template_part('templates/content', 'page');
else
 //do_action( 'shoestrap_content_page_override' );
?>
<div class="row">
  <div class="col-md-3 page-menu">
	<?php 
	  		if ( is_page( array( 'market-news', 'upcoming-events' ) ) ) {
	  					
	  					global $post;
			  			$id = get_the_ID();

$parent_id = $post->post_parent;

echo '<h3 class="widget-title">'. get_the_title($parent_id) .'</h3>';
						$defaults = array(
							'menu'            => 'sidebar-menu',
							'container'       => 'div',
							'menu_class'      => 'menu',
							'echo'            => true,
							'fallback_cb'     => 'wp_page_menu',
							'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s</ul>',
							'depth'           => -1,
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
    <div class="col-md-9 ascot-content">
 <?php the_content(); ?>
    </div>
  
</div>