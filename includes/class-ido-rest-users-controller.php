<?php
class Ido_REST_Users_Controller extends WP_REST_Users_Controller{

	protected $meta;

	public function __construct() {
		
		$this->namespace = IDO_REST_SLUG . '/v' . IDO_REST_API_VERSION;
		$this->rest_base = 'users';

		$this->meta = new WP_REST_User_Meta_Fields();
	}

	/**
	 * Get all users
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_items($request) {
		global $wpdb;

		$all_user_ids = array_map(
			function($user){
				return (int)$user->ID;
			},
			$wpdb->get_results("SELECT ID FROM $wpdb->users ORDER BY ID")
		);


		$prepared_args = array();
		$prepared_args['exclude'] = $request['exclude'];
		$prepared_args['include'] = $request['include'];
		$prepared_args['order'] = $request['order'];
		$prepared_args['number'] = $request['per_page'];
		if ( ! empty( $request['offset'] ) ) {
			$prepared_args['offset'] = $request['offset'];
		} else {
			$prepared_args['offset'] = ( $request['page'] - 1 ) * $prepared_args['number'];
		}
		$orderby_possibles = array(
			'id'              => 'ID',
			'include'         => 'include',
			'name'            => 'display_name',
			'registered_date' => 'registered',
			'slug'            => 'user_nicename',
			'email'           => 'user_email',
			'url'             => 'user_url',
		);
		$prepared_args['orderby'] = $orderby_possibles[ $request['orderby'] ];
		$prepared_args['search'] = $request['search'];
		$prepared_args['role__in'] = $request['roles'];

		if ( ! current_user_can( 'list_users' ) ) {
			$prepared_args['has_published_posts'] = true;
		}

		if ( ! empty( $prepared_args['search'] ) ) {
			$prepared_args['search'] = '*' . $prepared_args['search'] . '*';
		}

		if ( ! empty( $request['slug'] ) ) {
			$prepared_args['search'] = $request['slug'];
			$prepared_args['search_columns'] = array( 'user_nicename' );
		}

		/**
		 * Filter arguments, before passing to WP_User_Query, when querying users via the REST API.
		 *
		 * @see https://developer.wordpress.org/reference/classes/wp_user_query/
		 *
		 * @param array           $prepared_args Array of arguments for WP_User_Query.
		 * @param WP_REST_Request $request       The current request.
		 */
		$prepared_args = apply_filters( 'rest_user_query', $prepared_args, $request );


		// follower or following



		if(!empty($request['following'])):
			$current_followers = get_user_meta((int)$request['following'], '_bbpresslist_followers', true );
			// to do 取交集
			$prepared_args['include'] = array_map('intval', $current_followers);
			$prepared_args['exclude'] = array_diff($all_user_ids, $prepared_args['include']);
		endif;

		if(!empty($request['followed_by'])):
			$current_following = get_user_meta((int)$request['followed_by'], '_bbpresslist_following', true);
			$prepared_args['include'] = array_map('intval', $current_following);
			$prepared_args['exclude'] = array_diff($all_user_ids, $prepared_args['include']);
		endif;

		$query = new WP_User_Query( $prepared_args );

		$users = array();
		foreach ( $query->results as $user ) {
			$data = $this->prepare_item_for_response( $user, $request );
			$users[] = $this->prepare_response_for_collection( $data );
		}

		$response = rest_ensure_response( $users );

		// Store pagation values for headers then unset for count query.
		$per_page = (int) $prepared_args['number'];
		$page = ceil( ( ( (int) $prepared_args['offset'] ) / $per_page ) + 1 );

		$prepared_args['fields'] = 'ID';

		$total_users = $query->get_total();
		if ( $total_users < 1 ) {
			// Out-of-bounds, run the query again without LIMIT for total count
			unset( $prepared_args['number'] );
			unset( $prepared_args['offset'] );
			$count_query = new WP_User_Query( $prepared_args );
			$total_users = $count_query->get_total();
		}
		$response->header( 'X-WP-Total', (int) $total_users );
		$max_pages = ceil( $total_users / $per_page );
		$response->header( 'X-WP-TotalPages', (int) $max_pages );

		$base = add_query_arg( $request->get_query_params(), rest_url( sprintf( '%s/%s', $this->namespace, $this->rest_base ) ) );
		if ( $page > 1 ) {
			$prev_page = $page - 1;
			if ( $prev_page > $max_pages ) {
				$prev_page = $max_pages;
			}
			$prev_link = add_query_arg( 'page', $prev_page, $base );
			$response->link_header( 'prev', $prev_link );
		}
		if ( $max_pages > $page ) {
			$next_page = $page + 1;
			$next_link = add_query_arg( 'page', $next_page, $base );
			$response->link_header( 'next', $next_link );
		}

