<?php
/**
 * WP-JoomSport
 * @author      BearDev
 * @package     JoomSport
 */
class JoomSportMetaPerson {
    public static function output( $post ) {
        global $post, $thepostid, $wp_meta_boxes;
        
        
        $thepostid = $post->ID;

        wp_nonce_field( 'joomsport_person_savemetaboxes', 'joomsport_person_nonce' );
        ?>
        <div id="joomsportContainerBE">
            
            <div>
                <div>
                    <div>
                        <?php
                        do_meta_boxes(get_current_screen(), 'joomsportintab_person1', $post);
                        unset($wp_meta_boxes[get_post_type($post)]['joomsportintab_person1']);
                        ?>

                    </div>    
                </div>
            </div>   
            
        </div>
        <?php
    }
        
        
    public static function js_meta_personal($post){

        $metadata = get_post_meta($post->ID,'_joomsport_person_personal',true);

        ?>
        <div class="jsminwdhtd jstable">
            <div class="jstable-row">
                <div class="jstable-cell">
                    <?php echo __('First name', 'joomsport-sports-league-results-management');?>
                </div>
                <div class="jstable-cell">
                    <input type="text" name="personal[first_name]" value="<?php echo isset($metadata['first_name'])?esc_attr($metadata['first_name']):""?>" />
                </div>
            </div>
            <div class="jstable-row">
                <div class="jstable-cell">
                    <?php echo __('Last name', 'joomsport-sports-league-results-management');?>
                </div>
                <div class="jstable-cell">
                    <input type="text" name="personal[last_name]" value="<?php echo isset($metadata['last_name'])?esc_attr($metadata['last_name']):""?>" />
                </div>
            </div>
        </div>
        <?php
    }
    public static function js_meta_about($post){

        $metadata = get_post_meta($post->ID,'_joomsport_person_about',true);
        echo wp_editor($metadata, 'about',array("textarea_rows"=>3));


    }

    public static function js_meta_ef($post){

        $metadata = get_post_meta($post->ID,'_joomsport_person_ef',true);
        
        $efields = JoomSportHelperEF::getEFList('6', 0);

        if(count($efields)){
            echo '<div class="jsminwdhtd jstable">';
            foreach ($efields as $ef) {

                JoomSportHelperEF::getEFInput($ef, isset($metadata[$ef->id])?$metadata[$ef->id]:null);
                //var_dump($ef);
                ?>
                
                <div class="jstable-row">
                    <div class="jstable-cell"><?php echo $ef->name?></div>
                    <div class="jstable-cell">
                        <?php 
                        if($ef->field_type == '2'){
                            wp_editor(isset($metadata[$ef->id])?$metadata[$ef->id]:'', 'ef_'.$ef->id,array("textarea_rows"=>3));
                            echo '<input type="hidden" name="ef['.$ef->id.']" value="ef_'.$ef->id.'" />';
                        }else{
                            echo $ef->edit;
                        }
                        ?>
                    </div>    
                        
                </div>    
                <?php
            }
            echo '</div>';
        }else{
            $link = get_admin_url(get_current_blog_id(), 'admin.php?page=joomsport-page-extrafields');
             printf( __( 'There are no extra fields assigned to this section. Create new one on %s Extra fields list %s', 'joomsport-sports-league-results-management' ), '<a href="'.$link.'">','</a>' );

        }
        
    }

    
    public static function joomsport_person_save_metabox($post_id, $post){
        // Add nonce for security and authentication.
        $nonce_name   = isset( $_POST['joomsport_person_nonce'] ) ? $_POST['joomsport_person_nonce'] : '';
        $nonce_action = 'joomsport_person_savemetaboxes';
 
        // Check if nonce is set.
        if ( ! isset( $nonce_name ) ) {
            return;
        }
 
        // Check if nonce is valid.
        if ( ! wp_verify_nonce( $nonce_name, $nonce_action ) ) {
            return;
        }
 
        // Check if user has permissions to save data.
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
 
        // Check if not an autosave.
        if ( wp_is_post_autosave( $post_id ) ) {
            return;
        }
 
        // Check if not a revision.
        if ( wp_is_post_revision( $post_id ) ) {
            return;
        }
        
        if('joomsport_person' == $_POST['post_type'] ){
            self::saveMetaPersonal($post_id);
            self::saveMetaAbout($post_id);
            self::saveMetaEF($post_id);
            
        }
    }
    
    private static function saveMetaPersonal($post_id){
        $meta_array = array();
        $meta_array = isset($_POST['personal'])?  ($_POST['personal']):'';
        $meta_array = array_map( 'sanitize_text_field', wp_unslash( $_POST['personal'] ) );
        update_post_meta($post_id, '_joomsport_person_personal', $meta_array);
    }
    private static function saveMetaAbout($post_id){
        $meta_data = isset($_POST['about'])?  wp_kses_post($_POST['about']):'';
        update_post_meta($post_id, '_joomsport_person_about', $meta_data);
    }
    private static function saveMetaEF($post_id){
        $meta_array = array();
        if(isset($_POST['ef']) && count($_POST['ef'])){
            foreach ($_POST['ef'] as $key => $value){
                if(isset($_POST['ef_'.$key])){
                    $meta_array[$key] = sanitize_text_field($_POST['ef_'.$key]);
                }else{
                    $meta_array[$key] = sanitize_text_field($value);
                }
            }
        }
        //$meta_data = serialize($meta_array);
        update_post_meta($post_id, '_joomsport_person_ef', $meta_array);
    }
    
}
