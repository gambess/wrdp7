<?php
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
 
// check for plugin using plugin name
$oldPlugin = AMPFORWP_MAIN_PLUGIN_DIR.'amp-category-base-remover/amp-category-base-remover.php';
if ( is_plugin_active( $oldPlugin ) ) {
    //plugin is activated
	deactivate_plugins($oldPlugin);
	add_action( 'admin_notices', 'plugin_catagory_base_removed_admin_notice__success' );
} 

function plugin_catagory_base_removed_admin_notice__success(){
	?>
	<div class="notice notice-success is-dismissible">
        <p><?php _e( 'AMP Category Base URL Remover plugin has De-activated, <br> Category removal option is added in our core plugin <a href="#">Click here to view details</a>', 'amp-for-plugin' ); ?></p>
    </div>
	<?php
}


 add_action( 'current_screen', 'this_screen_own' );
 function this_screen_own(){
	$current_screen = get_current_screen(); 
	 if( $current_screen ->id == "plugin-install" ) {
		
			 function amp_enqueue_function_dependancies($hook) {
				wp_enqueue_script( 'AMPScriptDependanciesremove', plugins_url('dependencyScript.js', __FILE__), array('jquery') );
			}
			add_action( 'admin_enqueue_scripts', 'amp_enqueue_function_dependancies' );
 
			
			
			
			
	}
 }
 
 
 
add_filter( 'init', 'ampforwp_url_base_rewrite_rules', 100 );
function ampforwp_url_base_rewrite_rules(){
	global $redux_builder_amp;
	global $wp_rewrite;
	$categoryBaseRewrite = 0;
	$tagBaseRewrite = 0;
	
	if(isset($redux_builder_amp['ampforwp-category-base-removel-link'])){	
		$categoryBaseRewrite = $redux_builder_amp['ampforwp-category-base-removel-link'];
	}
	if(isset($redux_builder_amp['ampforwp-tag-base-removal-link'])){
		$tagBaseRewrite = $redux_builder_amp['ampforwp-tag-base-removal-link'];
	}
	/* $catagoryStatusChanges = get_option('AMP-category-base-removal-status');
	if($catagoryStatusChanges==$categoryBaseRewrite){
		update_option('AMP-category-base-removal-status',$categoryBaseRewrite);
		$wp_rerite->flush_rules( $hard );
	} */
	if($categoryBaseRewrite=='1'){
		add_action( 'created_category', 'amp_flush_rewrite_rules', 999 );
		add_action( 'edited_category', 'amp_flush_rewrite_rules', 999 );
		add_action( 'delete_category', 'amp_flush_rewrite_rules', 999 ); 
		add_filter( 'category_rewrite_rules', 'ampforwp_category_url_rewrite_rules');
	}elseif($categoryBaseRewrite=='0'){
		remove_action( 'created_category', 'amp_flush_rewrite_rules' , 999 );
		remove_action( 'edited_category', 'amp_flush_rewrite_rules' , 999 );
		remove_action( 'delete_category', 'amp_flush_rewrite_rules' , 999 );
		remove_filter( 'category_rewrite_rules', 'ampforwp_category_url_rewrite_rules');
		
	}
	 if($tagBaseRewrite=='1'){
		add_action( 'created_post_tag', 'amp_flush_rewrite_rules' , 999 );
		add_action( 'edited_post_tag', 'amp_flush_rewrite_rules', 999 );
		add_action( 'delete_post_tag', 'amp_flush_rewrite_rules', 999 );
		add_filter( 'tag_rewrite_rules', 'ampforwp_tag_url_rewrite_rules' );
	}elseif($tagBaseRewrite=='0'){
		 
		remove_action( 'created_post_tag', 'amp_flush_rewrite_rules' , 999 );
		remove_action( 'edited_post_tag', 'amp_flush_rewrite_rules', 999 );
		remove_action( 'delete_post_tag', 'amp_flush_rewrite_rules', 999 );
		remove_filter( 'tag_rewrite_rules', 'ampforwp_tag_url_rewrite_rules' ); 
	} 
}




function amp_flush_rewrite_rules($hard=true){
	//flush_rewrite_rules();
	global $wp_rewrite;
    $wp_rewrite->flush_rules( $hard );
}

function ampforwp_category_url_rewrite_rules($rewrite){
	global $redux_builder_amp;
	$categoryBaseRewrite = $redux_builder_amp['ampforwp-category-base-removel-link'];
	$categories = get_categories( array( 'hide_empty' => false ) );
	if(is_array( $categories ) && ! empty( $categories ) ) {
		
		
		foreach ( $categories as $category ) {
			$category_nicename = $category->slug;
			if (  $category->parent == $category->cat_ID ) {
				$category->parent = 0;
			} elseif ( 0 != $category->parent ) {
				$category_nicename = get_category_parents(  $category->parent, false, '/', true  ) . $category_nicename;
			}
			$category_nicename = trim($category_nicename);
			
			$rewrite["(".$category_nicename.")".'\/amp/?$'] = 'index.php?amp&category_name='.$category_nicename;
			$rewrite["(".$category_nicename.")".'\/amp\/page\/?([0-9]{1,})\/?$'] = 'index.php?amp&category_name='.$category_nicename.'&paged=$matches[1]';
			
			
			// Redirect support from Old Category Base
			$old_category_base = get_option( 'category_base' ) ? get_option( 'category_base' ) : 'category';
			$old_category_base = trim( $old_category_base, '/' );
			$rewrite[ $old_category_base . '/(.*)$' ] = 'index.php?category_redirect=$matches[1]';


			// Redirect support from Old Category Base
			$old_category_base = get_option( 'category_base' ) ? get_option( 'category_base' ) : 'category';
			$old_category_base = trim( $old_category_base, '/' );
			$rewrite[ $old_category_base . '/(.*)$' ] = 'index.php?category_redirect=$matches[1]';
			
		}
	}
	return $rewrite;
}

 
function ampforwp_tag_url_rewrite_rules($rewrite){
	$terms = get_terms( 'post_tag', array( 'hide_empty' => false ) );
	foreach ( $terms as $term ) {
		$term_nicename = trim($term->slug);
		
		$rewrite["(".$term_nicename.")".'\/amp/?$'] = 'index.php?amp&tag='.$term_nicename;
		$rewrite["(".$term_nicename.")".'\/amp\/page\/?([0-9]{1,})\/?$'] = 
		  'index.php?amp&tag='.$term_nicename.'&paged=$matches[1]'; 
  
	}
	
	return $rewrite;
}