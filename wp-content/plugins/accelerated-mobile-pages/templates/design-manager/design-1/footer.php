<?php
global $redux_builder_amp;
wp_reset_postdata(); ?>
<footer class="amp-wp-footer">
	<div id="footer">
		<?php if ( has_nav_menu( 'amp-footer-menu' ) ) { ?>
          <div class="footer_menu"> 
           <?php // schema.org/SiteNavigationElement missing from menus #1229 ?>
      <nav id ="primary-amp-menu" itemscope="" itemtype="https://schema.org/SiteNavigationElement">
             <?php 
             $menu = wp_nav_menu( array(
                  'theme_location' => 'amp-footer-menu',
                  'link_before'     => '<span itemprop="name">',
                  'link_after'     => '</span>',
                  'echo' => false
              ) );
              echo strip_tags( $menu , '<ul><li><a>'); ?>
          </div>
        </nav>
        <?php } ?>
		<h2><?php echo esc_html( $this->get( 'blog_name' ) ); ?></h2>
		<p class="copyright_txt">
			<?php
			global $allowed_html;
			echo wp_kses( ampforwp_translation($redux_builder_amp['amp-translator-footer-text'], 'Footer' ) , $allowed_html) ;
 		?>
		</p>

		
    <p class="back-to-top">
      <?php if($redux_builder_amp['ampforwp-footer-top']=='1') { ?>
        <a href="#top"> <?php echo ampforwp_translation( $redux_builder_amp['amp-translator-top-text'], 'Top' ); ?>
        </a> <?php }
      if($redux_builder_amp['amp-footer-link-non-amp-page']=='1') { ?> |  <?php ampforwp_view_nonamp(); 
      } ?>
    </p>

	</div>
</footer>
<?php do_action('ampforwp_global_after_footer'); ?>