<div class="wrap">
<h2>Frontpage Manager</h2>

<?php
$postcats = explode(",", get_option("fpm_post_category")); 
$limitpostby = get_option("fpm_post_cuttype");
$input_numposts = get_option("fpm_post_numposts");
$input_number = get_option("fpm_post_number");
$input_linktext = htmlspecialchars(get_option("fpm_post_linktext"));
$input_ending = htmlspecialchars(get_option("fpm_post_ending"));
$striptags = htmlspecialchars(get_option("fpm_striptags"));
$apply_nonfp = htmlspecialchars(get_option("fpm_apply_nonfp"));
$static_limit = htmlspecialchars(get_option("fpm_static_limit"));
$nonfp_limit = htmlspecialchars(get_option("fpm_nonfp_limit"));
$kill_title = get_option("fpm_kill_title");

$apply_nonfp_yes = $apply_nonfp == 1 ? 'checked ': '';
$apply_nonfp_no = $apply_nonfp == 0 ? 'checked ': '';

$static_limit_yes = $static_limit == 1 ? 'checked ': '';
$static_limit_no = $static_limit == 0 ? 'checked ': '';

$nonfp_limit_yes = $nonfp_limit == 1 ? 'checked ': '';
$nonfp_limit_no = $nonfp_limit == 0 ? 'checked ': '';

$kill_title_yes = $kill_title == 1 ? 'checked ': '';
$kill_title_no = $kill_title == 0 ? 'checked ': '';

$letter_sel = $limitpostby == "letter" ? 'selected' : '';
$word_sel = $limitpostby == "word" ? 'selected' : '';
$para_sel = $limitpostby == "paragraph" ? 'selected' : '';
$none_sel = $limitpostby == "none" ? 'selected' : '';

switch($limitpostby) {
 case "paragraph":
   $postnum_blurb = "<strong># paragraphs before cutoff</strong> (default <em>1</em>)";;
   break;
 case "letter":
   $postnum_blurb = "<strong># characters before cutoff</strong> (default <em>600</em>)";;
   break;
 case "word":
   $postnum_blurb = "<strong># words before cutoff</strong> (default <em>200</em>)";;
   break;
 case "none":
 default:
   $input_number = '';
   $input_linktext = '';
   $input_ending = '';
   $postnum_blurb = "<strong># before cutoff</strong> (default <em>1</em>)";;   
   break;
}

$option_display = $limitpostby == 'none' ? 'style="display:none;"' : '';
?>
	
<br />
<form method="post" name="options" target="_self">

<table>
<tr>
<td width="160" valign="top">
<strong>Select categories</strong><br />
<a href="javascript:toggle_boxes('fpm_post_category', this);">toggle all</a>
</td>

<td>
<!--<select name="fpm_post_category">
  <option value="all">all</option>-->
<?php
foreach ($cats as $cat) {
  $id = $cat->term_id;
  $name = $cat->name;
  //$cat_sel = $id == $postcat ? 'selected' : '';
  //echo "<option value=$id $cat_sel>$name</option>\r\n";
  $cat_sel = in_array($id, $postcats) ? 'checked' : '';
  echo "<input type=checkbox name=fpm_post_category[] value=$id $cat_sel />$name<br />\r\n";
}
?>
<!--</select>-->
</td>
</tr>

<tr>
<td><strong>Max posts to display</strong></td>
<td><input name="fpm_post_numposts" type="text" size="3" value="<?php echo $input_numposts; ?>" /> (default <em>1</em>)</td>
</tr>

<tr>
<td><strong>Tags to strip </strong></td>
<td><input type="text" size="30" name="fpm_striptags" value="<?php echo $striptags; ?>" /> 
(comma-separated, e.g. <em>img, div, hr</em>. Use <em>all</em> to remove all HTML)</td>
</tr>

<tr>
<td><strong>Apply to non-frontpage posts page? </strong></td>
<td>
<input type="radio" name="fpm_apply_nonfp" value="1" <?php echo $apply_nonfp_yes; ?>/> yes 
<input type="radio" name="fpm_apply_nonfp" value="0" <?php echo $apply_nonfp_no; ?>/> no 
(Should post selection happen to the main posts page when it isn&rsquo;t your front page?)
</td>
</tr>

<tr>
<td><strong>Hide category in page title? </strong></td>
<td>
<input type="radio" name="fpm_kill_title" value="1" <?php echo $kill_title_yes; ?>/> yes 
<input type="radio" name="fpm_kill_title" value="0" <?php echo $kill_title_no; ?>/> no 
</td>
</tr>

</table>

<br />
<h3>Limitation Options</h3>

<table>
<tr>
<td><strong>Limit post(s) by</strong></td> 

<td>
<select name="fpm_post_cuttype" onchange="change_num(this);">
  <option value="none" <?php echo $none_sel; ?>>Do not limit</option>
  <option value="paragraph" <?php echo $para_sel; ?>>Number of paragraphs</option>
  <option value="letter" <?php echo $letter_sel; ?>>Number of characters</option>
  <option value="word" <?php echo $word_sel; ?>>Number of words</option>
  </select>
</td>
</tr>

</table>

<table id="truncate" <?php echo $option_display; ?>>
<tr>
<td width="160" valign="top"><strong>Limit details</strong></td>

<td>
<input name="fpm_post_number" type="text" value="<?php echo $input_number; ?>" /> 
  <span id="fpm_num"><?php echo $postnum_blurb; ?></span><br />

  <input name="fpm_post_linktext" type="text" value="<?php echo $input_linktext; ?>" /> 
  <strong>Read more linktext</strong> <br />
  
  <input name="fpm_post_ending" type="text" value="<?php echo $input_ending; ?>" /> 
  <strong>Text ending</strong> (for word/character limit only)<br />
</td>
</tr>
</table>

<table>

<tr>
<td><strong>Apply limit to static front page? </strong></td>
<td>
<input type="radio" name="fpm_static_limit" value="1" <?php echo $static_limit_yes; ?>/> yes 
<input type="radio" name="fpm_static_limit" value="0" <?php echo $static_limit_no; ?>/> no 
(Should content limitation occur when a page is your frontpage, or not?)
</td>
</tr>

<tr>
<td><strong>Apply limit to non frontpage posts page? </strong></td>
<td>
<input type="radio" name="fpm_nonfp_limit" value="1" <?php echo $nonfp_limit_yes; ?>/> yes 
<input type="radio" name="fpm_nonfp_limit" value="0" <?php echo $nonfp_limit_no; ?>/> no 
(Should content limitation be done to the main post page, when it isn&rsquo;t your frontpage?)
</td>
</tr>
</table>

  <p class="submit">
  <input name="fpm_submit" type="hidden" value="true" />
  <input type="submit" name="Submit" class="button-primary" value="Update Options &raquo;" />
  </p>
  </form>

  </div>
