<?php
/**
 * Access comments
 */
class Ido_REST_Comments_Controller extends WP_REST_Comments_Controller {
	
	protected $meta;
	
	public function __construct() {
		
		$this->namespace = IDO_REST_SLUG . '/v' . IDO_REST_API_VERSION;
		$this->rest_base = 'comments';

		$this->meta = new WP_REST_Comment_Meta_Fields();
	}

	public function register_routes() {

		register_rest_route( $this->namespace, '/' . $this->rest_base, array(
			array(
				'methods'   => WP_REST_Server::READABLE,
				'callback'  => array( $this, 'get_items' ),
				'permission_callback' => array( $this, 'get_items_permissions_check' ),
				'args'      => $this->get_collection_params(),
			),
			array(
				'methods'  => WP_REST_Server::CREATABLE,
				'callback' => array( $this, 'create_item' ),
				'permission_callback' => array( $this, 'create_item_permissions_check' ),
				'args'     => $this->get_endpoint_args_for_item_schema( WP_REST_Server::CREATABLE ),
			),
			'schema' => array( $this, 'get_public_item_schema' ),
		) );

		register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<id>[\d]+)', array(
			array(
				'methods'  => WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_item' ),
				'permission_callback' => array( $this, 'get_item_permissions_check' ),
				'args'     => array(
					'context'          => $this->get_context_param( array( 'default' => 'view' ) ),
				),
			),
			array(
				'methods'  => WP_REST_Server::EDITABLE,
				'callback' => array( $this, 'update_item' ),
				'permission_callback' => array( $this, 'update_item_permissions_check' ),
				'args'     => $this->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE ),
			),
			array(
				'methods'  => WP_REST_Server::DELETABLE,
				'callback' => array( $this, 'delete_item' ),
				'permission_callback' => array( $this, 'delete_item_permissions_check' ),
				'args'     => array(
					'force'    => array(
						'default'     => false,
						'description' => __( 'Whether to bypass trash and force deletion.' ),
					),
				),
			),
			'schema' => array( $this, 'get_public_item_schema' ),
		) );
	}

	public function create_item($request){
		// add_action('rest_insert_comment', array($this, 'add_comment_images'), 999);
		if ( ! empty( $request['id'] ) ) {
			return new WP_Error( 'rest_comment_exists', __( 'Cannot create existing comment.' ), array( 'status' => 400 ) );
		}

		$prepared_comment = $this->prepare_item_for_database( $request );

		if ( is_wp_error( $prepared_comment ) ) {
			return $prepared_comment;
		}

		/*
		 * Do not allow a comment to be created with an empty string for
		 * comment_content. See wp_handle_comment_submission().
		 */
		if ( '' === $prepared_comment['comment_content'] ) {
			return new WP_Error( 'rest_comment_content_invalid', __( 'Comment content is invalid.' ), array( 'status' => 400 ) );
		}

		// Setting remaining values before wp_insert_comment so we can use wp_allow_comment().
		if ( ! isset( $prepared_comment['comment_date_gmt'] ) ) {
			$prepared_comment['comment_date_gmt'] = current_time( 'mysql', true );
		}

		// Set author data if the user's logged in.
		$missing_author = empty( $prepared_comment['user_id'] )
			&& empty( $prepared_comment['comment_author'] )
			&& empty( $prepared_comment['comment_author_email'] )
			&& empty( $prepared_comment['comment_author_url'] );

		if ( is_user_logged_in() && $missing_author ) {
			$user = wp_get_current_user();

			$prepared_comment['user_id'] = $user->ID;
			$prepared_comment['comment_author'] = $user->display_name;
			$prepared_comment['comment_author_email'] = $user->user_email;
			$prepared_comment['comment_author_url'] = $user->user_url;
		}

		// Honor the discussion setting that requires a name and email address of the comment author.
		if ( get_option( 'require_name_email' ) ) {
			if ( ! isset( $prepared_comment['comment_author'] ) && ! isset( $prepared_comment['comment_author_email'] ) ) {
				return new WP_Error( 'rest_comment_author_data_required', __( 'Creating a comment requires valid author name and email values.' ), array( 'status' => 400 ) );
			}

			if ( ! isset( $prepared_comment['comment_author'] ) ) {
				return new WP_Error( 'rest_comment_author_required', __( 'Creating a comment requires a valid author name.' ), array( 'status' => 400 ) );
			}

			if ( ! isset( $prepared_comment['comment_author_email'] ) ) {
				return new WP_Error( 'rest_comment_author_email_required', __( 'Creating a comment requires a valid author email.' ), array( 'status' => 400 ) );
			}
		}

		if ( ! isset( $prepared_comment['comment_author_email'] ) ) {
			$prepared_comment['comment_author_email'] = '';
		}

		if ( ! isset( $prepared_comment['comment_author_url'] ) ) {
			$prepared_comment['comment_author_url'] = '';
		}

		if ( ! isset( $prepared_comment['comment_agent'] ) ) {
			$prepared_comment['comment_agent'] = '';
		}

		$prepared_comment['comment_approved'] = wp_allow_comment( $prepared_comment, true );

		if ( is_wp_error( $prepared_comment['comment_approved'] ) ) {
			$error_code    = $prepared_comment['comment_approved']->get_error_code();
			$error_message = $prepared_comment['comment_approved']->get_error_message();

			if ( 'comment_duplicate' === $error_code ) {
				return new WP_Error( $error_code, $error_message, array( 'status' => 409 ) );
			}

			if ( 'comment_flood' === $error_code ) {
				return new WP_Error( $error_code, $error_message, array( 'status' => 400 ) );
			}

			return $prepared_comment['comment_approved'];
		}

		/**
		 * Filters a comment before it is inserted via the REST API.
		 *
		 * Allows modification of the comment right before it is inserted via wp_insert_comment().
		 *
		 * @since 4.7.0
		 *
		 * @param array           $prepared_comment The prepared comment data for wp_insert_comment().
		 * @param WP_REST_Request $request          Request used to insert the comment.
		 */
		$prepared_comment = apply_filters( 'rest_pre_insert_comment', $prepared_comment, $request );

		remove_all_filters( 'wp_insert_comment', 10);

		$comment_id = wp_insert_comment( $prepared_comment );

		if ( ! $comment_id ) {
			return new WP_Error( 'rest_comment_failed_create', __( 'Creating comment failed.' ), array( 'status' => 500 ) );
		}

		if ( isset( $request['status'] ) ) {
			$comment = get_comment( $comment_id );

			$this->handle_status_param( $request['status'], $comment );
		}

		$schema = $this->get_item_schema();

		if ( ! empty( $schema['properties']['meta'] ) && isset( $request['meta'] ) ) {
			$meta_update = $this->meta->update_value( $request['meta'], $comment_id );

			if ( is_wp_error( $meta_update ) ) {
				return $meta_update;
			}
		}

		// meta field udpate

		// 1. rating

        $rating = $request->get_param('rating');
        $post_id = $request->get_param('post');

        add_comment_meta($comment_id, 'rating', $rating);
        
        if( false == get_post_meta($post_id, 'rating_number', true)|| false == get_post_meta($post_id, 'rating_sum', true)){
            add_post_meta($post_id, 'rating_number', 1);
            add_post_meta($post_id, 'rating_sum', $rating);
            add_post_meta($post_id, 'rating_average', $rating);
        } else {
            $rating_number = (int)(get_post_meta($post_id, 'rating_number', true));
            $rating_number++;
            $rating_sum    = (get_post_meta($post_id, 'rating_sum', true));
            $rating_sum   += $rating;            
            update_post_meta($post_id, 'rating_number', $rating_number);
            update_post_meta($post_id, 'rating_sum', $rating_sum);
            $rating_average = $rating_sum/$rating_number;
            update_post_meta($post_id, 'rating_average', $rating_average);
        }

        // 2. uploaded images

        $img_ids = array();

        if(!empty($request->get_param('img_ids'))):
			$img_ids = explode(',', $request->get_param('img_ids'));
		endif;

		add_comment_meta( $comment_id, 'comment_image_reloaded', $img_ids );


		$comment = get_comment( $comment_id );

		$fields_update = $this->update_additional_fields_for_object( $comment, $request );

		if ( is_wp_error( $fields_update ) ) {
			return $fields_update;
		}

		$context = current_user_can( 'moderate_comments' ) ? 'edit' : 'view';

		$request->set_param( 'context', $context );

		$response = $this->prepare_item_for_response( $comment, $request );
		$response = rest_ensure_response( $response );

		$response->set_status( 201 );
		$response->header( 'Location', rest_url( sprintf( '%s/%s/%d', $this->namespace, $this->rest_base, $comment_id ) ) );

		/**
		 * Fires after a comment is created or updated via the REST API.
		 *
		 * @since 4.7.0
		 *
		 * @param array           $comment  Comment as it exists in the database.
		 * @param WP_REST_Request $request  The request sent to the API.
		 * @param bool            $creating True when creating a comment, false when updating.
		 */
		do_action( 'rest_insert_comment', $comment, $request, true );

		return $response;
	}
}