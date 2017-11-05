<?php
/**
 * Plugin generic functions file
 *
 * @package Blog Designer - Post and Widget
 * @since 1.0
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Function to unique number value
 * 
 * @package Blog Designer - Post and Widget
 * @since 1.0.0
 */
function bdpw_get_unique() {
    static $unique = 0;
    $unique++;

    return $unique;
}

/**
 * Function to add array after specific key
 * 
 * @package Blog Designer - Post and Widget
 * @since 1.0
 */
function bdpw_add_array(&$array, $value, $index, $from_last = false) {
    
    if( is_array($array) && is_array($value) ) {

        if( $from_last ) {
            $total_count    = count($array);
            $index          = (!empty($total_count) && ($total_count > $index)) ? ($total_count-$index): $index;
        }
        
        $split_arr  = array_splice($array, max(0, $index));
        $array      = array_merge( $array, $value, $split_arr);
    }
    
    return $array;
}

/**
 * Function to get post featured image
 * 
 * @package Blog Designer - Post and Widget
 * @since 1.0
 */
function bdpw_get_post_featured_image( $post_id = '', $size = 'full', $default_img = false ) {
  
    $size   = !empty($size) ? $size : 'full';
  
    $image  = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), $size );

    if( !empty($image) ) {
        $image = isset($image[0]) ? $image[0] : '';
    }

    return $image;
}

/**
 * Function to get post excerpt
 * 
 * @package Blog Designer - Post and Widget
 * @since 1.0
 */
function bdpw_get_post_excerpt( $post_id = null, $content = '', $word_length = '55', $more = '...' ) {
    
    $has_excerpt    = false;
    $word_length    = !empty($word_length) ? $word_length : '55';
    
    // If post id is passed
    if( !empty($post_id) ) {
        if (has_excerpt($post_id)) {

            $has_excerpt    = true;
            $content        = get_the_excerpt();

        } else {
            $content = !empty($content) ? $content : get_the_content();
        }
    }

    if( !empty($content) && (!$has_excerpt) ) {
        $content = strip_shortcodes( $content ); // Strip shortcodes
        $content = wp_trim_words( $content, $word_length, $more );
    }
    
    return $content;
}

/**
 * Pagination function for grid
 * 
 * @package  Blog Designer - Post and Widget
 * @since 1.0
 */
function bdpw_post_pagination($args = array()){
    
    $big = 999999999; // need an unlikely integer

    $paging = apply_filters('bdpw_pro_blog_paging_args', array(
                    'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
                    'format'    => '?paged=%#%',
                    'current'   => max( 1, $args['paged'] ),
                    'total'     => $args['total'],
                    'prev_next' => true,
                    'prev_text' => __('« Previous', 'blog-designer-for-post-and-widget'),
                    'next_text' => __('Next »', 'blog-designer-for-post-and-widget'),
                ));
    
    echo paginate_links($paging);
}