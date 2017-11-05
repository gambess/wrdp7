<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package themoments
 */

?>

	<!-- Tab to top scrolling -->
	
	<div class="widget-main clearfix">
		<div class="container">
		<div class="instagram-widget"><?php dynamic_sidebar( 'footer-1' ); ?></div>
		<div class="contact-widget"><?php dynamic_sidebar( 'footer-2' ); ?></div>
		</div>
	</div>

	
	<footer>
		<?php
            wp_nav_menu( array(
                'theme_location'    => 'secondary',
                'container'         => 'div',
                'menu_class'        => 'list-inline',
                 'fallback_cb'      => 'wp_bootstrap_navwalker::fallback',
                'walker'            => new Themoments_wp_bootstrap_navwalker())
            );
        ?>

		<div class="copyright">
            <?php esc_html_e( "Powered by", 'themoments' ); ?> <a href="<?php echo esc_url( 'http://wordpress.org/' ); ?>"><?php esc_html_e( "WordPress", 'themoments' ); ?></a> | <?php esc_html_e( 'Theme by', 'themoments' ); ?> <a href="<?php echo esc_url( 'http://thebootstrapthemes.com/' ); ?>"><?php esc_html_e( 'The Bootstrap Themes','themoments' ); ?></a>
        </div>
	</footer>
	     
    <div class="social-icons">
        <ul>
            <?php 
            $facebook =  get_theme_mod ( 'facebook_textbox' );
            $twitter = get_theme_mod( 'twitter_textbox' );
            $googleplus = get_theme_mod( 'googleplus_textbox' );
            $youtube = get_theme_mod( 'youtube_textbox' );
            $linkedin = get_theme_mod( 'linkedin_textbox' );
            $pinterest = get_theme_mod( 'pinterest_textbox' );
            if ( $facebook ) { ?>
              <li><a href="<?php echo esc_url( $facebook ); ?>" target="_blank"><i class="fa fa-facebook"></i></a></li>
            <?php }
            if ( $twitter ) { ?>
              <li><a href="<?php echo esc_url( $twitter ); ?>" target="_blank"><i class="fa fa-twitter"></i></a></li>
            <?php }
            if ( $googleplus ) { ?>
              <li><a href="<?php echo esc_url( $googleplus ); ?>" target="_blank"><i class="fa fa-google-plus"></i></a></li>
            <?php }
            if ( $youtube ) { ?>
              <li><a href="<?php echo esc_url( $youtube ); ?>" target="_blank"><i class="fa fa-youtube-play"></i></a></li>
            <?php }
            if ( $linkedin ) { ?>
              <li><a href="<?php echo esc_url( $linkedin ); ?>" target="_blank"><i class="fa fa-linkedin"></i></a></li>
            <?php }
            if ( $pinterest ) { ?>
              <li><a href="<?php echo esc_url( $pinterest ); ?>" target="_blank"><i class="fa fa-pinterest"></i></a></li>
            <?php } ?>
    	</ul>
	</div>
        

<div class="scroll-top-wrapper"> <span class="scroll-top-inner"><i class="fa fa-2x fa-angle-up"></i></span></div> 
		
		<?php wp_footer(); ?>
	</body>
</html>