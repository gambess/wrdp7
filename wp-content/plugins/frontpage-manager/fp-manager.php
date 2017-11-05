<?php
  /*
   Plugin Name: Frontpage Manager
   Plugin URI: http://kirilisa.com/projects/frontpage-manager/
   Description: Frontpage manager lets you customize how your frontpage and/or main posts page appears in a number of ways: limiting by category, number of posts, number of words/characters/paragraphs.   
   Version: 1.3
   Author: Elise Bosse
   Author URI: http://kirilisa.com

   Copyright 2009  Elise Bosse  (email : kirilisa@gmail.com)   
   This program is free software; you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation; either version 2 of the License, or
   (at your option) any later version.
   
   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.
   
   You should have received a copy of the GNU General Public License
   along with this program; if not, write to the Free Software
   Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
  */

if (!class_exists("FPManager")) {
  class FPManager {
  
    function FPManager() {
    
    }

    function activate() {
      // add options to database
      add_option("fpm_post_category", 'all');	
      add_option("fpm_post_numposts", 1);
      add_option("fpm_post_cuttype", 'paragraph');	
      add_option("fpm_post_number", 1);
      add_option("fpm_post_linktext", 'view full post &raquo;');
      add_option("fpm_post_ending", '');
      add_option("fpm_striptags", '');      
      add_option("fpm_apply_nonfp", 1);      
      add_option("fpm_static_limit", 0);      
      add_option("fpm_nonfp_limit", 1);      
      add_option("fpm_kill_title", 0);      
    }

    function deactivate() {
      // remove options from database
      delete_option("fpm_post_category");	
      delete_option("fpm_post_numposts");
      delete_option("fpm_post_cuttype");	
      delete_option("fpm_post_number");
      delete_option("fpm_post_linktext");
      delete_option("fpm_post_ending");
      delete_option("fpm_striptags");      
      delete_option("fpm_apply_nonfp");      
      delete_option("fpm_static_limit");      
      delete_option("fpm_nonfp_limit");      
      delete_option("fpm_kill_title");      
    }

    function get_all_categories() {
      global $wpdb;
      return $wpdb->get_results("SELECT t.* from $wpdb->terms t left join $wpdb->term_taxonomy tt on t.term_id = tt.term_id where tt.taxonomy= 'category'");
    }

    function add_admin_page() {
      add_submenu_page('options-general.php', 'Frontpage Manager', 'Frontpage Manager', 10, __FILE__, array('FPManager', 'admin_page'));
    } 

    function add_js() {
      echo file_get_contents(ABSPATH.'wp-content/plugins/frontpage-manager/functions.js');
    }

    function admin_page() {
      if(isset($_POST['fpm_submit'])) {
	$cats = "";

	// posted data
	$categories = $_POST['fpm_post_category'];
	$cuttype = $_POST['fpm_post_cuttype'];
	$numposts = intval(trim($_POST['fpm_post_numposts']));
	$number = intval(trim($_POST['fpm_post_number']));
	$ending = utf8_encode(trim($_POST['fpm_post_ending']));
	$linktext = utf8_encode(trim($_POST['fpm_post_linktext']));	
	$striptags = utf8_encode(trim(strtolower($_POST['fpm_striptags'])));
	$apply_nonfp = intval(trim($_POST['fpm_apply_nonfp']));        
	$static_limit = intval(trim($_POST['fpm_static_limit']));        
	$nonfp_limit = intval(trim($_POST['fpm_nonfp_limit']));        
	$kill_title = intval(trim($_POST['fpm_kill_title']));        

	// default number
	if ($number == '') {
	  if ($cuttype == 'paragraph' || $cuttype == 'none') $number = '1';
	  else if ($cuttype == 'letter') $number = '600';
	  else if ($cuttype == 'word') $number = '200';
	}

	// default numposts
	$numposts = $numposts < 1 ? 1 : $numposts;

	// make categories storable
	if (isset($categories) && !empty($categories)) {
	  foreach ($categories as $cat) {
	    $cats .= trim($cat).",";
	  }
	  $cats = trim($cats, ",");
	  
	  // store categories
	  update_option("fpm_post_category", $cats);	
	  
	  // updated message
	  echo "<div id=\"message\" class=\"updated fade\"><p><strong>Frontpage Manager options updated.</strong></p></div>";
	} else {
	  echo "<div id=\"message\" class=\"updated fade\"><p><strong>You must select at least 1 category.</strong></p></div>";
	}

	// update data in database
	update_option("fpm_post_cuttype", $cuttype);	
	update_option("fpm_post_numposts", $numposts);
	update_option("fpm_post_number", $number);
	update_option("fpm_post_linktext", $linktext);
	update_option("fpm_post_ending", $ending);
	update_option("fpm_striptags", $striptags);
	update_option("fpm_apply_nonfp", $apply_nonfp);
	update_option("fpm_static_limit", $static_limit);
	update_option("fpm_nonfp_limit", $nonfp_limit);
	update_option("fpm_kill_title", $kill_title);
      }
      
      $cats = FPManager::get_all_categories();

      require_once('admin_page.php');
    }


    // limit the posts, if necessary
    function display($content) {      
      $striptags = get_option('fpm_striptags');
      $cuttype = get_option('fpm_post_cuttype');
      $linktext = get_option('fpm_post_linktext');
      $ending = get_option('fpm_post_ending');
      $truncate = get_option('fpm_post_number');
      $static_limit = get_option('fpm_static_limit');
      $nonfp_limit = get_option('fpm_nonfp_limit');
      $readon = TRUE;
      $type = get_option('show_on_front');

      if ($cuttype == 'none') return $content;  // no limitation required    

      // these situations require limiting
      if (($type == 'posts' && is_front_page()) || 
	  ($type == 'page' && is_front_page() && $static_limit) || 
	  ($type == 'page' && is_home() && $nonfp_limit)) {
      
	//if (($fp_only && !is_front_page()) || (!$fp_only && !is_home()) || $cuttype == 'none') return $content;
	//if (!is_front_page() || get_option('show_on_front') != 'posts') return $content;
	//if ($cuttype == 'none') return $content;

	if ($striptags != '') {
	  // make sure tags to strip are clean
	  $striptags = str_replace(array('<',' ','>'), '', $striptags);
	  $tags = explode(',', $striptags);

	  // strip the tags
	  foreach ($tags as $tag) {	  
	    if ($tag == 'all') $content = strip_tags($content, '<p>');
	    else $content = preg_replace('/<\/?'.$tag.'( [^>]+)?>/', '', $content, -1, $cnt);
	  }
	}      

	switch($cuttype) {
	case "word":	
	  $tmp = explode(' ', $content);       	
	  $length = count($tmp);
	  if ($length > $truncate) {
	    $final = implode(' ', array_slice($tmp, 0, $truncate));
	    $final = FPManager::fix_html($final, $ending);
	  } else {
	    $final = $content;
	    $readon = FALSE;
	  }
	  break;

	case "letter":
	  $length = strlen($content);
	  if ($length > $truncate) {
	    $final = FPManager::fix_html(substr($content, 0, $truncate), $ending);
	  } else {
	    $final = $content;
	    $readon = FALSE;
	  }
	  break;

	case "paragraph":
	  $final = "";
	  $idx = 0;

	  $tmp = explode('</p>', $content);

	  // clean array of whitespace/null values
	  $tmp = array_values(array_filter(array_map("trim", $tmp)));

	  $length = count($tmp);
	  if ($length > $truncate) {
	    while ($idx < $truncate) {
	      $final .= $tmp[$idx]."</p>";
	      $idx++;
	    }
	  } else {
	    $final = $content;
	    $readon = FALSE;
	  }
	  break;
	}
            
	if ($readon) {
	  $final .= "\r\n".'<div class="fpm_readon"><a href="' . get_permalink() . '" rel="nofollow">';
	  $final .= utf8_encode($linktext) . "</a></div>\r\n";
	}

	return $final;
      } 

      return $content;
    }



    function trim_val($val) {
      //return trim($val);
      return $val.'xyz';
    }

    function fix_html($str, $ending) {
      $missing = array();

      // fix any closing tag whitespace
      $str = preg_replace('/<(\/[a-zA-Z]+)\s?>/', '<\1>', $str);
      
      // fix any truncated tags first
      $str = preg_replace('/<\/?[a-zA-Z0-9_= :;"-]*$/', '', $str);

      // add nominated ending, if any
      $str .= $ending;

      // fetch all open tags
      preg_match_all('/<[a-zA-Z]+/', $str, $opentags, PREG_OFFSET_CAPTURE);

      // see if the open tags are closed in the excerpt
      while ($opentags[0]) {
	$info = array_shift($opentags[0]); 
	$tag = $info[0];
	$offset = $info[1];
	
	// ignore tags that don't need to be closed
	if (in_array($tag, array('<img', '<hr', '<br', '<input'))) continue;

	// check for closing tag
	$closetag = str_replace('<', '</', $tag) . '>';
	if (!strpos($str, $closetag, $offset)) $missing[] = $closetag;
      }
      
      // close any remaining open tags
      for ($i = count($missing) - 1; $i >= 0; $i--) {
	$str .= $missing[$i];
      }

      return $str;
    }

    function alter_query() {
      global $wp_query;

      $type = get_option('show_on_front');
      $categories = get_option("fpm_post_category"); 
      $numposts = intval(get_option('fpm_post_numposts'));
      $apply_nonfp = get_option('fpm_apply_nonfp');

      // these situations require alteration
      //if (is_front_page() && $type == 'posts') {
      if (($type == 'posts' && is_front_page()) || 
	  ($type == 'page' && is_home() && $apply_nonfp)) {
            
	// limit categories shown in 2 different ways
	if (preg_match('/^([1-9]{1}[0-9]*,?)+$/', $categories)) {
	  $wp_query->query_vars['cat'] = $categories;
	  $wp_query->query_vars['category__in'] = explode(',', $categories);
	}

	// limit number of posts shown
	$wp_query->query_vars['showposts'] = $numposts;
      }
    }

    function set_title() {
      global $wp_query;
      $kill_title = get_option('fpm_kill_title');
      $apply_nonfp = get_option('fpm_apply_nonfp');

      if ($kill_title && ($apply_nonfp || is_front_page())) {
	$wp_query->query_vars['cat'] = "";
	$wp_query->query_vars['category__in'] = Array();
      }
    }

  }
}

  // instantiate class
if (class_exists("FPManager")) {
  $fpmanager = new FPManager();
}

// actions/filters
if (isset($fpmanager)) {
  add_filter('pre_get_posts', array('FPManager', 'alter_query')); // deal with which posts to show
  add_filter('the_content', array('FPManager', 'display')); // deal with how much of each post to show
  add_action('get_header', array('FPManager', 'set_title')); // get rid of title category if set

  // administrative options
  add_action('admin_menu', array('FPManager', 'add_admin_page'));
  add_action('admin_head', array('FPManager', 'add_js')); 

  // activate/deactivate
  register_activation_hook(__FILE__, array('FPManager', 'activate'));
  register_deactivation_hook(__FILE__, array('FPManager', 'deactivate'));
}
?>
