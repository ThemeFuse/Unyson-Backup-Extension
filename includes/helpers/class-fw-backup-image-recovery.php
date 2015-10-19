<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

class FW_Backup_Image_Recovery {

	public static $attachment_id;

	public static $common_sizes = array();

	public function __construct() {

		require_once( ABSPATH . 'wp-admin/includes/image.php' );

	}

	public function filter_callback( $attachment ) {
		return preg_match( '/image/', $attachment->post_mime_type );
	}

	public function get_attachment_images() {
		$args = array(
			'post_type'   => 'attachment',
			'numberposts' => - 1,
			'post_status' => null,
		);

		$attachments = get_posts( $args );

		return array_filter( $attachments, array( $this, 'filter_callback' ) );
	}

	public function remove_attachment_images() {

		$attachments         = $this->get_attachment_images();
		$intermediate_images = array();
		if ( ! empty( $attachments ) ) {
			foreach ( $attachments as $attachment ) {

				$intermediate_images = array_merge( $intermediate_images, $this->remove_intermediate_images( $attachment->ID ) );
			}
		}

		return $intermediate_images;
	}

	public function generate_attachment_images() {
		$attachments = $this->get_attachment_images();

		add_filter( 'intermediate_image_sizes_advanced', array(
			$this,
			'callback_filter_intermediate_image_sizes_advanced'
		) );
		add_filter( 'wp_generate_attachment_metadata', array( $this, 'callback_filter_generate_attachment_metadata' ) );

		if ( ! empty( $attachments ) ) {
			foreach ( $attachments as $attachment ) {
				$this->generate_intermediate_images( $attachment );
			}
		}

		remove_filter( 'intermediate_image_sizes_advanced', array(
			$this,
			'callback_filter_intermediate_image_sizes_advanced'
		) );
		remove_filter( 'wp_generate_attachment_metadata', array(
			$this,
			'callback_filter_generate_attachment_metadata'
		) );

	}

	public function generate_intermediate_images( $attachment ) {
		$attachment_id                                 = $attachment->ID;
		$file                                          = get_attached_file( $attachment_id );
		FW_Backup_Image_Recovery::$attachment_id = $attachment_id;

		if ( file_exists( $file ) ) {
			$metadata = wp_generate_attachment_metadata( $attachment_id, $file );
			wp_update_attachment_metadata( $attachment_id, $metadata );
		}
	}

	public function callback_filter_generate_attachment_metadata( $metadata ) {
		if(!empty($tmp['sizes'])) {
			$metadata['sizes'] = array_merge( $metadata['sizes'], FW_Backup_Image_Recovery::$common_sizes );
		}

		return $metadata;
	}

	public function callback_filter_intermediate_image_sizes_advanced( $sizes ) {

		$meta = wp_get_attachment_metadata( FW_Backup_Image_Recovery::$attachment_id );

		$uploadpath           = wp_upload_dir();
		$exclude              = array();
		$folder               = dirname( $meta['file'] );
		$filtered_meta_sizes  = array_intersect_key( $meta['sizes'], $sizes );
		$filtered_theme_sizes = array_intersect_key( $sizes, $meta['sizes'] );

		if ( ! empty( $filtered_meta_sizes ) ) {
			foreach ( $filtered_meta_sizes as $key => $size ) {
				$images_file = path_join( $uploadpath['basedir'], path_join( $folder, $size['file'] ) );

				if ( file_exists( $images_file ) ) {
					$exclude[] = $key;
				}
			}
		}

		$flipped                                      = array_flip( $exclude );
		FW_Backup_Image_Recovery::$common_sizes = array_intersect_key( $filtered_meta_sizes, $flipped );
		$diff                                         = array_diff_key( $filtered_theme_sizes, $flipped );

		$sizes = $diff;

		return $sizes;
	}

	public function remove_intermediate_images( $id ) {

		$meta         = wp_get_attachment_metadata( $id );
		$backup_sizes = get_post_meta( $id, '_wp_attachment_backup_sizes', true );
		$file         = get_attached_file( $id );
		$uploadpath   = wp_upload_dir();

		$collector = array();
		// Remove intermediate and backup images if there are any.
		if ( isset( $meta['sizes'] ) && is_array( $meta['sizes'] ) ) {
			foreach ( $meta['sizes'] as $size => $sizeinfo ) {
				$intermediate_file = str_replace( basename( $file ), $sizeinfo['file'], $file );

				$collector[] = path_join( $uploadpath['basedir'], $intermediate_file );
			}
		}

		if ( is_array( $backup_sizes ) ) {
			foreach ( $backup_sizes as $size ) {
				$del_file    = path_join( dirname( $meta['file'] ), $size['file'] );
				$collector[] = path_join( $uploadpath['basedir'], $del_file );
			}
		}

		return $collector;
	}
}