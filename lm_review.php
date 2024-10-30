<?php
/**
 * Plugin Name: LM Reviews
 * Plugin URI: http://www.logomind.nl
 * Description: Let customers place a nice review on your website!
 * Version: 1.0
 * Author: Buddy Jansen
 * Author URI: http://www.logomind.nl
 * License: GPLv2 or later
 */

require_once 'lm_widget.php';
require_once 'lm_options.php';

 class lm_reviews {
    
    public function __construct() {
        
        add_action ( 'init', array($this, 'lm_post_type' ) );
        add_action ( 'add_meta_boxes', array($this, 'lm_meta_box' ) );
        add_action ( 'save_post', array($this, 'save_lm_review' ) );
        add_action ( 'wp_enqueue_scripts', array($this, 'register_stylesheet' ) );
        add_action ( 'admin_menu', array($this, 'add_lm_options_page'));
        add_shortcode ( 'lm_reviews', array($this, 'lm_frontpage_shortcode' ) );
        
    }       
    
    /*/
     * Adds the metabox
     */
    public function lm_meta_box() {
        add_meta_box( 'lm_meta_box', 'LM Meta Box', array($this, 'lm_meta_box_function'), 'lm_review', 'side', 'high' );
    }
    
    /*/
     * Metabox function
     * Backend metabox
     */
    public function lm_meta_box_function( $post_id ) {
        
        $lm_reviews_data = get_post_meta(get_the_ID(), 'lm_review', true);
        ?>
        
        <strong><?php _e('Rating', 'lm_review'); ?>:</strong><br/><input type="number" min="1" max="5" name="lm_reviews[rating]" value="<?php echo $lm_reviews_data['rating']; ?>"><br/>
        <strong>Author:</strong><br/><input type="text" name="lm_reviews[author]" value="<?php echo $lm_reviews_data['author'] ?>"><br/>
        
        <?php
        
    }
    
    public function lm_frontpage_shortcode( $post_id ) {
        global $wpdb;
        $alertbox = '<div class="alert alert-success">Thanks for placing a review.</div>';
        
        if ($_POST['submit']) {
          echo $alertbox;
        }

        echo '<div class="outputcenter">';
        foreach($wpdb->get_results("SELECT * FROM {$wpdb->prefix}postmeta INNER JOIN wp_posts on ID=post_id WHERE meta_key = 'lm_review' AND post_type = 'lm_review' AND post_status = 'publish' ORDER BY post_id DESC") as $key => $row) {
            $reviews = unserialize($row->meta_value);
            
            echo '<div class="lm_review">';
            
            if ($reviews['rating'] == 5) {
               $reviews['rating'] = '<img src="'.plugin_dir_url(__FILE__).'assets/star5.png" class="stars">';
            }
            if ($reviews['rating'] == 4) {
               $reviews['rating'] = '<img src="'.plugin_dir_url(__FILE__).'assets/star4.png" class="stars">';
            }
            if ($reviews['rating'] == 3) {
               $reviews['rating'] = '<img src="'.plugin_dir_url(__FILE__).'assets/star3.png" class="stars">';
            }
            if ($reviews['rating'] == 2) {
               $reviews['rating'] = '<img src="'.plugin_dir_url(__FILE__).'assets/star2.png" class="stars">';
            }
            if ($reviews['rating'] == 1) {
               $reviews['rating'] = '<img src="'.plugin_dir_url(__FILE__).'assets/star1.png" class="stars">';
            }
            
            echo '<div class="lm_leftbox">Rating: '.$reviews['rating'].'<br/>';
            echo '<strong>Name:</strong> '.$reviews['author'].'<br/>';
            echo '<strong>Date:</strong> '.date('d-m-Y', strtotime($row->post_date)).'</div><br/>';
            echo '<div class="lm_message"><strong>Message:</strong> '.$row->post_content.'</div><br/><br/>';
            
            
            echo '</div>';
        }
        echo '</div>';
        ?>
        
        <div class="lm_reviews">
            <form action="<?php $_SERVER['PHP_SELF']; ?>" method="POST">

                <label>Rating:</label><br/>
            <span class="rating">
            <input type="radio" class="rating-input"
                id="rating-input-5" name="lm_reviews[rating]" checked value="5">
            <label for="rating-input-5" class="rating-star"></label>
            <input type="radio" class="rating-input"
                id="rating-input-4" name="lm_reviews[rating]" value="4">
            <label for="rating-input-4" class="rating-star"></label>
            <input type="radio" class="rating-input"
                id="rating-input-3" name="lm_reviews[rating]" value="3">
            <label for="rating-input-3" class="rating-star"></label>
            <input type="radio" class="rating-input"
                id="rating-input-2" name="lm_reviews[rating]" value="2">
            <label for="rating-input-2" class="rating-star"></label>
            <input type="radio" class="rating-input"
                id="rating-input-1" name="lm_reviews[rating]" value="1">
            <label for="rating-input-1" class="rating-star"></label>
        </span><br/>

                <label>Name:</label><br/>
                <input type="text" name="lm_reviews[author]"><br/>

                <label>Message:</label><br/>
                <textarea cols="30" rows="10" name="lm_reviews[comment]"></textarea><br/>
                <input type="submit" name="submit" class="lm_button">
            </form>
        </div>

        <?php  
        
       $results = $wpdb->get_row( "SELECT option_value FROM wp_options WHERE option_name = 'lm_plugin_options' " );
       $results = unserialize($results->option_value);
       
       if($results['lm_concept'] == 'on') {
           $publish = 'pending';
       } else {
           $publish = 'publish';
       }
        
        if($_POST['submit']) {
        $post = array (
            'post_type'     => 'lm_review',
            'post_status'   => $publish,
            'post_title'    => $_POST['lm_reviews']['author'],
            'post_content'  =>  $_POST['lm_reviews']['comment'],
        );
            
        wp_insert_post( $post );
        add_post_meta($post_id, 'lm_review', $_POST['lm_reviews']);
        }
    }
    
    public function save_lm_review( $post_id ) {
        update_post_meta($post_id, 'lm_review', $_POST['lm_reviews']);
    }
    
    /*/
     * Register custom post type
     */
    public function lm_post_type() {
        $labels = array (
            'name'                  => 'LM Reviews',
            'singular_name'         => 'lm_review',
            'edit_item'             => 'Edit review',
            'add_new'               => 'Add new Review',
            'add_new_item'          => 'Add new Review',
            'all_items'             => 'All reviews',
            'view_item'             => 'View review',
            'search_items'          => 'Search reviews' ,
            'not_found'             => 'No reviews found' ,
            'not_found_in_trash'    => 'No reviews found in the Trash' , 
        );
        $args = array (
            'labels'        => $labels,
            'description'   => 'Review',
            'public'        => true,
            'menu_position' => 30,
            'supports'      => array( 'title', 'editor'),
            'has_archive'   => true,
        );
        register_post_type( 'lm_review' , $args );
    }
    
    public function register_stylesheet() {
        wp_register_style( 'lm_reviews', plugins_url( 'lm_reviews/assets/css/style.css' ) );
        wp_enqueue_style( 'lm_reviews' );
         wp_register_style( 'bootstrap', plugins_url( 'lm_reviews/assets/css/bootstrap.css' ) );
	wp_enqueue_style( 'bootstrap' );
        wp_enqueue_script( 'stars.js',  plugins_url( 'lm_reviews/assets/js/stars.js', array(), '1.0.0', true ) );
    }
    
}

 $lm_reviews = new lm_reviews();

 ?>