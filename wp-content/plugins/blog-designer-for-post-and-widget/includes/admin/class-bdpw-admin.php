<?php
/**
 * Admin Class
 *
 * Handles the Admin side functionality of plugin
 *
 * @package Blog Designer - Post and Widget
 * @since 1.0
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

class Bdpw_Admin {

	function __construct() {

		add_filter('manage_edit-category_columns', array($this, 'bdpw_manage_category_columns'));

		// Filter to add extra column to post category
		add_filter('manage_category_custom_column', array($this, 'bdpw_cat_columns_data'), 10, 3);

		// Action to register admin menu
		add_action( 'admin_menu', array($this, 'bdpw_register_menu'), 9 );
	}

	/**
	 * Admin Class
	 *
	 * Add extra column to post category
	 *
	 * @package Blog Designer - Post and Widget
	 * @since 1.0
	*/
	function bdpw_manage_category_columns($columns) {

	    $new_columns['wpos_shortcode'] = __( 'Category ID', 'blog-designer-for-post-and-widget' );
	    
	    $columns = bdpw_add_array( $columns, $new_columns, 2 );
	    
	    return $columns;
	}

	/**
	 * 
	 * Add data to extra column to post category
	 * 
	 * @package Blog Designer - Post and Widget
	 * @since 1.0
	*/
	function bdpw_cat_columns_data($ouput, $column_name, $tax_id) {
	    
	    switch ($column_name) {
	        case 'wpos_shortcode':
	            echo $tax_id;	          
	            break;
	    }
	    return $ouput;
	}

	/**
	 * Function to register admin menus
	 * 
	 * @package Blog Designer - Post and Widget
	 * @since 1.0.4
	 */
	function bdpw_register_menu() {
		add_submenu_page( 'edit.php', __('Blog Designer', 'blog-designer-for-post-and-widget'), __('Blog Designer', 'blog-designer-for-post-and-widget'), 'manage_options', 'wpspw-pro-settings', array($this, 'bdpw_settings_page') );
	}

	
	/**
	 * Function to display plugin design HTML
	 * 
	 * @package Blog Designer - Post and Widget
	 * @since 1.0.0
	 */
	function bdpw_settings_page() {

		$wpos_feed_tabs = $this->bdpw_help_tabs();
		$active_tab 	= isset($_GET['tab']) ? $_GET['tab'] : 'how-it-work';
	?>
			
		<div class="wrap bdpw-wrap">

			<h2 class="nav-tab-wrapper">
				<?php
				foreach ($wpos_feed_tabs as $tab_key => $tab_val) {
					$tab_name	= $tab_val['name'];
					$active_cls = ($tab_key == $active_tab) ? 'nav-tab-active' : '';
					$tab_link 	= add_query_arg( array('page' => 'wpspw-pro-settings', 'tab' => $tab_key), admin_url('edit.php') );
				?>

				<a class="nav-tab <?php echo $active_cls; ?>" href="<?php echo $tab_link; ?>"><?php echo $tab_name; ?></a>

				<?php } ?>
			</h2>
			
			<div class="bdpw-tab-cnt-wrp">
			<?php
				if( isset($active_tab) && $active_tab == 'how-it-work' ) {
					$this->bdpw_howitwork_page();
				}
				else if( isset($active_tab) && $active_tab == 'plugins-feed' ) {
					echo  $this->bdpw_get_plugin_design( 'plugins-feed' );
				} else {
					echo  $this->bdpw_get_plugin_design( 'offers-feed' );
				}
			?>
			</div><!-- end .bdpw-tab-cnt-wrp -->

		</div><!-- end .bdpw-wrap -->

	<?php
	}

	/**
	 * Gets the plugin design part feed
	 *
	 * @package Blog Designer - Post and Widget
	 * @since 1.0.0
	 */
	function bdpw_get_plugin_design( $feed_type = '' ) {
		
		$active_tab = isset($_GET['tab']) ? $_GET['tab'] : '';
		
		// If tab is not set then return
		if( empty($active_tab) ) {
			return false;
		}

		// Taking some variables
		$wpos_feed_tabs =  $this->bdpw_help_tabs();
		$transient_key 	= isset($wpos_feed_tabs[$active_tab]['transient_key']) 	? $wpos_feed_tabs[$active_tab]['transient_key'] 	: 'bdpw_' . $active_tab;
		$url 			= isset($wpos_feed_tabs[$active_tab]['url']) 			? $wpos_feed_tabs[$active_tab]['url'] 				: '';
		$transient_time = isset($wpos_feed_tabs[$active_tab]['transient_time']) ? $wpos_feed_tabs[$active_tab]['transient_time'] 	: 172800;
		$cache 			= get_transient( $transient_key );
		
		if ( false === $cache ) {
			
			$feed 			= wp_remote_get( esc_url_raw( $url ), array( 'timeout' => 120, 'sslverify' => false ) );
			$response_code 	= wp_remote_retrieve_response_code( $feed );
			
			if ( ! is_wp_error( $feed ) && $response_code == 200 ) {
				if ( isset( $feed['body'] ) && strlen( $feed['body'] ) > 0 ) {
					$cache = wp_remote_retrieve_body( $feed );
					set_transient( $transient_key, $cache, $transient_time );
				}
			} else {
				$cache = '<div class="error"><p>' . __( 'There was an error retrieving the data from the server. Please try again later.', 'blog-designer-for-post-and-widget' ) . '</div>';
			}
		}
		return $cache;	
	}

	/**
	 * Function to get plugin feed tabs
	 *
	 *@package Blog Designer - Post and Widget
	 * @since 1.0.0
	 */
	function bdpw_help_tabs() {
		$wpos_feed_tabs = array(
							'how-it-work' 	=> array(
														'name' => __('How It Works', 'blog-designer-for-post-and-widget'),
													),
							'plugins-feed' 	=> array(
														'name' 				=> __('Our Plugins', 'blog-designer-for-post-and-widget'),
														'url'				=> 'http://wponlinesupport.com/plugin-data-api/plugins-data.php',
														'transient_key'		=> 'wpos_plugins_feed',
														'transient_time'	=> 172800
													),
							'offers-feed' 	=> array(
														'name'				=> __('WPOS Offers', 'blog-designer-for-post-and-widget'),
														'url'				=> 'http://wponlinesupport.com/plugin-data-api/wpos-offers.php',
														'transient_key'		=> 'wpos_offers_feed',
														'transient_time'	=> 86400,
													)
						);
		return $wpos_feed_tabs;
	}

	/**
	 * Function to get 'How It Works' HTML
	 *
	 * @package Blog Designer - Post and Widget
	 * @package Blog Designer - Post and Widget
	 * @package Blog Designer - Post and Widget
	 * @since 1.0.0
	 */
	function bdpw_howitwork_page() { ?>
		
		<style type="text/css">
			.wpos-pro-box .hndle{background-color:#0073AA; color:#fff;}
			.wpos-pro-box .postbox{background:#dbf0fa none repeat scroll 0 0; border:1px solid #0073aa; color:#191e23;}
			.postbox-container .wpos-list li:before{font-family: dashicons; content: "\f139"; font-size:20px; color: #0073aa; vertical-align: middle;}
			.bdpw-wrap .wpos-button-full{display:block; text-align:center; box-shadow:none; border-radius:0;}
			.bdpw-shortcode-preview{background-color: #e7e7e7; font-weight: bold; padding: 2px 5px; display: inline-block; margin:0 0 2px 0;}
		</style>

		<div class="post-box-container">
			<div id="poststuff">
				<div id="post-body" class="metabox-holder columns-2">
				
					<!--How it workd HTML -->
					<div id="post-body-content">
						<div class="metabox-holder">
							<div class="meta-box-sortables ui-sortable">
								<div class="postbox">
									
									<h3 class="hndle">
										<span><?php _e( 'How It Works - Display and shortcode', 'blog-designer-for-post-and-widget' ); ?></span>
									</h3>
									
									<div class="inside">
										<table class="form-table">
											<tbody>
												<tr>
													<th>
														<label><?php _e('Geeting Started with  Blog Designer', 'blog-designer-for-post-and-widget'); ?>:</label>
													</th>
													<td>
														<ul>
															<li><?php _e('Step-1. Go to "Post --> Add New".', 'blog-designer-for-post-and-widget'); ?></li>
															<li><?php _e('Step-2. Add post title, description and images', 'blog-designer-for-post-and-widget'); ?></li>
															<li><?php _e('Step-3. Select Category and Tgas', 'blog-designer-for-post-and-widget'); ?></li>
															
														</ul>
													</td>
												</tr>

												<tr>
													<th>
														<label><?php _e('How Shortcode Works', 'blog-designer-for-post-and-widget'); ?>:</label>
													</th>
													<td>
														<ul>
															<li><?php _e('Step-1. Create a page like Blog', 'blog-designer-for-post-and-widget'); ?></li>
															<li><?php _e('Step-2. Put below shortcode as per your need.', 'blog-designer-for-post-and-widget'); ?></li>
														</ul>
													</td>
												</tr>

												<tr>
													<th>
														<label><?php _e('All Shortcodes', 'blog-designer-for-post-and-widget'); ?>:</label>
													</th>
													<td>
														<span class="bdpw-shortcode-preview">[wpspw_post]</span> – <?php _e('Blog Grid Shortcode', 'blog-designer-for-post-and-widget'); ?> <br />
														<span class="bdpw-shortcode-preview">[wpspw_recent_post_slider]</span> – <?php _e('Recent Post Slider Shortcode', 'blog-designer-for-post-and-widget'); ?> <br />
														
													</td>
												</tr>						
													
												<tr>
													<th>
														<label><?php _e('Need Support?', 'blog-designer-for-post-and-widget'); ?></label>
													</th>
													<td>
														<p><?php _e('Check plugin document for shortcode parameters and demo for designs.', 'blog-designer-for-post-and-widget'); ?></p> <br/>
														<a class="button button-primary" href="https://www.wponlinesupport.com/plugins-documentation/documentblog-designer-post-and-widget/?utm_source=hp&event=doc" target="_blank"><?php _e('Documentation', 'blog-designer-for-post-and-widget'); ?></a>									
														<a class="button button-primary" href="http://demo.wponlinesupport.com/blog-designer-post-and-widget/?utm_source=hp&event=demo" target="_blank"><?php _e('Demo for Designs', 'blog-designer-for-post-and-widget'); ?></a>
													</td>
												</tr>
											</tbody>
										</table>
									</div><!-- .inside -->
								</div><!-- #general -->
							</div><!-- .meta-box-sortables ui-sortable -->
						</div><!-- .metabox-holder -->
					</div><!-- #post-body-content -->
					
					<!--Upgrad to Pro HTML -->
					<div id="postbox-container-1" class="postbox-container">
						<div class="metabox-holder wpos-pro-box">
							<div class="meta-box-sortables ui-sortable">
								<div class="postbox" style="">
										
									<h3 class="hndle">
										<span><?php _e( 'Upgrate to Pro', 'blog-designer-for-post-and-widget' ); ?></span>
									</h3>
									<div class="inside">										
										<ul class="wpos-list">
											<li>130+ stunning and cool layouts.</li>
											<li>8 Shortcodes</li>
											<li>50 Designs for Blog Post Grid</li>
											<li>45 Designs for Blog Post Slider/Carousel</li>
											<li>24 Designs for Blog Post Masonry Layout</li>
											<li>8 Designs for Blog Post List View</li>
											<li>13 Designs for Blog Post Grid Box</li>
											<li>8 Designs for Blog Post Grid Box Slider</li>
											<li>5 types of Widgets (Grid, slider and list etc)</li>
											<li>Visual Composer Page Builder Support</li>
											<li>Custom Read More link for Blog Post.</li>
											<li>Blog display with categories.</li>
											<li>Drag & Drop feature to display Blog post in your desired order and other 6 types of order parameter.</li>
											<li>Two type Pagination with Next – Previous or Numeric type support with grid layout.</li>
											<li>Slider RTL support.</li>
											<li>100% Multilanguage.</li>
										</ul>
										<a class="button button-primary wpos-button-full" href="https://www.wponlinesupport.com/wp-plugin/blog-designer-post-and-widget/?utm_source=hp&event=go_premium" target="_blank"><?php _e('Go Premium ', 'blog-designer-for-post-and-widget'); ?></a>	
										<p><a class="button button-primary wpos-button-full" href="http://demo.wponlinesupport.com/prodemo/blog-designer-post-and-widget/?utm_source=hp&event=pro_demo" target="_blank"><?php _e('View PRO Demo ', 'blog-designer-for-post-and-widget'); ?></a>			</p>								
									</div><!-- .inside -->
								</div><!-- #general -->
							</div><!-- .meta-box-sortables ui-sortable -->
						</div><!-- .metabox-holder -->

						<!-- Help to improve this plugin! -->
						<div class="metabox-holder">
							<div class="meta-box-sortables ui-sortable">
								<div class="postbox">
										<h3 class="hndle">
											<span><?php _e( 'Help to improve this plugin!', 'blog-designer-for-post-and-widget' ); ?></span>
										</h3>									
										<div class="inside">										
											<p>Enjoyed this plugin? You can help by rate this plugin <a href="https://wordpress.org/support/plugin/blog-designer-for-post-and-widget/reviews/?filter=5" target="_blank">5 stars!</a></p>
										</div><!-- .inside -->
								</div><!-- #general -->
							</div><!-- .meta-box-sortables ui-sortable -->
						</div><!-- .metabox-holder -->
					</div><!-- #post-container-1 -->

				</div><!-- #post-body -->
			</div><!-- #poststuff -->
		</div><!-- #post-box-container -->
	<?php }

}

$bdpw_admin = new Bdpw_Admin();