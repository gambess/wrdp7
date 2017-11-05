<?php do_action('ampforwp_before_meta_info_hook',$this);
	global $redux_builder_amp; ?>
<div class="amp-wp-article-header ampforwp-meta-info <?php if( is_page() && ! $redux_builder_amp['meta_page'] ) {?> hide-meta-info <?php  }?>">
	<div class="amp-wp-content post-title-meta">

			<ul class="amp-wp-meta amp-meta-wrapper">
<?php $post_author = $this->get( 'post_author' ); ?>
<?php if ( $post_author ) : ?>
	<?php $author_avatar_url = get_avatar_url( $post_author->user_email, array( 'size' => 24 ) ); ?>
	<div class="amp-wp-meta amp-wp-byline">
		<?php 
		if(is_single() ) {
			echo ampforwp_get_author_details( $post_author , 'meta-info' ); ?>
		<li class="amp-wp-meta-date"> <?php global $redux_builder_amp; echo ampforwp_translation($redux_builder_amp['amp-translator-on-text'] . ' ', 'On'); the_modified_date( get_option( 'date_format' ) ) ?></li>
<?php } 
	if( is_page() && $redux_builder_amp['meta_page'] ) { 
		echo ampforwp_get_author_details( $post_author , 'meta-info' ); ?>
	<li class="amp-wp-meta-date"> <?php global $redux_builder_amp; echo ampforwp_translation($redux_builder_amp['amp-translator-on-text'] . ' ', 'On'); the_modified_date( get_option( 'date_format' ) ) ?></li> 
<?php } ?>
	</div>
<?php endif; ?>

<?php
if( isset($redux_builder_amp['ampforwp-cats-single']) && $redux_builder_amp['ampforwp-cats-single']) {
  $ampforwp_categories = get_the_terms( $this->ID, 'category' );
  if ( $ampforwp_categories ) : ?>
  	<div class="amp-wp-meta amp-wp-tax-category ampforwp-tax-category">
  		<span>
				<?php global $redux_builder_amp;
				
						 global $redux_builder_amp; printf( ampforwp_translation($redux_builder_amp['amp-translator-categories-text'] .' ', 'accelerated-mobile-pages' ));
							
				?>
			</span>
      <?php foreach ($ampforwp_categories as $cat ) {
      		if( isset($redux_builder_amp['ampforwp-archive-support']) && $redux_builder_amp['ampforwp-archive-support'] && isset($redux_builder_amp['ampforwp-cats-tags-links-single']) && $redux_builder_amp['ampforwp-cats-tags-links-single']) {
            		echo ('<span class="amp-cat-'.$cat->term_id.'"><a href="'. user_trailingslashit( trailingslashit( get_category_link( $cat->term_id ) ) .'amp' ) . '" >'.$cat->name .'</a></span>');//#934
				} else {
					echo ('<span>'.$cat->name .'</span>');
				}
      }

			
			 ?>
  	</div>
  <?php endif; }  ?>

			</ul>
	</div>
</div>
<?php do_action('ampforwp_after_meta_info_hook',$this);
