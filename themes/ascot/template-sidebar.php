<?php
/*
Template Name: page with sidebar
*/

if ( !has_action( 'shoestrap_page_header_override' ) )
  get_template_part('templates/page', 'header');
else
  do_action( 'shoestrap_page_header_override' );


  
  ?>
  	<div class="row">
  		
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
				  				
								<aside class="post-excerpt">
									<?php the_content(); ?> 
								</aside>
								
							</section>
						</section>
					</div>
				</article>
			<?php  
			endwhile; else: ?>
				<p><?php _e('Sorry, no posts found.'); ?></p>
			<?php endif;?>
		</div>
		<div class="col-sm-3">
  		<?php dynamic_sidebar( 'secondary-sidebar' ); ?>
  		</div>
	</div>
