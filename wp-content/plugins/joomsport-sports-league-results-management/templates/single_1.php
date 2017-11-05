<?php
/**
 * The Template for displaying all single posts
 *
 * @package WordPress
 * @subpackage Twenty_Twelve
 * @since Twenty Twelve 1.0
 */

get_header(); ?>

<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">
                    <?php

                    ?>
                    <div class="entry-content">
                        <?php
                        require_once JOOMSPORT_PATH . 'sportleague' . DIRECTORY_SEPARATOR . 'sportleague.php';

                        if ( post_password_required() ) {
                            echo get_the_password_form();
                            return;
                        }
                        $controllerSportLeague->execute();
                        ?>
                    </div>
                    

		</div><!-- #content -->
	</main><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>