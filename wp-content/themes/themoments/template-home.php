<?php
/**
 * Template Name: Front Page 
 * The template used for displaying front page contents
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


  <!-- welcome message -->
  <section class="welcome spacer">
      <div class="container">
        <div class="inside-wrapper">
            <?php 
              $about_ID = get_theme_mod( 'themoments-home-about-page' );
              $about_post = get_post( $about_ID );
            ?>
            <div class="content-block">
                  <h3><?php echo $about_post->post_title; ?></h3>
                  <p><?php echo $about_post->post_content; ?></p> 
                  <a href="<?php echo esc_url( get_permalink( $about_post->ID ) ); ?>" title="<?php esc_attr_e( 'Read More', 'themoments' ); ?>" class="btn btn-danger"><?php esc_html_e( 'Read More', 'themoments' ); ?></a>
            </div> 

            <div class="message">
                <?php
                  $about_image = wp_get_attachment_image_src( get_post_thumbnail_id( $about_post->ID ), 'medium' );
                ?> 
                <img src="<?php echo esc_url( $about_image[0] ); ?>" class="img-responsive">
            </div>
        </div>
      </div>
  </section>
<?php
    wp_reset_postdata();
?>
  <!-- welcome message -->




<!-- theme-slider -->
<section class="theme-slider">
  <div class="container">
      <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">

        <!-- Wrapper for slides -->
        <div class="carousel-inner" role="listbox">
          <?php             
              $sliderp[0] = get_theme_mod( 'themoments-home-slider-page-1' );
              $sliderp[1] = get_theme_mod( 'themoments-home-slider-page-2' );
              $sliderp[2] = get_theme_mod( 'themoments-home-slider-page-3' );
          
              $args = array (
                'post_type' => 'page',
                'post_per_page' => 3,
                'post__in'         => $sliderp,
                'orderby'           =>'post__in',
              );
           
            $loop = new WP_Query( $args );
           
            if ( $loop->have_posts() ) :
              $i=0;
            
              while ( $loop->have_posts() ) : $loop->the_post();
          ?>

          <div class="item <?php echo $i == 0 ? 'active' : ''; ?>">
                <?php if ( has_post_thumbnail() ){
                  $arg =
                    array(
                      'class' => 'img-responsive',
                      'alt' => ''
                    );
                    the_post_thumbnail( '', $arg );
                  } 
                ?>
            <div class="slide-caption">
                <div class="slide-caption-details">
                <h2><?php the_title(); ?></h2>
                <div class="summary"><?php the_excerpt(); ?></div>
                <a href="<?php echo esc_url( get_permalink() ); ?>" class="btn btn-danger"><?php esc_html_e( 'Know More', 'themoments' ); ?></a>
                </div>
            </div>
          </div> <!-- /.end of item -->
        
          <?php
            $i++;
            endwhile;
              wp_reset_postdata();  
            endif;                             
          ?> 
        </div>  <!-- /.end of carousel inner -->

        <!-- Controls -->
        <a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
          <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"><i class="fa fa-angle-left"></i></span>
          <span class="sr-only"><?php esc_html_e( 'Previous', 'themoments' ); ?></span>
        </a>
        <a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
          <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"><i class="fa fa-angle-right"></i></span>
          <span class="sr-only"><?php esc_html_e( 'Next', 'themoments' ); ?></span>
        </a>

      </div> <!-- /.end of carousel example -->
  </div>
</section> <!-- /.end of section -->
<!-- theme-slider -->


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
  		            'cat' => $category_id
  		        );
  		        $loop = new WP_Query( $args );                                   
  		        if ( $loop->have_posts() ) : while ( $loop->have_posts() ) : $loop->the_post();
  		    ?>

      		<div class="col-sm-6">
              <div class="post-block  eq-blocks">
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
<div class="text-center"><a href="<?php echo esc_url( $category_link ); ?>"><?php esc_html_e( 'View All', 'themoments' ); ?></a></div>
		     	</div>  <!-- /.end of container -->
</section>  <!-- /.end of section -->
<!-- post list  -->



<!-- testimonial-services -->
<?php
	$testimonial_id = get_theme_mod( 'testimonial_display' );
		$args = array(
  			'cat' => $testimonial_id
  		);
  		$testimonials= new WP_Query( $args );
  	if ( $testimonials->have_posts() ) :
?>
<div class="testimonial spacer clearfix">
  <div class="container text-center">
    <div class="inside-wrapper">
      <?php
        if ( get_theme_mod( 'testimonial_title' ) )
          $title = get_theme_mod( 'testimonial_title' );                
        else
          $title = get_cat_name( $testimonial_id );
      ?>
    <h4><?php echo esc_html( $title ); ?></h4>
  <div id="carousel-testimonials" class="carousel slide testimonails" data-ride="carousel">
    <div class="carousel-inner">
	<?php $i = 0; ?>
	<?php while ( $testimonials->have_posts() ) : $testimonials->the_post(); ?>
      <div class="item<?php if ( $i == 0 )  echo ' active'; ?>">
		<?php $image= wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' ); ?>
        <img alt="portfolio" src="<?php echo esc_url( $image[0] ); ?>" width="100" class="img-circle">
        <p><?php the_content(); ?></p>      
        <h5><?php the_title(); ?></h5>
      </div>
	<?php $i++; endwhile; wp_reset_postdata(); ?>
  </div>

   <!-- Indicators -->
    <ol class="carousel-indicators">
	<?php $i = 0; ?>
	<?php while ( $testimonials->have_posts() ) : $testimonials->the_post(); ?>
    	<li data-target="#carousel-testimonials" data-slide-to="<?php echo $i; ?>" <?php if ( $i == 0 ) echo 'class="active"'; ?>></li>
	<?php $i++; endwhile; wp_reset_postdata(); ?>
    </ol>
    <!-- Indicators -->
  </div>
  </div>
  </div> 
</div> 
<?php endif; ?>
<!-- testimonial-services -->


<!-- team -->
<?php
	$crew_cat_id = get_theme_mod( 'crew_display' );
		$crew_args = array(
  			'cat' => $crew_cat_id
  		);
  		$crew_mems = new WP_Query( $crew_args );
	
  	if ( $crew_mems->have_posts() ) :
?>
<div class="crewmembers spacer  text-center">
  <div class="container">
    <?php
      if ( get_theme_mod( 'crew_title' ) )
        $title = get_theme_mod( 'crew_title' );                
      else
        $title = get_cat_name( $crew_cat_id );
    ?>
<h4><?php echo esc_html( $title ); ?></h4>
<div class="row team">
<?php while ( $crew_mems->have_posts() ) : $crew_mems->the_post(); ?>
    <div class="col-sm-2 col-xs-6">
		<?php $image= wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' ); ?>
        <img src="<?php echo esc_url( $image[0] ); ?>" alt="<?php echo esc_attr( $title ); ?>" class="img-responsive">
        <h5><?php the_title(); ?></h5>            
    </div>
<?php endwhile; wp_reset_postdata(); ?>
</div> 
</div>
</div>
<?php endif; ?>
<!-- team -->




<!-- clients -->
<?php
	$client_cat_id = get_theme_mod( 'client_display' );
		$client_args = array(
			'cat' => $client_cat_id,
			'posts_per_page' => 6
		);
		$client_mems = new WP_Query( $client_args );
	
	if ( $client_mems ->have_posts() ) :
?>

<div class="clients spacer text-center">
  <div class="container">
  <?php
      if ( get_theme_mod( 'client_section_title' ) )
        $title = get_theme_mod( 'client_section_title' );                
      else
        $title = get_cat_name( $client_cat_id );
    ?>
    <h4><?php echo esc_html( $title ); ?></h4>
	<div class="row">
	     <?php while ( $client_mems ->have_posts() ) : $client_mems ->the_post(); ?>
	     <?php $image= wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );
	     	if ( ! empty( $image ) ): ?>
		     	<div class=" col-sm-2 col-xs-6">	    		
		        	<img src="<?php echo esc_url( $image[0] ); ?>" alt="<?php esc_attr_e( 'Client', 'themoments' ); ?>"> 
		        </div> 
		<?php endif; ?>
	    <?php endwhile; wp_reset_postdata(); ?>
	</div>
  </div>
</div>

<?php endif; ?>
<!-- clients -->

<?php get_footer();?>