		return $response;
	}

	public function update_item($request) {
		$user = $this->get_user( $request['id'] );
		if ( is_wp_error( $user ) ) {
			return $user;
		}

		$id = $user->ID;

		if ( ! $user ) {
			return new WP_Error( 'rest_user_invalid_id', __( 'Invalid user ID.' ), array( 'status' => 404 ) );
		}

		if ( email_exists( $request['email'] ) && $request['email'] !== $user->user_email ) {
			return new WP_Error( 'rest_user_invalid_email', __( 'Invalid email address.' ), array( 'status' => 400 ) );
		}

		if ( ! empty( $request['username'] ) && $request['username'] !== $user->user_login ) {
			return new WP_Error( 'rest_user_invalid_argument', __( "Username isn't editable." ), array( 'status' => 400 ) );
		}

		if ( ! empty( $request['slug'] ) && $request['slug'] !== $user->user_nicename && get_user_by( 'slug', $request['slug'] ) ) {
			return new WP_Error( 'rest_user_invalid_slug', __( 'Invalid slug.' ), array( 'status' => 400 ) );
		}

		if ( ! empty( $request['roles'] ) ) {
			$check_permission = $this->check_role_update( $id, $request['roles'] );

			if ( is_wp_error( $check_permission ) ) {
				return $check_permission;
			}
		}

		$user = $this->prepare_item_for_database( $request );

		// Ensure we're operating on the same user we already checked.
		$user->ID = $id;

		$user_id = wp_update_user( wp_slash( (array) $user ) );

		if ( is_wp_error( $user_id ) ) {
			return $user_id;
		}

		$user = get_user_by( 'id', $user_id );

		/* This action is documented in lib/endpoints/class-wp-rest-users-controller.php */
		do_action( 'rest_insert_user', $user, $request, false );

		if ( is_multisite() && ! is_user_member_of_blog( $id ) ) {
			add_user_to_blog( get_current_blog_id(), $id, '' );
		}

		if ( ! empty( $request['roles'] ) ) {
			array_map( array( $user, 'add_role' ), $request['roles'] );
		}

		$schema = $this->get_item_schema();

		if ( ! empty( $schema['properties']['meta'] ) && isset( $request['meta'] ) ) {
			$meta_update = $this->meta->update_value( $request['meta'], $id );

			if ( is_wp_error( $meta_update ) ) {
				return $meta_update;
			}
		}

		$user = get_user_by( 'id', $user_id );
		$fields_update = $this->update_additional_fields_for_object( $user, $request );

		if ( is_wp_error( $fields_update ) ) {
			return $fields_update;
		}

		// custom code start

	

		if(isset($request['profile_media_id']) && (int)$request['profile_media_id'] > 0){

			update_user_meta( $user_id, $meta_key, 'profile_photo',	'profile_photo.jpg' );
			
			$profile_media_id = $request['profile_media_id'];

			$profile_media_url = wp_get_attachment_url($profile_media_id);

			// to do: make the folder dynamic

			$targeted_folder = '/opt/bitnami/apps/wordpress/htdocs/wp-content/uploads/ultimatemember/' . $user_id . '/';

			$profile_image = $targeted_folder . 'profile_photo.jpg';
	    	
	    	file_put_contents($profile_image, file_get_contents($profile_media_url));

	    	$dimensions = getimagesize($profile_image);

	    	if(file_exists($targeted_folder . 'profile_photo-40.jpg')){
	    		chmod($targeted_folder . 'profile_photo-40.jpg', 777); 
	    		unlink($targeted_folder . 'profile_photo-40.jpg');
	    	}

	    	if(file_exists($targeted_folder . 'profile_photo-80.jpg')){
	    		chmod($targeted_folder . 'profile_photo-80.jpg', 777); 
	    		unlink($targeted_folder . 'profile_photo-80.jpg');
	    	}

	    	if(file_exists($targeted_folder . 'profile_photo-190.jpg')){
	    		chmod($targeted_folder . 'profile_photo-190.jpg', 777); 
	    		unlink($targeted_folder . 'profile_photo-190.jpg');
	    	}

	    	$profile_image_40 = wp_crop_image($profile_image, 0, 0, $dimensions[0], $dimensions[1], 40, 40, false, $targeted_folder . 'profile_photo-40.jpg');

	    	$profile_image_80 = wp_crop_image($profile_image, 0, 0, $dimensions[0], $dimensions[1], 80, 80, false, $targeted_folder . 'profile_photo-80.jpg');

	    	$profile_image_190 = wp_crop_image($profile_image, 0, 0, $dimensions[0], $dimensions[1], 190, 190, false, $targeted_folder . 'profile_photo-190.jpg');
		}


		// custom code end

		$request->set_param( 'context', 'edit' );

		$response = $this->prepare_item_for_response( $user, $request );
		$response = rest_ensure_response( $response );

		return $response;		
	}

	/**
	 * Get the user, if the ID is valid.
	 *
	 * @since 4.7.2
	 *
	 * @param int $id Supplied ID.
	 * @return WP_User|WP_Error True if ID is valid, WP_Error otherwise.
	 */
	protected function get_user( $id ) {
		$error = new WP_Error( 'rest_user_invalid_id', __( 'Invalid user ID.' ), array( 'status' => 404 ) );
		if ( (int) $id <= 0 ) {
			return $error;
		}

		$user = get_userdata( (int) $id );
		if ( empty( $user ) || ! $user->exists() ) {
			return $error;
		}

		return $user;
	}
}