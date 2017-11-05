<?php
/**
 * Template part for displaying posts.
 *
 * @package themoments
 */

?>

<div class="col-sm-6">
    <div class="post-block  eq-blocks">
    <?php if ( has_post_thumbnail() ) : ?>
                <a href="<?php echo esc_url( get_permalink() ); ?>" rel="bookmark"><?php the_post_thumbnail('medium'); ?></a>
                <?php else : ?>
                <a href="<?php echo esc_url( get_permalink() ); ?>" rel="bookmark"><img src="<?php echo get_template_directory_uri(); ?>/images/no-blog-thumbnail.jpg" title="<?php the_title_attribute(); ?>" alt="<?php the_title_attribute(); ?>" class="img-responsive" /></a>
            <?php endif; ?>  
    <div class="summary">
                        <h4><a href="<?php echo esc_url( get_permalink() ); ?>" rel="bookmark"><?php the_title(); ?></a></h4>

    <small class="date"><?php the_date(); ?></small>

        <?php the_excerpt(); ?>
        
        <a href="<?php echo esc_url( get_permalink() ); ?>" rel="bookmark" title="" class="readmore"><?php esc_html_e('Read More','themoments'); ?> </a>

    </div>
</div>
</div>



