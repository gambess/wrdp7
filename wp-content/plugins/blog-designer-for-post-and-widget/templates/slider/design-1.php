<div class="wpspw-post-slides">
	
	<div class="wpspw-post-content-position">
		
		<div class="wpspw-post-content-left wpspw-medium-8 wpspw-columns">
	
			<?php if($show_category == "true") { ?>
				<div class="wpspw-post-categories">
					<?php echo $cate_name; ?>			
				</div> <!-- end .wpspw-post-categories -->
			<?php } ?>
			
			<h2 class="wpspw-post-title">
	
				<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
			</h2>
			
			<?php if($show_date == "true" || $show_author == 'true')    {  ?>
	
				<div class="wpspw-post-date">		
	
					<?php if($show_author == 'true') { ?> <span><?php  esc_html_e( 'By', 'wp-stylish-post' ); ?> <?php the_author(); ?></span><?php } ?>
				
					<?php echo ($show_author == 'true' && $show_date == 'true') ? '&nbsp;/&nbsp;' : '' ?>
				
					<?php if($show_date == "true") { echo get_the_date(); } ?>
				</div><!-- end .wpspw-post-date -->
			<?php }

			if($show_content == "true") { ?>
				
				<div class="wpspw-post-content">
					<div><?php echo bdpw_get_post_excerpt( $post->ID, get_the_content(), $words_limit); ?></div>
					<a class="wpspw-readmorebtn" href="<?php the_permalink(); ?>"><?php _e('Read More', 'blog-designer-for-post-and-widget'); ?></a>
				</div><!-- end .wpspw-post-content -->

			<?php } ?>
			<?php if(!empty($tags) && $show_tags == 'true') { ?>
				<div class="wpswp-post-tags">
					<?php echo $tags;  ?>
				</div>
			<?php } ?>

			<?php if(!empty($comments) && $show_comments == 'true') { ?>
				<div class="wpswp-post-comments">
					<a href="<?php the_permalink(); ?>/#comments"><?php echo $comments.' '.$reply;  ?></a>
				</div>
			<?php } ?>
		</div><!-- end .wpspw-post-content-left -->
		
		<div class="wpspw-post-image-bg">
			
			<?php if( !empty($feat_image) ) { ?>
			
				<a href="<?php the_permalink(); ?>">
					<img src="<?php echo $feat_image; ?>" alt="<?php the_title(); ?>" />
				</a>
			<?php } ?>
		</div><!-- end .wpspw-post-image-bg -->
	</div><!-- end .wpspw-post-content-position -->
</div><!-- end .wpspw-post-slides -->