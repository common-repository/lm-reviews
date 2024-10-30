<?php

class lm_options {
    
    public $options;
    
    public function __construct() {
        $this->options = get_option('lm_plugin_options');
        add_action ( 'admin_menu', array($this, 'add_lm_options_page'));
        add_action ( 'admin_init', array($this, 'register_fields_and_settings') );
        
    }
    
    /*
     * Add's options page
     */
    public function add_lm_options_page() {
        add_submenu_page('edit.php?post_type=lm_review', 'LM Options' , 'LM Options', 'manage_options', __FILE__, array( $this, 'plugin_options_page' ) );
    }
    
    public function plugin_options_page() {
        ?>
        
        <h1>LM Review Options Page</h1>
   
        <form action="options.php" method="post" enctype="multipart/form-data">
        <?php settings_fields('lm_plugin_options'); ?>
        <?php do_settings_sections(__FILE__); ?>
            
            <input name="submit" type="submit" class="button-primary" value="Save options">
        </form>
        
        <?php
    }
    
    public function register_fields_and_settings() {
        register_setting('lm_plugin_options', 'lm_plugin_options');
        add_settings_section('lm_main_section', 'Main Settings', array($this, 'lm_main_section_cb'), __FILE__, '');
        add_settings_field('lm_concept', 'Accept Review first', array($this, 'lm_concept_setting'), __FILE__, 'lm_main_section');
        
    }
    
    public function lm_concept_setting() {
        $check = $this->options['lm_concept'];
        
        if ($check == 'on') {
            $checked = 'checked';
        } else {
            $checked = '';
        }
        
        echo '<input type="checkbox" name="lm_plugin_options[lm_concept]" '.$checked.'> ';

    }
  
}

$lm_options = new lm_options();

?>
