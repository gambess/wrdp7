<?php global $redux_builder_amp; ?>
<?php
add_action( 'amp_post_template_head', function() {
    remove_action( 'amp_post_template_head', 'amp_post_template_add_fonts' );
}, 9 );
add_action('amp_post_template_css', 'ampforwp_additional_style_input_2');

function ampforwp_additional_style_input_2( $amp_template ) {
	global $redux_builder_amp;
	global $post;
	$post_id = '';
	$post_id = $post->ID;
	$get_customizer = new AMP_Post_Template( $post_id );
	// Get content width
	$content_max_width       = absint( $get_customizer->get( 'content_max_width' ) );
	// Get template colors
	$theme_color             = $get_customizer->get_customizer_setting( 'theme_color' );
	$text_color              = $get_customizer->get_customizer_setting( 'text_color' );
	$muted_text_color        = $get_customizer->get_customizer_setting( 'muted_text_color' );
	$border_color            = $get_customizer->get_customizer_setting( 'border_color' );
	$link_color              = $get_customizer->get_customizer_setting( 'link_color' );
	$header_background_color = $get_customizer->get_customizer_setting( 'header_background_color' );
	$header_color            = $get_customizer->get_customizer_setting( 'header_color' );
	?>

/* Global Styling */
body{	background: #f1f1f1; font: 16px/1.4 Sans-serif; }
a {	color: #312C7E;	text-decoration: none }
.clearfix, .cb { clear: both }
amp-iframe{ max-width: 100%; margin-bottom : 20px; }
amp-anim { max-width: 100%; }
.alignleft{ margin-right: 12px; margin-bottom:5px; float: left; }
.alignright{ float:right; margin-left: 12px; margin-bottom:5px; }
.aligncenter{ text-align:center; margin: 0 auto }
#statcounter{width: 1px;height:1px;}

/* Template Styles */
.amp-wp-content, .amp-wp-title-bar div {
    <?php if ( $content_max_width > 0 ) : ?>
    max-width: <?php echo sprintf( '%dpx', $content_max_width ); ?>;
    margin: 0 auto;
    <?php endif; ?>
}

/* Slide Navigation code */
.nav_container{ padding: 18px 15px; background: #312C7E; color: #fff; text-align: center }
amp-sidebar{ width: 250px; }
.amp-sidebar-image{ line-height: 100px; vertical-align:middle; }
.amp-close-image{ top: 15px; left: 225px; cursor: pointer; }
.toggle-navigationv2 ul{ list-style-type: none; margin: 0; padding: 0; }
.toggle-navigationv2 ul ul li a{ padding-left: 35px; background: #fff; display: inline-block }
.toggle-navigationv2 ul li a{ padding: 15px 25px; width: 100%; display: inline-block; background: #fafafa; font-size: 14px; border-bottom: 1px solid #efefef; }
.close-nav{ font-size: 12px; background: rgba(0, 0, 0, 0.25); letter-spacing: 1px; display: inline-block; padding: 10px; border-radius: 100px; line-height: 8px; margin: 14px; left: 191px; color: #fff; }
.close-nav:hover{ background: rgba(0, 0, 0, 0.45);}
.toggle-navigation ul{ list-style-type: none; margin: 0; padding: 0; display: inline-block; width: 100% }
.menu-all-pages-container:after{ content: ""; clear: both }
.toggle-navigation ul li{ font-size: 13px; border-bottom: 1px solid rgba(0, 0, 0, 0.11); padding: 11px 0px; width: 25%; float: left; text-align: center; margin-top: 6px }
.toggle-navigation ul ul{ display: none }
.toggle-navigation ul li a{ color: #eee; padding: 15px; }
.toggle-navigation{ display: none; background: #444; }
.toggle-text{ color: #fff; font-size: 12px; text-transform: uppercase; letter-spacing: 3px; display: inherit; text-align: center; }
.toggle-text:before{ content: "..."; font-size: 32px; position: ; font-family: georgia; line-height: 0px; margin-left: 0px; letter-spacing: 1px; top: -3px; position: relative; padding-right: 10px; }
.nav_container:hover + .toggle-navigation, .toggle-navigation:hover, .toggle-navigation:active, .toggle-navigation:focus{ display: inline-block; width: 100%; }
/* Category 2 */
.category-widget-wrapper{ padding:30px 15% 10px 15% }
.amp-category-block ul{ list-style-type:none;padding:0 }
.amp-category-block-btn{ display: block; text-align: center; font-size: 13px; margin-top: 15px; border-bottom: 1px solid #f1f1f1; text-decoration: none; padding-bottom: 8px;}
.category-widget-gutter h4{ margin-bottom: 0px;}
.category-widget-gutter ul{ margin-top: 10px; list-style-type:none; padding:0 }
.amp-category-post{ width: 32%;display: inline-block; word-wrap: break-word;float: left;}
.amp-category-post amp-img{ margin-bottom:5px; }
.amp-category-block li:nth-child(3){ margin: 0 1%;}
.searchmenu{ margin-right: 15px; margin-top: 11px; position: absolute; top: 0; right: 0; }
.searchmenu button{ background:transparent; border:none }
.closebutton{ background: transparent; border: 0; color: rgba(255, 255, 255, 0.7); border: 1px solid rgba(255, 255, 255, 0.7); border-radius: 30px; width: 32px; height: 32px; font-size: 12px; text-align: center; position: absolute; top: 12px; right: 20px; outline:none }
amp-lightbox{ background: rgba(0, 0, 0,0.85); }
/* CSS3 icon */

[class*=icono-]:after, [class*=icono-]:before { content: ''; pointer-events: none; }
.icono-search:before{ position: absolute; left: 50%; -webkit-transform: rotate(270deg); -ms-transform: rotate(270deg); transform: rotate(270deg); width: 2px; height: 9px; box-shadow: inset 0 0 0 32px; top: 0px; border-radius: 0 0 1px 1px; left: 14px; }
[class*=icono-] { display: inline-block; vertical-align: middle; position: relative; font-style: normal; color: #f42; text-align: left; text-indent: -9999px; direction: ltr }
.icono-search { -webkit-transform: translateX(-50%); -ms-transform: translateX(-50%); transform: translateX(-50%) }
.icono-search { border: 1px solid; width: 10px; height: 10px; border-radius: 50%; -webkit-transform: rotate(45deg); -ms-transform: rotate(45deg); transform: rotate(45deg); margin: 4px 4px 8px 8px; }
.searchform label{ color: #f7f7f7; display: block; font-size: 10px; letter-spacing: 0.3px; line-height: 0; opacity:0.6 }
.searchform{ background: transparent; left: 20%; position: absolute; top: 35%; width: 60%; max-width: 100%; transition-delay: 0.5s; }
.searchform input{ background: transparent; border: 1px solid #666; color: #f7f7f7; font-size: 14px; font-weight: 400; line-height: 1; letter-spacing: 0.3px; text-transform: capitalize; padding: 20px 0px 20px 30px; margin-top: 15px; width: 100%; }
#searchsubmit{opacity:0}
.hide{display:none}
.headerlogo a, [class*=icono-]{ color: #F42F42 }
/* Pagination */
.amp-wp-content.pagination-holder { background: none; padding: 0; box-shadow: none; height: auto; min-height: auto; }
#pagination{ width: 100%; margin-top: 15px; }
#pagination .next{ float: right; margin-bottom: 10px; }
#pagination .prev{ float: left }
#pagination .next a, #pagination .prev a{ margin-bottom: 12px; background: #fefefe; -moz-border-radius: 2px; -webkit-border-radius: 2px; border-radius: 2px; -moz-box-shadow: 0 2px 3px rgba(0,0,0,.05); -webkit-box-shadow: 0 2px 3px rgba(0,0,0,.05); box-shadow: 0 2px 3px rgba(0,0,0,.05); padding: 11px 15px; font-size: 12px; color: #666; }
<?php 
 if(is_single()){?>
/* Sticky Social bar in Single */
.ampforwp-social-icons-wrapper{ margin: 0.65em 0px 0.65em 0px; height: 28px; }
.sticky_social{ width: 100%; bottom: 0; display: block; left: 0; box-shadow: 0px 4px 7px #000; background: #fff; padding: 7px 0px 0px 0px; position: fixed; margin: 0; z-index: 10; text-align: center; }
.custom-amp-socialsharing-icon{ width: 50px; height: 28px; display: inline-block; background: #5cbe4a;position: relative; top: -8px; padding-top: 0px; }
.custom-amp-socialsharing-icon amp-img{ top: 4px; }
.custom-amp-socialsharing-line{background:#00b900}
.ampforwp-social-icons custom-amp-socialsharing-vk{background:#45668e}
.custom-amp-social-sharing-odnoklassniki{background:#ed812b}
<?php }?>
/* Header */
#header{ background: #fff; text-align: center; }
#header h3{ text-align: center; font-size: 20px; font-weight: bold; line-height: 1; padding: 15px; margin: 0; }
.amp-logo{ margin: 15px 0px 10px 0px; }
main { padding: 30px 15% 10px 15%; }
.amp-wp-content.widget-wrapper{padding: 0px 15%;}
main .amp-wp-content{ margin-bottom: 12px;  padding: 15px; }
.amp-loop-list, .featured-image-content, .the_content, .taxonomy-description{background: #fff; -moz-border-radius: 2px; -webkit-border-radius: 2px; border-radius: 2px; -moz-box-shadow: 0 2px 3px rgba(0,0,0,.05); -webkit-box-shadow: 0 2px 3px rgba(0,0,0,.05); box-shadow: 0 2px 3px rgba(0,0,0,.05);}
.home-post_image{ float: right; margin-left: 15px; margin-bottom: -6px; }
.amp-wp-title{ margin-top: 0px; }
h2.amp-wp-title{ line-height: 30px; }
h2.amp-wp-title a{ font-weight: 300; color: #000; font-size: 20px; }
h2.amp-wp-title , .amp-wp-post-content p{ margin: 0 0 0 5px; }
/* For Excerpt */
.amp-wp-post-content .small-screen-excerpt{
	display: none;
}
.amp-wp-post-content .large-screen-excerpt {
	display: block;
}
.amp-wp-post-content p{ font-size: 12px; color: #999; line-height: 20px; margin: 3px 0 0 5px; }
main .amp-archive-heading{ background : none; box-shadow: none; padding: 5px; }
.page-title{
	font-size: 1.17em;
	padding: 6px 0;
    }
/* Footer */
#footer{ background : #fff; font-size: 13px; text-align: center; letter-spacing: 0.2px; padding: 20px 0; }
#footer p:first-child{ margin-bottom: 12px; }
#footer p{ margin: 0 }
.footer_menu ul{ list-style-type: none; padding: 0; text-align: center; margin: 0px 20px 25px 20px; line-height: 27px; font-size: 13px }
.footer_menu ul li{ display:inline; margin:0 10px; }
.footer_menu ul li:first-child{ margin-left:0 }
.footer_menu ul li:last-child{ margin-right:0 }
.footer_menu ul ul{ display:none }
<?php if(is_singular() || is_home() && $redux_builder_amp['amp-frontpage-select-option'] && ampforwp_get_blog_details() == false ){ ?>
/* Single */
.comment-button-wrapper{ margin-bottom: 0px; margin-top: 60px; text-align:center }
.comment-button-wrapper a{ color: #fff; background: #312c7e; font-size: 13px; padding: 10px 20px 10px 20px; box-shadow: 0 0px 3px rgba(0,0,0,.04); border-radius: 80px; }
h1.amp-wp-title{ text-align: center; margin: 0.7em 0px 0.6em 0px; font-size: 1.5em; }
.amp-wp-content.post-title-meta, .amp-wp-content.post-pagination-meta{ background: none; padding:  0; box-shadow:none }
.post-pagination-meta{ min-height:75px }
.single-post .post-pagination-meta{ min-height:auto }
.single-post .ampforwp-social-icons{ display:inline-block }
.post-pagination-meta .amp-wp-tax-category, .post-title-meta .amp-wp-tax-tag{ display : none; }
.amp-meta-wrapper{ border-bottom: 1px solid #DADADA; padding-bottom:10px; display:inline-block; width:100%; margin-bottom:0 }
.amp-wp-meta{ padding-left: 0; }
.amp-wp-tax-category{ float:right }
.amp-wp-tax-tag, .amp-wp-meta li{ list-style: none; display: inline-block; }
li.amp-wp-tax-category{ float: right }
.amp-wp-byline, .amp-wp-posted-on{ float: left }
.amp-wp-content amp-img{ max-width: 100%; }
figure{ margin: 0; }
figcaption{ font-size: 11px; margin-bottom: 11px; background: #eee; padding: 6px 8px; }

.amp-wp-author:before{ content: "By "; color: #555; }
.amp-wp-author{ margin-right: 1px; }
.amp-wp-meta{ font-size: 12px; color: #555; }
.amp-wp-author-name:before{content:'By';}
.amp-ad-wrapper{ text-align: center }
.single-post main{ padding:12px 15% 10px 15% }
.the_content p{ margin-top: 5px; color: #333; font-size: 15px; line-height: 26px; margin-bottom: 15px; }
.amp-wp-tax-tag{ font-size: 13px; border: 0; display: inline-block; margin: 0.5em 0px 0.7em 0px; width: 100%; }
main .amp-wp-content.featured-image-content{ padding: 0px; border: 0; margin-bottom: 0; box-shadow: none }
.amp-wp-article-featured-image amp-img {margin: 0 auto;}
.amp-wp-article-featured-image.wp-caption .wp-caption-text, .ampforwp-gallery-item .wp-caption-text{color: #696969; font-size: 11px; line-height: 15px; background: #eee; margin: 0; padding: .66em .75em; text-align: center;}
.ampforwp-gallery-item.amp-carousel-slide { padding-bottom: 20px;}
.amp-wp-content.post-pagination-meta{ max-width: 1030px; }
.single-post .ampforwp-social-icons.ampforwp-social-icons-wrapper{ display: block; margin: 0.9em auto 0.9em auto ; max-width: 1030px; }
.amp-wp-article-header.amp-wp-article-category.ampforwp-meta-taxonomy{ margin: 10px auto; max-width: 1030px; } .ampforwp_single_excerpt { margin-bottom:15px; font-size: 15px;}
.single-post .amp_author_area amp-img{ margin: 0; float: left; margin-right: 12px; border-radius: 60px; }
.single-post .amp_author_area .amp_author_area_wrapper{ display: inline-block; width: 100%; line-height: 1.4; margin-top: 2px; font-size: 13px; color:#333; font-family: sans-serif; }
<?php if(is_single()){?>
/* Related Posts */
main .amp-wp-content.relatedpost{ background: none; box-shadow: none; max-width: 1030px; padding:0px 0 0 0; margin:1.8em auto 1.5em auto }
 .related_posts .related-title, .comments_list h3{ font-size: 14px; font-weight: bold; letter-spacing: 0.4px; margin: 15px 0 10px 0; color: #333; }
.related_posts .related-title {
	display: block;
}
.related_posts ol{ list-style-type:none; margin:0; padding:0 }
.related_posts ol li{ display:inline-block; width:100%; margin-bottom: 12px; background: #fefefe; -moz-border-radius: 2px; -webkit-border-radius: 2px; border-radius: 2px; -moz-box-shadow: 0 2px 3px rgba(0,0,0,.05); -webkit-box-shadow: 0 2px 3px rgba(0,0,0,.05); box-shadow: 0 2px 3px rgba(0,0,0,.05); padding: 0px; }
.related_posts .related_link{ margin-top:18px; margin-bottom:10px; margin-right:10px }
.related_posts .related_link a{ font-weight: 300; color: #000; font-size: 18px; }
.related_posts ol li amp-img{ width:100px; float:left; margin-right:15px }
.related_posts ol li p{ font-size: 12px; color: #999; line-height: 1.2; margin: 12px 0 0 0; }
.no_related_thumbnail{ padding: 15px 18px; }
.no_related_thumbnail .related_link{ margin: 16px 18px 20px 19px; }
<?php }
if($redux_builder_amp['wordpress-comments-support'] ==1) { ?>
/* Comments */
.page-numbers{padding: 9px 10px;background: #fff;font-size: 14px}
.ampforwp-comment-wrapper{margin:1.8em 0px 1.5em 0px}
main .amp-wp-content.comments_list {background: none;box-shadow: none;max-width: 1030px;padding:0}
.comments_list div{ display:inline-block;}
.comments_list ul{ margin:0;padding:0}
.comments_list ul.children{ padding-bottom:10px; margin-left: 4%; width: 96%;} 
.comments_list ul li p{ margin:0;font-size:15px;clear:both;padding-top:16px; word-break: break-word;}
.comments_list ul li{ font-size:13px;list-style-type:none; margin-bottom: 12px; background: #fefefe; -moz-border-radius: 2px; -webkit-border-radius: 2px; border-radius: 2px; -moz-box-shadow: 0 2px 3px rgba(0,0,0,.05); -webkit-box-shadow: 0 2px 3px rgba(0,0,0,.05); box-shadow: 0 2px 3px rgba(0,0,0,.05);padding: 0px;max-width: 1000px;width:100%;}
.comments_list ul li .comment-body{ padding: 25px;width: 91%;}
.comments_list ul li .comment-body .comment-author{ margin-right:5px}
.comment-author{ float:left }
.comment-author-img{float: left; margin-right: 5px; border-radius: 60px;}
.single-post footer.comment-meta{ padding-bottom: 0; line-height: 1.7;}
.comments_list li li{ margin: 20px 20px 10px 20px;background: #f7f7f7;box-shadow: none;border: 1px solid #eee;}
.comments_list li li li{ margin:20px 20px 10px 20px}
.comment-content amp-img{max-width: 300px;}
<?php } ?>
.amp-facebook-comments{margin-top:45px}
/* ADS */
.amp_home_body .amp_ad_1{ margin-top: 10px; margin-bottom: -20px; }
.single-post .amp_ad_1{ margin-top: 10px; margin-bottom: -20px; }
html .single-post .ampforwp-incontent-ad-1 { margin-bottom: 10px; }
.amp-ad-4{ margin-top:10px; }
<?php if($redux_builder_amp['amp-enable-notifications']==1){ ?>
/* Notifications */
#amp-user-notification1 p { display: inline-block; }
amp-user-notification { padding: 5px; text-align: center; background: #fff; border-top: 1px solid; }
amp-user-notification button { padding: 8px 10px; background: #000; color: #fff; margin-left: 5px; border: 0; }
amp-user-notification button:hover { cursor: pointer }
<?php } ?>
.amp-wp-content blockquote { background-color: #fff; border-left: 3px solid; margin: 0; padding: 15px 20px 8px 24px; background: #f3f3f3; }
pre { white-space: pre-wrap; }
/* Tables */
table { display: -webkit-box; display: -ms-flexbox; display: flex; -ms-flex-wrap: wrap; flex-wrap: wrap; overflow-x: auto; }
table a:link { color: #666; font-weight: bold; text-decoration: none; }
table a:visited { color: #999999; font-weight: bold; text-decoration: none; }
table a:active,
table a:hover { color: #bd5a35; text-decoration: underline; }
table { font-family: Arial, Helvetica, sans-serif; color: #666; font-size: 12px; text-shadow: 1px 1px 0px #fff; background: #eee; margin: 0px; width: 95%; }
table th { padding: 21px 25px 22px 25px; border-top: 1px solid #fafafa; border-bottom: 1px solid #e0e0e0; background: #ededed; background: -webkit-gradient(linear, left top, left bottom, from(#ededed), to(#ebebeb)); background: -moz-linear-gradient(top, #ededed, #ebebeb); }
table th:first-child { text-align: left; padding-left: 20px; }
table tr:first-child th:first-child { -moz-border-radius-topleft: 3px; -webkit-border-top-left-radius: 3px; border-top-left-radius: 3px; }
table tr:first-child th:last-child { -moz-border-radius-topright: 3px; -webkit-border-top-right-radius: 3px; border-top-right-radius: 3px; }
table tr { text-align: center; padding-left: 20px; }
table td:first-child { text-align: left; padding-left: 20px; border-left: 0; }
table td { padding: 18px; border-top: 1px solid #ffffff; border-bottom: 1px solid #e0e0e0; border-left: 1px solid #e0e0e0; background: #fafafa; background: -webkit-gradient(linear, left top, left bottom, from(#fbfbfb), to(#fafafa)); background: -moz-linear-gradient(top, #fbfbfb, #fafafa); }
table tr.even td { background: #f6f6f6; background: -webkit-gradient(linear, left top, left bottom, from(#f8f8f8), to(#f6f6f6)); background: -moz-linear-gradient(top, #f8f8f8, #f6f6f6); }
table tr:last-child td {border-bottom: 0;}
table tr:last-child td:first-child { -moz-border-radius-bottomleft: 3px; -webkit-border-bottom-left-radius: 3px; border-bottom-left-radius: 3px; }
table tr:last-child td:last-child { -moz-border-radius-bottomright: 3px; -webkit-border-bottom-right-radius: 3px; border-bottom-right-radius: 3px; }
table tr:hover td { background: #f2f2f2; background: -webkit-gradient(linear, left top, left bottom, from(#f2f2f2), to(#f0f0f0)); background: -moz-linear-gradient(top, #f2f2f2, #f0f0f0); }
.hide-meta-info{ display: none; }
<?php }?>
/* Responsive */
@media screen and (min-width: 650px) { table {display: inline-table;}  }
@media screen and (max-width: 800px) { .single-post main{ padding: 12px 10px 10px 10px } }
@media screen and (max-width: 630px) { .related_posts ol li p{ display:none } .related_link { margin: 16px 18px 20px 19px; } .amp-category-post {line-height: 1.45;font-size: 14px; } .amp-category-block li:nth-child(3) {margin:0 0.6%} }
@media screen and (max-width: 510px) { .ampforwp-tax-category span{ display:none }
.related_posts ol li p{ line-height: 1.6; margin: 7px 0 0 0; }
.related_posts .related_link { margin: 17px 18px 17px 19px; }
.comments_list ul li .comment-body{ width:auto }
}
@media screen and (max-width: 425px) { .related_posts .related_link p{ display:none } .alignright, .alignleft {float: none;} .related_posts .related_link { margin: 13px 18px 14px 19px; } .related_posts .related_link a{ font-size: 18px; line-height: 1.7; } .amp-meta-wrapper{ display: inline-block; margin-bottom: 0px; margin-top: 8px; width:100% } .ampforwp-tax-category{ padding-bottom:0 } h1.amp-wp-title{ margin: 16px 0px 13px 0px; } .amp-wp-byline{ padding:0 }   .related_posts .related_link a { font-size: 17px; line-height: 1.5; } }
@media screen and (max-width: 375px) { #pagination .next a, #pagination .prev a{ padding: 10px 6px; font-size: 11px; color: #666; } .related_posts .related-title, .comments_list h3{ margin-top:15px; } #pagination .next{ margin-bottom:15px;} .related_posts .related_link a { font-size: 15px; line-height: 1.6; } }
@media screen and (max-width: 340px) { .related_posts .related_link a { font-size: 15px; } .single-post main{ padding: 10px 5px 10px 5px } .the_content .amp-ad-wrapper{ text-align: center; margin-left: -13px; } .amp-category-post {line-height: 1.45;font-size: 12px; } .amp-category-block li:nth-child(3) {margin:0%} }
@media screen and (max-width: 320px) { .related_posts .related_link a { font-size: 13px; } h1.amp-wp-title{ font-size:17px; padding:0px 4px	} }
@media screen and (max-width: 400px) { .amp-wp-title{ font-size: 19px; margin: 21px 10px -1px 10px; } }
@media screen and (max-width: 767px) {  .amp-wp-post-content .large-screen-excerpt { display: none; } .amp-wp-post-content .small-screen-excerpt { display: block; } main, .amp-category-block, .category-widget-wrapper{ padding: 15px 18px 0px 18px; } .toggle-navigation ul li{ width: 50% }    }
@media screen and (max-width: 495px) { h2.amp-wp-title a{ font-size: 17px; line-height: 26px;} }
<?php if($redux_builder_amp['amp-rtl-select-option'] == true) { ?>
header, amp-sidebar, article, footer, main { direction: rtl; }
.amp-wp-header .amp-wp-site-icon { position: relative;float: left; }
.amp-wp-header .nav_container { float: left;right: initial;left: -11px; }
.amp-wp-header .amp-wp-site-icon { top: -3px;right: initial;left: -11px; }
.amp-wp-byline, .amp-wp-posted-on { float:right }
.amp-wp-tax-category { float:left }
.related_posts ol li amp-img { float:right; margin-right:0px; margin-left:15px }
main .amp-archive-heading { direction:rtl }
.searchform { direction:rtl }
.closebutton { right:0; left:20px }
.amp-meta-wrapper { padding-right:0 }
.comment-author { float:right; margin-left:5px; }
.amp-ad-wrapper, .amp-wp-article amp-ad{ direction: ltr; }
.toggle-navigationv2 ul li a { padding: 15px 8px; width: 95%;}
<?php } ?>
.amp-wp-tax-tag a, a, .amp-wp-author, .headerlogo a, [class*=icono-] { color: <?php echo sanitize_hex_color( $header_background_color ); ?>;; }
body a {color: <?php echo $redux_builder_amp['amp-opt-color-rgba-link-design2']['color'];?> }
.amp-wp-content blockquote{ border-color:<?php echo sanitize_hex_color( $header_background_color ); ?>;; }
.nav_container, .comment-button-wrapper a { background:  <?php echo sanitize_hex_color( $header_background_color ); ?>;; }
.nav_container a{ color:<?php echo sanitize_hex_color( $header_color ); ?> }
amp-user-notification { border-color:  <?php echo sanitize_hex_color( $header_background_color ); ?>;; }
amp-user-notification button { background-color:  <?php echo sanitize_hex_color( $header_background_color ); ?>;; }
<?php if( $redux_builder_amp['enable-single-social-icons'] == true && is_socialshare_or_socialsticky_enabled_in_ampforwp() )  { ?>
.single-post footer { padding-bottom: 40px; }
.amp-ad-2{ margin-bottom: 50px; }
<?php } ?>
/**/
.amp-wp-author:before{ content: " <?php global $redux_builder_amp; echo ampforwp_translation($redux_builder_amp['amp-translator-by-text'], 'By '); ?>  "; }
.ampforwp-tax-category span:first-child:after { content: ' '; }
.ampforwp-tax-category span:after,.ampforwp-tax-tag span:after { content: ', '; }
.ampforwp-tax-category span:last-child:after, .ampforwp-tax-tag span:last-child:after { content: ' '; }
.amp-wp-article-content img { max-width: 100%; }
<?php if ($redux_builder_amp['ampforwp-callnow-button']) { ?>
.callnow{ position: relative; top: -35px; right: 39px }
.callnow a:before { content: ""; position: absolute; right: 23px; width: 4px; height: 8px; border-width: 6px 0 6px 3px; border-style: solid; border-color:<?php echo $redux_builder_amp['amp-opt-color-rgba-colorscheme-call']['color']; ?>; background: transparent; transform: rotate(-30deg); box-sizing: initial; border-top-left-radius: 3px 5px; border-bottom-left-radius: 3px 5px; }
<?php } ?>
<?php if ( class_exists('TablePress') ) { ?>
.tablepress-table-description {	clear: both; display: block; }
.tablepress { border-collapse: collapse; border-spacing: 0; width: 100%; margin-bottom: 1em; border: none; }
.tablepress th, .tablepress td { padding: 8px; border: none; background: none; text-align: left; }
.tablepress tbody td { vertical-align: top; }
.tablepress tbody td, .tablepress tfoot th { border-top: 1px solid #dddddd; }
.tablepress tbody tr:first-child td { border-top: 0; }
.tablepress thead th { border-bottom: 1px solid #dddddd; }
.tablepress thead th, .tablepress tfoot th { background-color: #d9edf7;	font-weight: bold; vertical-align: middle; }
.tablepress .odd td {	background-color: #f9f9f9; }
.tablepress .even td { background-color: #ffffff; }
.tablepress .row-hover tr:hover td { background-color: #f3f3f3; }
@media (min-width: 768px) and (max-width: 1600px) {.tablepress { overflow-x: none; } }
@media (min-width: 320px) and (max-width: 767px) {.tablepress { display: inline-block; overflow-x: scroll; } }
<?php }  ?>
.design_2_wrapper .amp-loop-list .amp-wp-meta {display: none;}
<?php if(!is_home() && $redux_builder_amp['ampforwp-bread-crumb'] == 1 ) { ?>
.breadcrumb{line-height: 1;margin-bottom:6px;}
.breadcrumb ul, .category-single ul{padding:0; margin:0;}
.breadcrumb ul li{display:inline;}
.breadcrumb ul li a{font-size:12px;}
.breadcrumb ul li a::after {content: "►";display: inline-block;font-size: 8px;padding: 0 6px 0 7px;vertical-align: middle;opacity: 0.5;position:relative;top: -0.5px;}
.breadcrumb ul li:hover a::after{color:#c3c3c3;}
.breadcrumb ul li:last-child a::after{display:none;}
<?php } ?> 
.amp-menu > li > a > amp-img, .sub-menu > li > a > amp-img { display: inline-block; margin-right: 4px; }
<?php echo $redux_builder_amp['css_editor']; } ?>
