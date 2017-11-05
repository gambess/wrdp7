<?php
/**
 * 'wpspw_post' Shortcode
 * 
 * @package Blog Designer - Post and Widget
 * @since 1.0
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Function to handle the `wpspw_post` shortcode
 * 
 * @package Blog Designer - Post and Widget
 * @since 1.0
 */
function bdpw_get_posts( $atts, $content = null ) {
    
    // Shortcode Parameters
	extract(shortcode_atts(array(
		'limit' 				=> '20',
		'category' 				=> '',
		'grid' 					=> '1',
		'design'                => 'design-1',
		'show_author' 			=> 'true',
		'show_date' 			=> 'true',
		'show_category_name' 	=> 'true',
		'show_full_content' 	=> 'false',
		'show_content' 			=> 'true',
		'content_words_limit' 	=> '20',
		'show_tags'				=> 'true',
		'show_comments'			=> 'true',
		'pagination'			=> 'true',
		'pagination_type'		=> 'numeric',
	), $atts));
	
	$posts_per_page 	= !empty($limit) 					? $limit 						: '20';
	$design 	            = !empty($design) 					? $design 						: 'design-1';
	$cat 				= (!empty($category))				? explode(',',$category) 		: '';
	$gridcol			= !empty($grid) 					? $grid 						: '1';
	$show_author 		= ($show_author == 'false')			? 'false'						: 'true';
	$show_date 			= ( $show_date == 'false' ) 		? 'false'						: 'true';
	$show_category 		= ( $show_category_name == 'false' )? 'false' 						: 'true';
	$show_content 		= ( $show_content == 'false' ) 		? 'false' 						: 'true';
	$words_limit 		= !empty( $content_words_limit ) 	? $content_words_limit 			: 20;
	$show_full_content 	= ( $show_full_content == 'true' )	? 'true' 						: 'false';
	$show_tags 			= ( $show_tags == 'false' ) 		? 'false'						: 'true';
	$show_comments 		= ( $show_comments == 'false' ) 	? 'false'						: 'true';
	$postpagination 	= ($pagination == 'false')			? 'false'						: 'true';
	$pagination_type 	= ($pagination_type == 'prev-next')	? 'prev-next' 					: 'numeric';
	
	// Taking some globals
	global $post, $paged;

	// Taking some variables
	$count = 0;

	// Pagination parameter
	if(is_home() || is_front_page()) {
		$paged = get_query_var('page');
	} else {
		$paged = get_query_var('paged');
	}

	// WP Query Parameters
	$args = array ( 
		'post_type'      		=> BDPW_POST_TYPE,
		'post_status' 			=> array('publish'),
		'order'          		=> 'DESC',
		'orderby'        		=> 'date', 
		'posts_per_page' 		=> $posts_per_page,
		'ignore_sticky_posts'	=> true,
		'paged'         		=> $paged,
	);

    // Category Parameter
	if($cat != "") {
		
		$args['tax_query'] = array(
								array( 
									'taxonomy' 			=> BDPW_CAT,
									'field' 			=> 'term_id',
									'terms' 			=> $cat,
								));
	}

	// WP Query
	$query 		= new WP_Query($args);
	$post_count = $query->post_count;

	ob_start();

	// If post is there
	if ( $query->have_posts() ) {
?>
	
		<div class="sp_wpspwpost_static wpspw-<?php echo $design; ?> wpspw-grid-<?php echo $gridcol; ?> wpspw-clearfix">
			
			<?php while ( $query->have_posts() ) : $query->the_post();
				
				$count++;
				$css_class 	= '';
				$news_links = array();
				$feat_image = bdpw_get_post_featured_image( $post->ID, 'full', true );
				$terms 		= get_the_terms( $post->ID, 'category' );

				$tags 		= get_the_tag_list(__('Tags:', 'blog-designer-for-post-and-widget'),', ');
				$comments 	= get_comments_number( $post->ID );
				$reply		= ($comments <= 1) ? 'Reply' : 'Replies';
				
				if($terms) {
					foreach ( $terms as $term ) {
						$term_link = get_term_link( $term );
						$news_links[] = '<a href="' . esc_url( $term_link ) . '">'.$term->name.'</a>';
					}
				}
				$cate_name = join( " ", $news_links );
				
				if ( ( is_numeric( $grid ) && ( $grid > 0 ) && ( 0 == ($count - 1) % $grid ) ) || 1 == $count ) { $css_class .= ' first'; }
				if ( ( is_numeric( $grid ) && ( $grid > 0 ) && ( 0 == $count % $grid ) ) || $post_count == $count ) { $css_class .= ' last'; }
	            
	            // Include shortcode html file
				include( BDPW_DIR.'/templates/grid/'."$design".'.php' );

			endwhile; ?>
		</div><!-- end .sp_wpspwpost_static -->

		<?php if( $postpagination == "true" && ($query->max_num_pages > 1) ) { ?>
			<div class="wpspw_pagination wpspw-clearfix">

				<?php if($pagination_type == "numeric") {

					echo bdpw_post_pagination( array( 'paged' => $paged , 'total' => $query->max_num_pages ) );

				} else { ?>
					<div class="button-blog-p"><?php next_posts_link( ' Next >>', $query->max_num_pages ); ?></div>
					<div class="button-blog-n"><?php previous_posts_link( '<< Previous' ); ?> </div>
				<?php } ?>
			</div><!-- end .blog_pagination -->
		<?php }
	} // end of have_post()
	
	wp_reset_query(); // Reset WP Query

    $content .= ob_get_clean();
    return $content;
}

// 'wpspw_post' Shortcode
add_shortcode('wpspw_post', 'bdpw_get_posts');