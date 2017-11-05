<?php

// Structured Data Type
add_filter( 'amp_post_template_metadata', 'ampforwp_structured_data_type', 20, 1 );
function ampforwp_structured_data_type( $metadata ){
	global $redux_builder_amp, $post;
	$post_types 	= '';
	$set_sd_post 	= '';
	$set_sd_page 	= '';	

	$set_sd_post 	= $redux_builder_amp['ampforwp-sd-type-posts'];
	$set_sd_page 	= $redux_builder_amp['ampforwp-sd-type-pages'];

	if ( empty( $set_sd_post ) ) {
		$set_sd_post = 'BlogPosting';
	}

	if ( empty( $set_sd_page ) ) {
		$set_sd_page = 'BlogPosting';
	}
	 
	$post_types = ampforwp_get_all_post_types();

	if ( $post_types ) { // If there are any custom public post types.
    	foreach ( $post_types  as $post_type ) {
    		
        	if($post->post_type == 'post'){
        		$metadata['@type'] = $set_sd_post;
        	}

        	if($post->post_type == 'page'){
        		$metadata['@type'] = $set_sd_page;
        	}

        	if( $post->post_type == 'page' ||  $post->post_type == 'post'  ){
        		continue;
        	}

        	if($post->post_type == $post_type){
        		if ( empty( $redux_builder_amp['ampforwp-sd-type-'.$post_type.''] ) ) {
					$redux_builder_amp['ampforwp-sd-type-'.$post_type.''] = 'BlogPosting';
				}
        		$metadata['@type'] = $redux_builder_amp['ampforwp-sd-type-'.$post_type.''];
        	}


        }
    }

	return $metadata;
}
// VideoObject
add_filter( 'amp_post_template_metadata', 'ampforwp_structured_data_video_thumb', 20, 1 );
if( ! function_exists('ampforwp_structured_data_video_thumb') ){
	function ampforwp_structured_data_video_thumb( $metadata ){
		global $redux_builder_amp, $post;
		if($metadata['@type'] == 'VideoObject'){
			$post_image_id = '';
			$post_image_id = get_post_thumbnail_id( get_the_ID() );
			$post_image = wp_get_attachment_image_src( $post_image_id, 'full' );
			$structured_data_video_thumb_url = '';
			// If there's no featured image, take default from settings
			if ( $post_image == false) {
				if (! empty( $redux_builder_amp['amporwp-structured-data-video-thumb-url']['url'] ) ) {
						$structured_data_video_thumb_url = $redux_builder_amp['amporwp-structured-data-video-thumb-url']['url'];
					}
			}
			// If featured image is present, take it as thumbnail
			else{
				$structured_data_video_thumb_url = $post_image[0];
			}
			$metadata['name'] = $metadata['headline'];
			$metadata['uploadDate'] = $metadata['datePublished'];
			$metadata['thumbnailUrl'] = $structured_data_video_thumb_url;
		}
		return $metadata;
	}
}
