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
		add_action( 'admin_init', array( &$this, 'enqueue_scripts') );
	}


	public function enqueue_scripts()
	{
		wp_register_style( 'gallery-css', plugins_url( '/assets/css/gallery.css', __FILE__ ) );
        wp_register_script( 'gallery-widget', plugins_url( '/assets/js/jquery.flot.js', __FILE__ ), array('jquery') );

        wp_enqueue_style( 'gallery-css' );

        //wp_enqueue_script( 'gallery-widget' );
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
		echo '<h3>Drop files anywhere to upload</h3>';
		echo '<a href="#" class="browser button button-hero">Select Files</a>';
	}

}