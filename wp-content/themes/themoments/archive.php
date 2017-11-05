<?php
/**
 * The template for displaying archive pages.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package themoments
 */

get_header(); ?>
<div class="inside-page post-list">
    <div class="container">
        <div class="row">
        <div  class="col-md-9">
        
        <?php if ( have_posts() ) : ?>

                <?php
                    the_archive_title( '<h1>', '</h1>' );
                    the_archive_description( '<div class="taxonomy-description">', '</div>' );
                ?>
        <div class="row">
            <?php /* Start the Loop */ ?>
            <?php while ( have_posts() ) : the_post(); ?>

                <?php

                    /*
                     * Include the Post-Format-specific template for the content.
                     * If you want to override this in a child theme, then include a file
                     * called content-___.php (where ___ is the Post Format name) and that will be used instead.
                     */
                    get_template_part( 'template-parts/content' );
                ?>

            <?php endwhile; ?>

            

        <?php else : ?>

            <?php get_template_part( 'template-parts/content', 'none' ); ?>

        <?php endif; ?>
        </div>
        <div class="page-nav"><?php the_posts_navigation(); ?></div>
        </div>
    <div class="col-md-3"><?php get_sidebar(); ?>
    </div>
    </div>

</div>
</div>
<?php get_footer(); ?>
