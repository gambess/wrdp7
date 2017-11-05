<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till <header>
 *
 * @package themoments
 */

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="<?php echo esc_url( 'http://gmpg.org/xfn/11' ); ?>">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<?php wp_head(); ?>
</head>


<body <?php body_class(); ?>>
<?php $header_text_color = get_header_textcolor(); ?>
<header>	
<section class="logo-menu">
	<nav class="navbar navbar-default navbar-fixed-top">
		<div class="container">
					<!-- Brand and toggle get grouped for better mobile display -->
				    <div class="navbar-header">
				      	<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
					        <span class="sr-only"><?php esc_html_e( 'Toggle navigation', 'themoments' ); ?></span>
					        <span class="icon-bar"></span>
					        <span class="icon-bar"></span>
					        <span class="icon-bar"></span>
				      	</button>
				      	<div class="logo-tag">
				      		
				      			<?php if ( has_custom_logo() ) : the_custom_logo(); else: ?>
				      			<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><h1 class="site-title" style="color:<?php echo "#". $header_text_color;?>"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></h1>
				      			<h2 class="site-description" style="color:<?php echo "#". $header_text_color;?>"><?php echo esc_html( get_bloginfo( 'description' ) ); ?></h2><?php endif; ?></a>                     
      						
      					</div>
				    </div>

					<!-- Collect the nav links, forms, and other content for toggling -->
					<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">  							
						<?php
				            wp_nav_menu( array(
					                'theme_location'    => 'primary',
					                'depth'             => 8,
					                'container'         => 'div',
					                'menu_class'        => 'nav navbar-nav navbar-right',
					                'fallback_cb'       => 'wp_bootstrap_navwalker::fallback',
					                'walker'            => new Themoments_wp_bootstrap_navwalker()
				                )
				            );
				        ?>
				        
				    </div> <!-- /.end of collaspe navbar-collaspe -->
	</div> <!-- /.end of container -->
	</nav>
</section> <!-- /.end of section -->
</header>