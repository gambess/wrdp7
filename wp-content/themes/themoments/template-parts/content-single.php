<?php
/**
 * Template part for displaying single posts.
 *
 * @package themoments
 */

?>

<div class="page-title">
  <h1><?php the_title(); ?></h1>
</div>

<div class="single-post">
  <div class="info">
    <ul class="list-inline">
    <?php $archive_year  = get_the_time('Y'); $archive_month = get_the_time('m'); $archive_day = get_the_time('d'); ?>
      <li><i class="fa fa-user"></i><a class="url fn n" href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>"><?php echo esc_html( get_the_author() ); ?></a></li>
      <li><i class="fa fa-calendar"></i> <a href="<?php echo esc_url( get_day_link( $archive_year, $archive_month, $archive_day ) ); ?>"><?php echo get_the_date(); ?></a></li>
      <li><i class="fa fa-comments-o"></i> <?php comments_popup_link( __( 'zero comment', 'themoments' ), __( 'one comment', 'themoments' ), __( '% comments', 'themoments' ) ); ?></li>
    </ul>
  </div>

  <div class="post-content">
    <figure class="feature-image">
      <?php if ( has_post_thumbnail() ) : ?>
        <?php the_post_thumbnail('full'); ?>
      <?php endif; ?> 
    </figure>
    
    <article>
      <?php the_content();?>

      <?php
        wp_link_pages( array(
          'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'themoments' ),
          'after'  => '</div>',
        ) );
      ?>     
    </article>

    <div class="post-info"><?php the_category();?><?php the_tags();?></div>

    </div>
  </div>


  