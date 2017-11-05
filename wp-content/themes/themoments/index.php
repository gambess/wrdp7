<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package themoments
 */

get_header(); ?>

<?php
  $home_banner_title = get_theme_mod( 'themoments-home-banner-title-setting' );
  $home_banner_desc = get_theme_mod( 'themoments-home-banner-description-setting' );
  $home_banner_link = get_theme_mod( 'themoments-home-banner-link-setting' );
?>
<!-- Header Image -->
<div class="banner-top">
    <?php if ( has_header_image() ) : ?>
        <img src="<?php echo esc_url( get_header_image() ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'title' ) ); ?>" class="img-responsive" />
    <?php endif; ?>
    <div class="banner-text">
       <h1>
        <?php
          if( $home_banner_title )
            echo esc_html( $home_banner_title );
          else
            bloginfo( 'title' );
        ?>
      </h1>
      <h3>
        <?php
          if( $home_banner_desc )
            echo wp_kses_post( $home_banner_desc );
          else
            bloginfo( 'description' );
        ?>          
      </h3>
      <?php if( $home_banner_link ): ?>
        <a href="<?php echo esc_url( $home_banner_link ); ?>" class="btn btn-danger"><?php esc_html_e('Read More', 'themoments'); ?></a>
      <?php endif; ?>
    </div>
</div>
<!-- Header Image -->




<!-- post list  -->
<section class="front-post-list post-list spacer">
    <div class="container">
      <div class="inside-wrapper">
          <?php
            $category_id = get_theme_mod( 'features_display' );
            $category_link = get_category_link( $category_id );
            $category = get_category( $category_id );
          ?>

              <?php
                if ( get_theme_mod( 'features_title' ) )
                  $title = get_theme_mod( 'features_title' );                
                else
                  $title = get_cat_name( $category_id );
              ?>
            <h4>
              <?php echo esc_html( $title ); ?>
            </h4>
            

          <div class="row">

          <?php
              $args = array(
                  'cat' => $category_id,
                  'posts_per_page' => 2
              );
              $loop = new WP_Query( $args );                                   
              if ( $loop->have_posts() ) : while ( $loop->have_posts() ) : $loop->the_post();
          ?>

          <div class="col-sm-6">
              <div class="post-block">
                <?php if ( has_post_thumbnail() ) : ?>
                <a href="<?php echo esc_url( get_permalink() ); ?>" rel="bookmark" class="featured-image"><?php the_post_thumbnail( 'medium' ); ?></a>
                <?php else : ?>
                <a href="<?php echo esc_url( get_permalink() ); ?>" rel="bookmark" class="featured-image"><img src="<?php echo get_template_directory_uri(); ?>/images/no-blog-thumbnail.jpg" title="<?php the_title_attribute(); ?>" alt="<?php the_title_attribute(); ?>" class="img-responsive" /></a>
              <?php endif; ?>  
              <!-- summary -->
              <div class="summary text-center">
                <?php $tags = wp_get_object_terms( $post->ID, 'post_tag' ); ?>
                <div class="post-category">
                  <?php
                  if ( !empty( $tags ) )
                    foreach( $tags as $tag )
                        echo $tag->name." ";
                  ?>
                </div>
              <h4><a href="<?php echo esc_url( get_permalink() ); ?>" rel="bookmark"><?php the_title(); ?></a></h4>
              
              </div>
              <!-- summary -->
              </div>
          </div>
  


          

<?php
            endwhile;
                wp_reset_postdata();
            endif;
        ?>
      </div>
      </div>
          </div>  <!-- /.end of container -->
</section>  <!-- /.end of section -->
<!-- post list  -->






<div class="inside-page post-list">
    <div class="container">
        <div class="row">
        <div  class="col-md-9">
        
        <?php if ( have_posts() ) : ?>

                <?php
                    the_archive_title( '<h4>', '</h4>' );
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