jQuery(document).ready(function() {
  jQuery('div.wp-menu-name').each(function() {
    if (jQuery(this).text() == "JoomSport") {
      // group
      jQuery('#adminmenu .wp-submenu li > a[href*="=joomsport"]').parent().addClass("group");
      // group-1
      jQuery('#adminmenu .wp-submenu li > a[href$="=joomsport_season"]').parent().addClass("group-1");
      jQuery('#adminmenu .wp-submenu li > a[href$="=joomsport_match"]').parent().addClass("group-1");
      // group-2
      jQuery('#adminmenu .wp-submenu li > a[href$="=joomsport_team"]').parent().addClass("group-2");
      jQuery('#adminmenu .wp-submenu li > a[href$="=joomsport_player"]').parent().addClass("group-2");
      jQuery('#adminmenu .wp-submenu li > a[href$="=joomsport_venue"]').parent().addClass("group-2");
      jQuery('#adminmenu .wp-submenu li > a[href$="edit.php?post_type=joomsport_person"]').parent().addClass("group-2");
      jQuery('#adminmenu .wp-submenu li > a[href$="=joomsport_import"]').parent().addClass("group-2");
          // group-3
      jQuery('#adminmenu .wp-submenu li > a[href$="=joomsport-page-events"]').parent().addClass("group-3");
      jQuery('#adminmenu .wp-submenu li > a[href$="=joomsport-page-boxfields"]').parent().addClass("group-3");
      jQuery('#adminmenu .wp-submenu li > a[href$="=joomsport_personcategory&post_type=joomsport_person"]').parent().addClass("group-3");
      
      jQuery('#adminmenu .wp-submenu li > a[href$="=joomsport-page-gamestages"]').parent().addClass("group-3");
      jQuery('#adminmenu .wp-submenu li > a[href$="=joomsport-page-extrafields"]').parent().addClass("group-3");
      jQuery('#adminmenu .wp-submenu li > a[href$="=joomsport_settings"]').parent().addClass("group-3");
      jQuery('#adminmenu .wp-submenu li > a[href$="=joomsport_help"]').parent().addClass("group-3");

      jQuery("li.group-1:first").before('<fieldset class="item item-1"><legend>Manage</legend></fieldset>');
      jQuery("li.group-2:first").before('<fieldset class="item item-2"><legend>Enter data</legend></fieldset>');
      jQuery("li.group-3:first").before('<fieldset class="item item-3"><legend>Configure</legend></fieldset>');
    }
  });
});