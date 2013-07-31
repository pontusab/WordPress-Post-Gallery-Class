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

	public function __construct( $post_type, $_key = '_gallery' )
	{
		$this->post_type = $post_type;
		$this->_key      = $_key;

		add_action( 'add_meta_boxes', array( &$this, 'add_meta_boxes' ) );
		add_action( 'admin_enqueue_scripts', array( &$this, 'enqueue_scripts') );
		add_action( 'admin_footer', array( &$this, 'upload_script' ) );
		add_action( 'wp_ajax_gallery_upload', array( &$this, 'handle_upload' ) );
		add_action( 'wp_ajax_remove_attachment', array( &$this, 'remove_attacment' ) );
		add_action( 'wp_ajax_add_attachment', array( &$this, 'add_attacment' ) );
	}


	public static function upload_script()
	{
		global $post;

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
			    'action'      		  => 'gallery_upload',            // the ajax action name,
			    'post_id'			  => $post->ID
		    ),
		);
		 
		return $plupload;		
	}


	public function enqueue_scripts()
	{
		wp_enqueue_script( 'plupload-all' );
		wp_register_style( 'gallery-css', plugins_url( '/assets/css/gallery.css', __FILE__ ) );
		wp_register_script( 'gallery-js', plugins_url( '/assets/js/gallery.js', __FILE__ ) );

		wp_enqueue_script( 'jquery-ui-draggable' );
		wp_enqueue_script( 'jquery-ui-sortable' );
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
		add_meta_box( 'gallery', __( 'Gallery' ), array( &$this, 'meta_data' ), $this->post_type, 'normal', 'low' );
	}


	public function meta_data()
	{
		global $post;
		$attachments = get_post_meta( $post->ID, $this->_key, true ); 
		
		$upploaded   = get_posts( array(
			'post_type'		 => 'attachment',
			'posts_per_page' => -1,
			'exclude'		 => $attachments
		));

		if( !$attachments )
		{
			$attachments = array();
		}

		//ksort( $attachments );
		ksort( $attachments );

		$output = '<div id="plupload-upload-ui" class="hide-if-no-js clearfix">';

			$output .= '<div class="header">';
				$output .= '<div class="media-router">';
					$output .= '<a href="#" data-click="1" class="media-menu-item '.( empty( $attachments ) ? 'active' : null ).'">'. __('Upload Files') .'</a>';
					$output .= '<a href="#" data-click="2" class="media-menu-item '.( !empty( $attachments ) ? 'active' : null ).'">'. __('Attached Images') .'</a>';

					$output .= '<a href="#" data-click="3" class="media-menu-item">'. __('Media Library') .'</a>';
				$output .= '</div>';
			$output .= '</div>';

			$output .= '<div id="drag-drop-area">';

				// Start Upload

	     		$output .= '<div data-panel="1" class="panel upload-wrap '.( empty( $attachments ) ? 'active' : null ).'">';

	       			$output .= '<div class="drag-drop-inside">';
	        			$output .= '<h3>'. __( 'Drop files anywhere to upload' ) .'</h3>';
	        			$output .= '<p class="drag-drop-buttons">';
	        			$output .= '<input id="plupload-browse-button" type="button" value="'. __('Select Files') .'" class="browser button button-hero" />';
	        			$output .= '</p>';
	      			$output .= '</div>';

	     		$output .= '</div>';

	     		// End Upload

	     		// Start Delete

	     		$output .= '<div data-panel="2" class="panel scrollable delete-wrap '.( !empty( $attachments ) ? 'active' : null ).'">';
	     			
	     			$output .= '<ul>';

	     				foreach( $attachments as $key => $value )
	     				{
		     				$output .= '<li data-id="'. $value .'">';
		     					$output .= wp_get_attachment_image( $value, array( 120, 120 ) );
		     				$output .= '</li>';
		     			}

	     			$output .= '</ul>';

	     			$output .= '<div class="delete">';
			  			$output .= '<a class="button button-primary button-large" href="#">'. __('Delete Image') .'</a>';
			  		$output .= '</div>';

	     		$output .= '</div>';

	     		// End Delete

	     		// Start add

	     		$output .= '<div data-panel="3" class="panel scrollable add-wrap">';
	     			$output .= '<ul>';
	     				
		     			foreach( $upploaded as $image )
		     			{
		     				$output .= '<li data-id="'. $image->ID .'">';
		     					$output .= wp_get_attachment_image( $image->ID, array( 120, 120 ) );
		     				$output .= '</li>';
		     			}

		     		$output .= '</ul>';

		     		$output .= '<div class="add">';
			  			$output .= '<a class="button button-primary button-large" href="#">'. __('Attatch Image(s)') .'</a>';
			  		$output .= '</div>';
	       			
	     		$output .= '</div>';

	     		// End add

	  		$output .= '</div>';

	  	$output .= '</div>';

  		echo $output;
	}


	public function remove_attacment()
	{
		$post_id 	 = $_REQUEST['post_id'];
		$attachments = isset( $_POST['ids'] ) ? $_POST['ids'] : null;
		$saved   	 = get_post_meta( $post_id, $this->_key, true ); 

		if( count( $attachments ) )
		{
			$attachments = explode( ',', $attachments );

			foreach( $attachments as $attachment )
			{
				unset( $saved[ array_search( $attachment, $saved ) ] );

				//wp_delete_attachment( $attachment, true );
			}

			if( count( $saved ) > 0 )
			{
				update_post_meta( $post_id, $this->_key, $saved );
			}
			else
			{
				delete_post_meta( $post_id, $this->_key );
			}
		}

		exit;
	}

	static public function images( $size, $li = true )
	{
		global $post;

		$attachments = get_post_meta( $post->ID, '_gallery', true ); 
		if( !$attachments )
		{
			$attachments = array();
		}

		ksort( $attachments );

	
		$output = '';

		if( $li )
		{
			foreach( $attachments as $attachment )
			{
				$output .= '<li>';
					$output .= wp_get_attachment_image( $attachment, $size );
				$output .= '</li>';
			}
		}

		return $output;
	}

	public function add_attacment()
	{
		$post_id 	 = $_REQUEST['post_id'];
		$attachments = isset( $_POST['ids'] ) ? $_POST['ids'] : null;
		$saved   	 = get_post_meta( $post_id, $this->_key, true ); 

		if( count( $attachments ) )
		{
			$attachments = explode( ',', $attachments );

			foreach( $attachments as $attachment )
			{
				$saved[microtime()] = $attachment;
			}

			update_post_meta( $post_id, $this->_key, $saved );

		}

		exit;
	}


	public function handle_upload()
	{
		$post_id = $_REQUEST['post_id'];
		$saved   = get_post_meta( $post_id, $this->_key, true ); 
		$file 	 = $_FILES['async-upload'];

		$file_attr  = wp_handle_upload( $file, array(
			'test_form' => true,
			'action'    => 'gallery_upload'
		));

		$attachment = array(
			'post_mime_type' => $file_attr['type'],
			'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $file['name'] ) ),
			'post_content'   => '',
			'post_status'    => 'inherit'
		);

		// Adds file as attachment to WordPress
		$id = wp_insert_attachment( $attachment, $file_attr['file'], $post_id );
		
		if( !is_wp_error( $id ) )
		{
			wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $file_attr['file'] ) );

			$saved[microtime()] = $id;
			update_post_meta( $post_id, $this->_key, $saved );

			echo $file_attr['url'];
		}
	
		exit;
	}
}