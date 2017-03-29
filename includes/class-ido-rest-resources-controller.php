<?php

class Ido_REST_Resources_Controller extends WP_REST_Controller{
	public function __construct(){
		$this->namespace = IDO_REST_SLUG . '/v' . IDO_REST_API_VERSION;
		$this->rest_base = 'resources';
	}

	/**
	 * Register the routes for the objects of the controller.
	 */
	public function register_routes(){

		register_rest_route( $this->namespace, '/' . $this->rest_base, array(
			array(
				'methods'         => WP_REST_Server::READABLE,
				'callback'        => array( $this, 'get_items' ),
				'permission_callback' => array( $this, 'get_items_permissions_check' ),
				'args'            => $this->get_collection_params(),
			),
			'schema' => array( $this, 'get_public_item_schema' ),
		) );
	}

	public function get_items_permissions_check($request){

		if ( 'edit' === $request['context'] && ! current_user_can('edit_posts')){
			return new WP_Error( 'rest_forbidden_context', __( 'Sorry, you are not allowed to edit these posts in this post type' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return true;		
	}

	public function get_items( $request ) {

		//Make sure a search string is set in case the orderby is set to 'relevace'
		if ( ! empty( $request['orderby'] ) && 'relevance' === $request['orderby'] && empty( $request['search'] ) && empty( $request['filter']['s'] ) ) {
			return new WP_Error( 'rest_no_search_term_defined', __( 'You need to define a search term to order by relevance.' ), array( 'status' => 400 ) );
		}

		$args                         = array();
		$args['post_type']            = array('post', 'place', 'advert', 'topic');
		$args['author']               = $request['author'];
		$args['paged']                = $request['page'];
		$args['s']                    = $request['search'];
		
		// Ensure our per_page parameter overrides filter.
		$args['posts_per_page']       = $request['per_page'];


		if(isset($request['favorited_by']) && !empty($request['favorited_by'])):
			$favorited_by                 = (int)$request['favorited_by'];
			$post_place_advert_favorites  = get_user_meta($favorited_by, 'simplefavorites', true)[0]['posts'];
			$topic_favorites              = array_map('intval', explode(',', get_user_meta($favorited_by, 'wp__bbp_favorites', true)));
			$favorites = array_merge($post_place_advert_favorites, $topic_favorites);
			$args['post__in'] = $favorites;
		endif;

		// add_filter( 'posts_request', array($this,'my_posts_request_filter') );
		$resources_query = new WP_Query();
		$query_result = $resources_query->query( $args );

		// Allow access to all password protected posts if the context is edit.
		if ( 'edit' === $request['context'] ) {
			add_filter( 'post_password_required', '__return_false' );
		}

		$resources = array();
		foreach ( $query_result as $resource ) {
			if ( ! $this->check_read_permission( $resource ) ) {
				continue;
			}

			$data = $this->prepare_item_for_response( $resource, $request );
			$resources[] = $this->prepare_response_for_collection( $data );
		}

		// Reset filter.
		if ( 'edit' === $request['context'] ) {
			remove_filter( 'post_password_required', '__return_false' );
		}

		$page = (int) $args['paged'];
		$total_resources = $resources_query->found_posts;

		if ( $total_resources < 1 ) {
			// Out-of-bounds, run the query again without LIMIT for total count
			unset( $args['paged'] );
			$count_query = new WP_Query();
			$count_query->query( $args );
			$total_resources = $count_query->found_posts;
		}

		$max_pages = ceil( $total_resources / (int) $args['posts_per_page'] );

		$response = rest_ensure_response( $resources );
		$response->header( 'X-WP-Total', (int) $total_resources );
		$response->header( 'X-WP-TotalPages', (int) $max_pages );

		$request_params = $request->get_query_params();

		$base = add_query_arg( $request_params, rest_url( sprintf( '%s/%s', $this->namespace, $this->rest_base ) ) );

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

	// public function prepare_items_query( $prepared_args = array(), $request = null){
	// 	$valid_vars = array_flip( $this->get_allowed_query_vars() );
	// 	$query_args = array();
	// 	foreach ( $valid_vars as $var => $index ) {
	// 		if ( isset( $prepared_args[ $var ] ) ) {
	// 			/**
	// 			 * Filter the query_vars used in `get_items` for the constructed query.
	// 			 *
	// 			 * The dynamic portion of the hook name, $var, refers to the query_var key.
	// 			 *
	// 			 * @param mixed $prepared_args[ $var ] The query_var value.
	// 			 *
	// 			 */
	// 			$query_args[ $var ] = apply_filters( "rest_query_var-{$var}", $prepared_args[ $var ] );
	// 		}
	// 	}

	// 	return $query_args;
	// }

	// public function get_allowed_query_vars(){
	// 	global $wp;

	// 	/**
	// 	 * Filter the publicly allowed query vars.
	// 	 *
	// 	 * Allows adjusting of the default query vars that are made public.
	// 	 *
	// 	 * @param array  Array of allowed WP_Query query vars.
	// 	 */
	// 	$valid_vars = apply_filters( 'query_vars', $wp->public_query_vars );

	// 	if ( current_user_can( 'edit_posts' ) ) {
	// 		*
	// 		 * Filter the allowed 'private' query vars for authorized users.
	// 		 *
	// 		 * If the user has the `edit_posts` capability, we also allow use of
	// 		 * private query parameters, which are only undesirable on the
	// 		 * frontend, but are safe for use in query strings.
	// 		 *
	// 		 * To disable anyway, use
	// 		 * `add_filter( 'rest_private_query_vars', '__return_empty_array' );`
	// 		 *
	// 		 * @param array $private_query_vars Array of allowed query vars for authorized users.
	// 		 * }
			 
	// 		$private = apply_filters( 'rest_private_query_vars', $wp->private_query_vars );
	// 		$valid_vars = array_merge( $valid_vars, $private );
	// 	}
	// 	// Define our own in addition to WP's normal vars.
	// 	$rest_valid = array(
	// 		'posts_per_page',
	// 	);
	// 	$valid_vars = array_merge( $valid_vars, $rest_valid );

	// 	/**
	// 	 * Filter allowed query vars for the REST API.
	// 	 *
	// 	 * This filter allows you to add or remove query vars from the final allowed
	// 	 * list for all requests, including unauthenticated ones. To alter the
	// 	 * vars for editors only, {@see rest_private_query_vars}.
	// 	 *
	// 	 * @param array {
	// 	 *    Array of allowed WP_Query query vars.
	// 	 *
	// 	 *    @param string $allowed_query_var The query var to allow.
	// 	 * }
	// 	 */
	// 	$valid_vars = apply_filters( 'rest_query_vars', $valid_vars );

	// 	return $valid_vars;		
	// }

	public function check_read_permission($post){
	// Can we read the post?
		if ( 'publish' === $post->post_status || current_user_can( $post_type->cap->read_post, $post->ID ) ) {
			return true;
		}

		$post_status_obj = get_post_status_object( $post->post_status );
		if ( $post_status_obj && $post_status_obj->public ) {
			return true;
		}

		// Can we read the parent if we're inheriting?
		if ( 'inherit' === $post->post_status && $post->post_parent > 0 ) {
			$parent = $this->get_post( $post->post_parent );
			return $this->check_read_permission( $parent );
		}

		// If we don't have a parent, but the status is set to inherit, assume
		// it's published (as per get_post_status()).
		if ( 'inherit' === $post->post_status ) {
			return true;
		}

		return false;		
	}

	public function get_collection_params(){
		return array(
			'page'                   => array(
				'description'        => __( 'Current page of the collection.' ),
				'type'               => 'integer',
				'default'            => 1,
				'sanitize_callback'  => 'absint',
				'validate_callback'  => 'rest_validate_request_arg',
				'minimum'            => 1,
			),
			'per_page'               => array(
				'description'        => __( 'Maximum number of items to be returned in result set.' ),
				'type'               => 'integer',
				'default'            => 10,
				'minimum'            => 1,
				'maximum'            => 100,
				'sanitize_callback'  => 'absint',
				'validate_callback'  => 'rest_validate_request_arg',
			),
			'search'                 => array(
				'description'        => __( 'Limit results to those matching a string.' ),
				'type'               => 'string',
				'sanitize_callback'  => 'sanitize_text_field',
				'validate_callback'  => 'rest_validate_request_arg',
			),
		);		
	}

	public function prepare_item_for_response($post, $request){
		$GLOBALS['post'] = $post;
		setup_postdata( $post );
		$data = array();
		$data['id'] = $post->ID;
		$data['type'] = $post->post_type;
		// $data['title'] = array(
		// 	'raw'      => $post->post_title,
		// 	'rendered' => get_the_title( $post->ID ),
		// );
		$data['title'] = get_the_title($post->ID);
		// Wrap the data in a response object.
		$response = rest_ensure_response( $data );
		return $response;
	}

	function my_posts_request_filter( $input ) {
        print_r( $input ); 
        return $input;
    }		

}