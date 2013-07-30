<?php
/*
Plugin Name: WordPress-Post-Gallery-Class
Plugin URI: https://github.com/pontusab/WordPress-Post-Gallery-Class
Version: 0.1
Author: Pontus Abrahamsson
*/
 

class WPgallery
{
	public $post_type;
	public $_key;

	public function __construct( $post_type, $_key = '_wpgallery' )
	{
		$this->post_type = $post_type;
		$this->_key      = $_key;

		add_action( 'add_meta_boxes', array( &$this, 'add_meta_boxes' ) );
		add_action( 'admin_enqueue_scripts', array( &$this, 'enqueue_scripts') );
		add_action( 'admin_footer', array( &$this, 'upload_script' ) );
		add_action( 'wp_ajax_photo_gallery_upload', array( &$this, 'handle_upload' ) );
	}


	public static function upload_script()
	{
		$plupload = array(
		    'runtimes'            => 'html5,silverlight,flash,html4',
		    'browse_button'       => 'plupload-browse-button',
		    'container'           => 'plupload-upload-ui',
		    'drop_element'        => 'drag-drop-area',
		    'file_data_name'      => 'async-upload',            
		    'multiple_queues'     => true,
		    'max_file_size'       => wp_max_upload_size() . 'b',
		    'url'                 => admin_url( 'admin-ajax.php' ),
		    'flash_swf_url'       => includes_url( 'js/plupload/plupload.flash.swf' ),
		    'silverlight_xap_url' => includes_url( 'js/plupload/plupload.silverlight.xap' ),
		    'filters'             => array( array( 'title' => __( 'Allowed Files' ), 'extensions' => '*' ) ),
		    'multipart'           => true,
		    'urlstream_upload'    => true,
		 
		    // additional post data to send to our ajax hook
		    'multipart_params'    => array(
		    '_ajax_nonce' 		  => wp_create_nonce( 'photo-upload' ),
		    'action'      		  => 'photo_gallery_upload',            // the ajax action name
		    ),
		);
		 
		return $plupload;		
	}


	public function enqueue_scripts()
	{
		wp_enqueue_script( 'plupload-all' );
		wp_register_style( 'gallery-css', plugins_url( '/assets/css/gallery.css', __FILE__ ) );
		wp_register_script( 'gallery-js', plugins_url( '/assets/js/gallery.js', __FILE__ ) );

        wp_enqueue_style( 'gallery-css' );
        wp_enqueue_script( 'gallery-js' );
        ?>

        <script type="text/javascript">
			var gallery_settings = <?php echo json_encode( self::upload_script() ); ?>;
		</script>

        <?php
	}


	public function add_meta_boxes()
	{
		if( is_array( $this->post_type ) ) 
		{
			foreach( $this->post_type as $type ) 
			{
				add_meta_box( 'gallery', __( 'Gallery' ), array( &$this, 'meta_data' ), $type, 'normal', 'high' );
			}
		}
		else
		{
			add_meta_box( 'gallery', __( 'Gallery' ), array( &$this, 'meta_data' ), $this->post_type, 'normal', 'high' );
		}
	}


	public function meta_data()
	{
		$output = '<div id="plupload-upload-ui" class="hide-if-no-js">';

			$output .= '<div class="header">';
				$output .= '<div class="media-router">';
					$output .= '<a href="#" data-click="1" class="media-menu-item active">Upload Files</a>';
					$output .= '<a href="#" data-click="2" class="media-menu-item">Attached Images</a>';
				$output .= '</div>';
			$output .= '</div>';

			$output .= '<div id="drag-drop-area">';
	     		$output .= '<div data-panel="1" class="panel active">';
	       			$output .= '<div class="drag-drop-inside">';
	        			$output .= '<h3>'. __( 'Drop files anywhere to upload' ) .'</h3>';
	        			$output .= '<p class="drag-drop-buttons">';
	        			$output .= '<input id="plupload-browse-button" type="button" value="'. __('Select Files') .'" class="browser button button-hero" />';
	        			$output .= '</p>';
	      			$output .= '</div>';
	     		$output .= '</div>';

	     		$output .= '<div data-panel="2" class="panel">';
	     			$output .= 'wefwf';
	     		$output .= '</div>';

	     		$output .= '<div class="overlay"></div>';
	  		$output .= '</div>';
	  	$output .= '</div>';

  		echo $output;
	}


	public function handle_upload()
	{
		check_ajax_referer( 'photo-upload' );
		
		$status = wp_handle_upload( $_FILES['async-upload'], array( 
		    'test_form' => true, 
		    'action' 	=> 'photo_gallery_upload' 
		)); 
		
		exit;
	}
}