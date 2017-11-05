<?php

/*
 * This file is part of the SoccerInfo package.
 *
 * (c) Szilard Mihaly <office@mihalysoft.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
if (!defined('ABSPATH')) die();

if ( ! class_exists('SoccerInfo_Admin')) {
    
    /**
     * Manage the rendering in the back-end.
     *
     * @category   Admin
     * @package    SoccerInfo
     * @author     Szilard Mihaly
     * @copyright  (c) 2015 Mihaly Soft
     */
    class SoccerInfo_Admin {

        /**
         * Constructor
         *
         * @param  none
         * @return void
         */
        public function __construct() {}

        /**
         * Add the admin styles
         *
         * @param  none
         * @return void
         */
        public static function print_admin_styles()
        {
            // Execute this only when we are on a SoccerInfo page
            if (isset($_GET['page']))
            {
                // We quit if the current page isn't one of SoccerInfo
				$_GET['page'] = trim($_GET['page']);
                if ( ! in_array($_GET['page'], SoccerInfo::$pages ))
                    return;

                wp_register_style('soccer-info-backend', plugins_url( SOCCER_INFO_BASEPATH.'/css/soccer-info-admin.css' ));
                wp_enqueue_style('soccer-info-backend');
            }
        }
		
	
		function HtmlPrintBoxHeader($id, $title, $right = false) {
			?>
				<div id="<?php echo $id; ?>" class="postbox">
					<h3 class="hndle"><span><?php echo $title ?></span></h3>
					<div class="inside">
			<?php
		}
		
		function HtmlPrintBoxFooter( $right = false) {
				?>
					</div>
				</div>
				<?php
		}
	
		/**
		 * Returns a link pointing back to the plugin page in WordPress
		 * 
		 * @return string The full url
		 */
		function GetBackLink() {
			global $wp_version;
			$url = '';
			//admin_url was added in WP 2.6.0
			if(function_exists("admin_url")) $url = admin_url("options-general.php?page=" .  SOCCER_INFO_BASEPATH);
			else $url = $_SERVER['PHP_SELF'] . "?page=" .  SOCCER_INFO_BASEPATH;
			
			//Some browser cache the page... great! So lets add some no caching params depending on the WP and plugin version
			$url.='&si_wpv=' . $wp_version . '&si_pv=' . SOCCER_INFO_VERSION;
			
			return $url;
		}

        /**
         * Backend pages handler
         *
         * @param  none
         * @return string
         */
        public function admin_page() {
			global $wp_version, $soccer_info;
				
            // JS must be enabled to use properly SoccerInfo...
            _e('<noscript>Javascript must be enabled, thank you.</noscript>', 'soccer-info');
            
            // Initialize libraries
            //$ctl = new SoccerInfo_Admin;
			
			$wpsiopt = get_option("soccer_info_options");
		
			?>
			
			<div class="wrap" id="si_div">
				<h2><?php printf(__('Soccer Info %s for WordPress', 'soccer-info'), SOCCER_INFO_VERSION); ?> </h2>
				
				<?php
				if ( isset($_POST['si_update']) && !empty($_POST['si_update']) ) { //Pressed Button: Update Config
					check_admin_referer('soccer_info');
					
					if (isset($_POST['si_timezone']) && is_numeric($_POST['si_timezone']) && ( $_POST['si_timezone'] == 0 || !empty($_POST['si_timezone']) ))
						$wpsiopt['si_timezone'] = sanitize_option('gmt_offset', $_POST['si_timezone']);
					
					if (isset($_POST['si_date_format']) && !empty($_POST['si_date_format']))
						$wpsiopt['si_date_format'] = sanitize_option('date_format', $_POST['si_date_format']);
					
					if (isset($_POST['si_time_format']) && !empty($_POST['si_time_format']))
						$wpsiopt['si_time_format'] = sanitize_option('time_format', $_POST['si_time_format']);
					
					if (isset($_POST['si_date_format_custom']) && !empty($_POST['si_date_format_custom']))
						$wpsiopt['si_date_format_custom'] = sanitize_option('date_format', $_POST['si_date_format_custom']);
					
					if (isset($_POST['si_donated']) && !empty($_POST['si_donated']))
						$wpsiopt['si_donated'] = SOCCER_INFO_VERSION; //$_POST['si_donated'];
					else
						$wpsiopt['si_donated'] = false;
					
					if (isset($_POST['si_pro']))
						$_POST_si_pro = trim($_POST['si_pro']);
					if (isset($_POST_si_pro) && preg_match("/[\dl]/", $_POST_si_pro))
						$wpsiopt['si_pro'] = sanitize_text_field($_POST_si_pro);
					else
						$wpsiopt['si_pro'] = '';
					
					update_option("soccer_info_options", $wpsiopt);
					
					?>
					<div class="updated">
						<p><?php _e('Settings Updated', 'soccer-info');?></p>
					</div>
					<?php
					
				}
				
				if (isset($_POST['si_reset_config'])) {
					check_admin_referer('soccer_info');
					
					delete_option("soccer_info_options");
					
					$soccer_info->wpsiopt = SoccerInfo::$wpsiopt_default;
					$soccer_info->LoadOptions();
					
					$wpsiopt = get_option("soccer_info_options");
					
					?>
					<div class="updated">
						<p><?php _e('Settings Updated to the default values', 'soccer-info');?></p>
					</div>
					<?php
				}
				
				?>
					
				<div id="poststuff" class="metabox-holder has-right-sidebar">
					<div class="inner-sidebar">
						<div id="side-sortables" class="meta-box-sortabless ui-sortable" style="position:relative;">
							
							<?php $this->HtmlPrintBoxHeader('si_pnres',__('About this Plugin:', 'soccer-info'),true); ?>
								<?php _e('Soccer Info lets you display ranking tables, fixtures and results of major soccer leagues without any hassles.', 'soccer-info'); ?>
								<?php
									$translator_name = __('translator_name', 'soccer-info');
									if ( $translator_name != 'translator_name'  ) {
										echo '<br />'.__('Translated by:', 'soccer-info').'<br />';
										$translator_url = __('translator_url', 'soccer-info');
										if ( $translator_url != 'translator_url' )
											echo '<a class="si_button si_pluginSupport" href="'.$translator_url.'">';
										
										echo $translator_name;
										
										if ( $translator_url != 'translator_url' )
											echo '</a>';
									}
								?>
								<br /><br />
								<?php
								if (function_exists('get_transient')) {
								  require_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );
								
								  // Before, try to access the data, check the cache.
								  if (false === ($api = get_transient('soccerinfo_plugin_info'))) {
									// The cache data doesn't exist or it's expired.
								
									$api = plugins_api('plugin_information', array('slug' => 'soccer-info' ));
									if ( !is_wp_error($api) ) {
									  // cache isn't up to date, write this fresh information to it now to avoid the query for xx time.
									  $myexpire = 60 * 15; // Cache data for 15 minutes
									  set_transient('soccerinfo_plugin_info', $api, $myexpire);
									}
								  }
								  if ( !is_wp_error($api) ) {
									$plugins_allowedtags = array('a' => array('href' => array(), 'title' => array(), 'target' => array()),
																'abbr' => array('title' => array()), 'acronym' => array('title' => array()),
																'code' => array(), 'pre' => array(), 'em' => array(), 'strong' => array(),
																'div' => array(), 'p' => array(), 'ul' => array(), 'ol' => array(), 'li' => array(),
																'h1' => array(), 'h2' => array(), 'h3' => array(), 'h4' => array(), 'h5' => array(), 'h6' => array(),
																'img' => array('src' => array(), 'class' => array(), 'alt' => array()));
									//Sanitize HTML
									foreach ( (array)$api->sections as $section_name => $content )
										$api->sections[$section_name] = wp_kses($content, $plugins_allowedtags);
									foreach ( array('version', 'author', 'requires', 'tested', 'homepage', 'downloaded', 'slug') as $key )
										$api->$key = wp_kses($api->$key, $plugins_allowedtags);
								
									  if ( ! empty($api->downloaded) ) {
										echo sprintf(__('Downloaded %s times', 'soccer-info'),number_format_i18n($api->downloaded));
										echo '.';
									  }
								?>
										<?php if ( ! empty($api->rating) ) : ?>
										<div class="si-star-holder" title="<?php echo esc_attr(sprintf(__('(Average rating based on %s ratings)', 'soccer-info'),number_format_i18n($api->num_ratings))); ?>">
											<div class="si-star si-star-rating" style="width: <?php echo esc_attr($api->rating) ?>px"></div>
											<div class="si-star si-star5"><img src="<?php echo WP_PLUGIN_URL; ?>/soccer-info/img/star.png" alt="<?php _e('5 stars', 'soccer-info') ?>" /></div>
											<div class="si-star si-star4"><img src="<?php echo WP_PLUGIN_URL; ?>/soccer-info/img/star.png" alt="<?php _e('4 stars', 'soccer-info') ?>" /></div>
											<div class="si-star si-star3"><img src="<?php echo WP_PLUGIN_URL; ?>/soccer-info/img/star.png" alt="<?php _e('3 stars', 'soccer-info') ?>" /></div>
											<div class="si-star si-star2"><img src="<?php echo WP_PLUGIN_URL; ?>/soccer-info/img/star.png" alt="<?php _e('2 stars', 'soccer-info') ?>" /></div>
											<div class="si-star si-star1"><img src="<?php echo WP_PLUGIN_URL; ?>/soccer-info/img/star.png" alt="<?php _e('1 star', 'soccer-info') ?>" /></div>
										</div>
										<small><?php echo sprintf(__('(Average rating based on %s ratings)', 'soccer-info'),number_format_i18n($api->num_ratings)); ?> <a target="_blank" href="http://wordpress.org/support/view/plugin-reviews/soccer-info"> <?php _e('rate', 'soccer-info') ?></a></small>
										<br />
										<?php endif; ?>
								
								<?php
								  } // if ( !is_wp_error($api)
								 }// end if (function_exists('get_transient'
								
								$si_update = '';
								if (isset($api->version)) {
								 if ( version_compare($api->version, SOCCER_INFO_VERSION, '>') ) {
									 $si_update = ', <a href="'.admin_url( 'plugins.php' ).'">'.sprintf(__('a newer version is available: %s', 'soccer-info'),$api->version).'</a>';
									 echo '<div id="message" class="updated">';
									 echo '<a href="'.admin_url( 'plugins.php' ).'">'.sprintf(__('A newer version of Soccer Info is available: %s', 'soccer-info'),$api->version).'</a>';
									 echo "</div>\n";
								  }else{
									 $si_update = ' '. __('(latest version)', 'soccer-info');
								  }
								}
								?>
								
								<p>
								<?php echo __('Version:', 'soccer-info'). ' '.SOCCER_INFO_VERSION.$si_update; ?> <br />
								<a href="http://wordpress.org/extend/plugins/soccer-info/changelog/" target="_blank"><?php echo __('Changelog', 'soccer-info'); ?></a> |
								<a href="http://wordpress.org/extend/plugins/soccer-info/faq/" target="_blank"><?php echo __('FAQ', 'soccer-info'); ?></a> |
								<a href="http://wordpress.org/support/view/plugin-reviews/soccer-info" target="_blank"><?php echo __('Rate This', 'soccer-info'); ?></a> |
								<a href="http://wordpress.org/support/plugin/soccer-info" target="_blank"><?php echo __('Support', 'soccer-info'); ?></a>
								<!-- | <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=4V94PVSJNFZMA" target="_blank"><?php echo __('Donate', 'soccer-info'); ?></a>-->
								</p>
									
							<?php $this->HtmlPrintBoxFooter(true); ?>
							
							<?php /** $this->HtmlPrintBoxHeader('si_contribute',__('Contribute:', 'soccer-info'),true); ?>
								<?php _e('Please donate to keep this plugin FREE. If you find this plugin useful, please consider making a small donation to help contribute to my time invested and to further development. Thanks for your kind support!', 'soccer-info'); ?>
								
								<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank" style="clear:both; text-align:center;">
								<input type="hidden" name="cmd" value="_s-xclick" />
								<input type="hidden" name="hosted_button_id" value="4V94PVSJNFZMA" />
								<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!" />
								<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1" />
								</form>
									
							<?php $this->HtmlPrintBoxFooter(true); /**/ ?>
							
							
							<?php $this->HtmlPrintBoxHeader('si_faq',__('Frequently Asked Questions:', 'soccer-info'),true); ?>
								<?php _e("
<h4>I've just activated Soccer Info, what do I need to do now?</h4>
<p>
You are now able to add the shortcodes that displays the tables (fixtures or results) to any of your posts or pages.<br /> 
For example: <strong>[soccer-info id='1' type='table' /]</strong><br />
That means you want to display the raking <em>`table`</em> (type) of the soccer league with the <em>id=1</em> (Spanish Primera Division)<br />
You will find the whole list of the soccer leagues supported by the plugin on the <strong>`Settings` > `Soccer Info`</strong> page.
</p>
<h4>I don't know how to use shortcodes, what should I do then?</h4>
<p>
For your confort, we added a function to the post/page editor that automatically generates and inserts the shortcodes for you. You will be prompted to select the league (and some other info) and then just click `Insert`<br />
It sounds easy. Isn't is?
</p>
<h4>Does this plugin have a widget?</h4>
<p>
Yes, and it's easy to use.
</p>
<h4>I have a another question. Where can I ask that?</h4>
<p>
For more information, check out the plugin's website: <a href='http://www.mihalysoft.com/wordpress-plugins/soccer-info/' target='_blank'>Soccer Info</a>
</p>", 'soccer-info'); ?>
							<?php $this->HtmlPrintBoxFooter(true); ?>
							
							<?php $this->HtmlPrintBoxHeader('si_league_list',__('Supported Leagues:', 'soccer-info'),true); ?>
								<?php
									$i_l = 0;
									foreach($soccer_info->competitions as $league => $ii) {
										if ( $i_l > 0 ) {
											$e_h_l = esc_html($league);
											if ( strlen($e_h_l) > 30 )
												$e_h_l = substr($e_h_l, 0, 30).'...';
											echo $e_h_l.' <span class="alignright">ID = '.$i_l.'</span><br />'."\n";
										}
										$i_l++;
									}
								?>
							<?php $this->HtmlPrintBoxFooter(true); ?>
							
							
						</div>
					</div>
					
					<div class="has-sidebar si-padded" >
					<form method="post" action="<?php echo $this->GetBackLink() ?>">
					
						<div id="post-body-content" class="has-sidebar-content">
						
								<div class="meta-box-sortabless">
							
						<!-- Basic Options -->
						<?php $this->HtmlPrintBoxHeader('si_options',__('Options', 'soccer-info')); ?>
	
							<!-- <p><?php _e('Description...', 'soccer-info') ?></p> -->
							
							<table class="form-table" style="clear:none;">
							<tbody>
								<tr valign="top">
									<th scope="row">
										<label for="si_timezone">
											<?php _e('Timezone', 'soccer-info') ?>
										</label>
									</th>
									<td>
										<select id="si_timezone" name="si_timezone">
											<option value="-12"<?php if ($wpsiopt['si_timezone'] == -12) echo ' selected="selected"' ?>>UTC -12</option>
											<?php
											for ($i = -11; $i < 14; $i ++) {
												if ($i.'.5' == $wpsiopt['si_timezone'])
													$selected_5 = ' selected="selected"';
												else
													$selected_5 = '';
												if($i < 0)echo '<option value="'.$i.'.5"'.$selected_5.'>UTC '.$i.':30</option>';
												
												if($i == 0) {
													if ('-0.5' == $wpsiopt['si_timezone'])
														$selected_0_5 = ' selected="selected"';
													else
														$selected_0_5 = '';
													echo '<option value="-0.5"'.$selected_0_5.'>UTC -0:30</option>';
												}
												
												if ($i == $wpsiopt['si_timezone'])
													$selected = ' selected="selected"';
												else
													$selected = '';
												echo '<option value="'.$i.'"'.$selected.'>UTC '.(($i>=0)?'+':'').$i.'</option>';
												
												if($i >= 0)echo '<option value="'.$i.'.5"'.$selected_5.'>UTC +'.$i.':30</option>';
												
												if(in_array($i, array(5, 8, 12, 13))) {
													if ($i.'.75' == $wpsiopt['si_timezone'])
														$selected_75 = ' selected="selected"';
													else
														$selected_75 = '';
													echo '<option value="'.$i.'.75"'.$selected_75.'>UTC +'.$i.':45</option>';
												}
											}
											?>
											<option value="14"<?php if ($wpsiopt['si_timezone'] == 14) echo ' selected="selected"' ?>>UTC +14</option>
										</select>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row">
										<label for="si_date_format">
											<?php _e('Date Format', 'soccer-info') ?>
										</label>
									</th>
									<td>
										<fieldset>
											<legend class="screen-reader-text"><span><?php _e('Date Format', 'soccer-info') ?></span></legend>
											<?php
											$o_dates = array( __('l, F j, Y', 'soccer-info'), 
															  __('F j, Y', 'soccer-info'), 
															  __('Y/m/d', 'soccer-info'), 
															  __('m/d/Y', 'soccer-info'), 
															  __('d/m/Y', 'soccer-info') );
											foreach ($o_dates as $o_d) {
												if ($o_d == $wpsiopt['si_date_format']) {
													$checked = ' checked="checked"';
												}
												else
													$checked = '';
												echo '<label title="'.esc_attr($o_d).'"><input type="radio" name="si_date_format" value="'.esc_attr($o_d).'"'.$checked.' /> <span>'.date_i18n($o_d).'</span></label><br />';
											}
											?>
											<label><input type="radio" name="si_date_format" id="si_date_format_custom_radio" value="custom"<?php if ($wpsiopt['si_date_format'] == 'custom') echo ' checked="checked"'; ?> /> <?php _e('Custom:', 'soccer-info');?> </label><input type="text" name="si_date_format_custom" value="<?php echo esc_attr($wpsiopt['si_date_format_custom']);?>" class="small-text" /> <span class="example"><?php echo date_i18n($wpsiopt['si_date_format_custom']);?></span>  <img class='ajax-loading' src='<?php echo admin_url();?>images/wpspin_light.gif' />
											<p><a href="http://codex.wordpress.org/Formatting_Date_and_Time" target="_blank"><?php _e('Documentation on date and time formatting.', 'soccer-info'); ?></a></p>
										</fieldset>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row">
										<label for="si_time_format">
											<?php _e('Time Format', 'soccer-info') ?>
										</label>
									</th>
									<td>
										<fieldset>
											<legend class="screen-reader-text"><span><?php _e('Time Format', 'soccer-info'); ?></span></legend>
											<?php
											$o_times = array( __('g:i a', 'soccer-info'), 
															  __('g:i A', 'soccer-info'), 
															  __('H:i', 'soccer-info') );
											foreach ($o_times as $o_t) {
												if ($o_t == $wpsiopt['si_time_format'])
													$checked = ' checked="checked"';
												else
													$checked = '';
												echo '<label title="'.esc_attr($o_t).'"><input type="radio" name="si_time_format" value="'.esc_attr($o_t).'"'.$checked.' /> <span>'.date_i18n($o_t).'</span></label><br />';
											}
											?>
										</fieldset>
									</td>
								</tr>
                                <?php /**
								<tr valign="top">
									<th scope="row">
										<label for="si_pro">
											<?php _e('PRO Code (links removal)', 'soccer-info'); ?>
										</label>
									</th>
									<td>
										<fieldset>
											<legend class="screen-reader-text"><span><?php _e('Contribution', 'soccer-info'); ?></span></legend>
											<?php
												echo '<label title="'.__('PRO code', 'soccer-info').'"><input id="si_pro" type="text" name="si_pro" value="'.$wpsiopt['si_pro'].'" />  &nbsp; <span>'.__('Enter the PRO Code to remove the links of the teams.', 'soccer-info').'</span></label>
												<p class="description">'.__('If you do not have a PRO Code yet, please follow the <a href="http://www.mihalysoft.com/wordpress-plugins/soccer-info/soccer-info-pro-code/" target="_blank"><strong>instructions here</strong></a>.', 'soccer-info').'</p>';
											?>
											<input type="hidden" name="si_donated" value="<?php echo SOCCER_INFO_VERSION;?>" />
										</fieldset>
									</td>
								</tr>
								/**/ ?>
							</tbody>
							</table>
							
						<?php $this->HtmlPrintBoxFooter(); ?>
						
						</div> <!-- meta-box-sortabless -->
						</div> <!-- has-sidebar-content -->
						
						<p class="submit">
								<?php wp_nonce_field('soccer_info') ?>
								<input type="submit" name="si_update" value="<?php _e('Update options', 'soccer-info'); ?>" class="button-primary" />
								<input type="submit" onclick='return confirm("<?php _e('Do you really want to reset your configuration?', 'soccer-info'); ?>");' class="si_warning" name="si_reset_config" value="<?php _e('Reset options', 'soccer-info'); ?>" />
						</p>
					</form>
					</div> <!-- has-sidebar si-padded -->
					
				</div> <!-- metabox-holder has-right-sidebar -->
				
			</div> <!-- wrap -->
			<?php
            
            // Page Footer
           // echo $this->admin_footer();
        }

        /**
         * Add's new global menu, if $href is false menu is added
         * but registred as submenuable
         *
         * @return void
         */
        protected static function add_root_menu($name, $id, $href = FALSE)
        {
            global $wp_admin_bar;
            if ( ! is_super_admin() || ! is_admin_bar_showing())
              return;

            $wp_admin_bar->add_menu(array(
                'id'    => $id,
                'title' => $name,
                'href'  => $href
            ));
        }

        /**
         * Add's new submenu where additinal $meta specifies class,
         * id, target or onclick parameters
         *
         * @return void
         */
        protected static function add_sub_menu($name, $link, $parent, $id, $meta = FALSE)
        {
            global $wp_admin_bar;
            if ( ! is_super_admin() || ! is_admin_bar_showing())
                return;
            
            $wp_admin_bar->add_menu(array(
                'parent' => $parent,
                'title'  => $name,
                'href'   => $link,
                'meta'   => $meta,
                'id'     => $id
            ));
        }
        
        /**
         * Add the admin scripts
         *
         * @param  none
         * @return void
         */
        public static function print_admin_scripts()
        {
            // Execute this only when we are on a SoccerInfo page
            if (isset($_GET['page']))
            {
                // We quit if the current page isn't one of SoccerInfo
				$_GET['page'] = trim($_GET['page']);
                if ( ! in_array($_GET['page'], SoccerInfo::$pages ))
                    return;
                
                // Make sure to use the latest version of jQuery...
                //wp_deregister_script('jquery');
                //wp_register_script('jquery', ('http://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js'), FALSE, NULL, TRUE);
                //wp_enqueue_script('jquery');

                wp_register_script('soccer-info', plugins_url( SOCCER_INFO_BASEPATH.'/js/admin.js'), array('jquery') );
                wp_enqueue_script('soccer-info');
                //wp_register_script('soccer-info-mask', plugins_url( SOCCER_INFO_BASEPATH.'/js/jquery.maskedinput.js'), array('jquery') );
                //wp_enqueue_script('soccer-info-mask');
            }
        }
        public static function print_admin_scripts_widgets() {
			
			wp_register_script('soccer-info-widgets', plugins_url( SOCCER_INFO_BASEPATH.'/js/admin-widgets.js'), array('jquery') );
			wp_enqueue_script('soccer-info-widgets');
			
		}
        
        /**
         * Admin menu generation
         *
         * @param  none
         * @return void
         */
        public static function admin_menu() {
			
            $instance = new SoccerInfo_Admin;
            $parent   = 'soccer_info_overview';
			
			
		
			if (function_exists('add_options_page')) {
				add_options_page(__('Soccer Info', 'soccer-info'), __('Soccer Info', 'soccer-info'), 'manage_options', SOCCER_INFO_BASEPATH, array($instance,'admin_page'));
			}
        }

        /**
         * Add TinyMCE Button
         *
         * @param  none
         * @return void
         */
        public static function add_editor_button() //static
        {
            // Don't bother doing this stuff if the current user lacks permissions
            if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages')) return;

            // Check for SoccerInfo capability
            //if ( ! current_user_can('soccer_info')) return;

            // Add only in Rich Editor mode
            if (get_user_option('rich_editing') == 'true')
            {
                add_filter('mce_external_plugins', array('SoccerInfo_Admin', 'add_editor_plugin'));
                add_filter('mce_buttons', array('SoccerInfo_Admin', 'register_editor_button'));
            }

			add_action('wp_print_scripts', array('SoccerInfo_Admin', 'si_myscript')); //added from 1.9.5 version
        }
		
		 //added from 1.9.5 version
		public static function si_myscript() {
		?>
		<script type="text/javascript">
			var si_site_url = "<?php echo add_query_arg('si_plugin_trigger', '1', get_option('siteurl')); ?>";
		</script>
		<?php
		}
        
        /**
         * Add TinyMCE plugin
         *
         * @param  array $plugin_array
         * @return array
         */
        public static function add_editor_plugin($plugin_array)
        {
            $plugin_array['SoccerInfo'] = plugins_url( SOCCER_INFO_BASEPATH.'/js/tinymce/editor_plugin.js');
            return $plugin_array;
        }
        
        /**
         * Register TinyMCE button
         *
         * @param  array $buttons
         * @return array
         */
        public static function register_editor_button($buttons)
        {
            array_push($buttons, 'separator', 'SoccerInfo');
            return $buttons;
        }
    }
}