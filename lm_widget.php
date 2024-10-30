<?php

class lm_widget extends WP_Widget {
    
    public function __construct() {
        parent::__construct (
            'lm_widget',
            'LM Review',
            array ('description' => 'LM Review Widget', )
        );
        add_action('widgets_init', array($this, 'register_lm_review_widget'));

    }
    
	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
    public function form( $instance ) {
		if ( isset( $instance[ 'number_reviews' ] ) ) {
			$number_reviews = $instance[ 'number_reviews' ];
		}
		else {
			$number_reviews =  '5';
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'number_reviews' ); ?>"><?php _e( 'Number of reviews:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'number_reviews' ); ?>" name="<?php echo $this->get_field_name( 'number_reviews' ); ?>" type="text" value="<?php echo esc_attr( $number_reviews ); ?>">
		</p>
		<?php 
	}
        
    public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['number_reviews'] = ( ! empty( $new_instance['number_reviews'] ) ) ? strip_tags( $new_instance['number_reviews'] ) : '';

		return $instance;
	}
    

    /**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
            
        global $wpdb;
        $alertbox = '<div class="alert alert-success">Thanks for placing a review.</div>';
        
        if ($_POST['submit']) {
             echo $alertbox;
         }
         
         $lm_reviews_number = $instance['number_reviews'];
         
        echo '<div class="outputcenter">';
         foreach($wpdb->get_results("SELECT * FROM {$wpdb->prefix}postmeta INNER JOIN {$wpdb->prefix}posts on ID=post_id WHERE meta_key = 'lm_review' AND post_type = 'lm_review' AND post_status = 'publish' ORDER BY post_id DESC LIMIT $lm_reviews_number") as $key => $row) {
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
            
            echo 'Rating: '.$reviews['rating'].'<br/>';
            echo '<strong>Name:</strong> '.$reviews['author'].'<br/>';
            echo '<strong>Date:</strong> '.date('d-m-Y', strtotime($row->post_date)).'<br/><br/>';
            echo '<strong>Message:</strong> <br> '.$row->post_content.'<br/><br/>';
            
            
            echo '</div>';
        }
        echo '</div>';
        ?>
        
        <?php  
        
        if($_POST['submit']) {

        $post = array (
            'post_type'     => 'lm_review',
            'post_status'   => 'publish',
            'post_title'    => $_POST['lm_reviews']['author'],
            'post_content'  =>  $_POST['lm_reviews']['comment'],
        );
            
        wp_insert_post( $post );
        add_post_meta($post_id, 'lm_review', $_POST['lm_reviews']);
        }
    }	
    
    public function register_lm_review_widget() {
        register_widget( 'lm_widget' );
    }
    
}



$lm_widget = new lm_widget();
require_once 'lm_review.php';

?>
