<?php

class Ido_REST_Places_Controller extends WP_REST_Posts_Controller{

	protected $post_type;
	protected $meta;
	public function __construct($post_type='place'){
		$this->post_type = $post_type;
		$this->namespace = IDO_REST_SLUG . '/v' . IDO_REST_API_VERSION;
		$obj = get_post_type_object($post_type);
		$this->rest_base = !empty($obj->rest_base)?$obj->rest_base:$obj->name;
		$this->meta = new WP_REST_Post_Meta_Fields( $this->post_type );
	}

	public function get_collection_params(){
		$params = parent::get_collection_params();

        // $default_lat        = 51.0530588;
        // $default_lng        = -114.0625613;
		
		$params['orderby'] = array(
			'description'        => __( 'Sort collection by object attribute.' ),
			'type'               => 'string',
			'default'            => 'date',
			'enum'               => array(
				'date',
				'relevance',
				'id',
				'include',
				'title',
				'slug',
				'distance',
				'rating_average',
				'views_count',
				'is_featured'
			),
			'validate_callback'  => 'rest_validate_request_arg',
		);

		$params['is_featured'] = array(
			'description'        => __( 'Filter featured or non-featured places.' ),
			'type'               => 'boolean',
            'default'            => null,
            'enum'               => array(0, 1),
			'validate_callback'  => 'rest_validate_request_arg',
		);

        $params['dist']    = array(
            'description'        => __( 'Limit response to resources within some distance from current position.' ),
            'type'               => 'integer',
            'sanitize_callback'  => 'absint',
            'validate_callback'  => 'rest_validate_request_arg',
        );

        $params['lat']    = array(
            'description'        => __( 'Pass lat of current position to server' ),
            'type'               => 'float',
            // 'default'            => $default_lat,
            'validate_callback'  => 'rest_validate_request_arg',
        );

        $params['lng']    = array(
            'description'        => __( 'Pass lng of current position to server' ),
            'type'               => 'float',
            // 'default'            => $default_lng,
            'validate_callback'  => 'rest_validate_request_arg',
        );

		return $params;
	}

	public function get_items($request){
		
		// Ensure a search string is set in case the orderby is set to 'relevance'.
		if ( ! empty( $request['orderby'] ) && 'relevance' === $request['orderby'] && empty( $request['search'] ) ) {
			return new WP_Error( 'rest_no_search_term_defined', __( 'You need to define a search term to order by relevance.' ), array( 'status' => 400 ) );
		}

		// Retrieve the list of registered collection query parameters.
		$registered = $this->get_collection_params();
		$args = array();

		/*
		 * This array defines mappings between public API query parameters whose
		 * values are accepted as-passed, and their internal WP_Query parameter
		 * name equivalents (some are the same). Only values which are also
		 * present in $registered will be set.
		 */
		$parameter_mappings = array(
			'author'         => 'author__in',
			'author_exclude' => 'author__not_in',
			'exclude'        => 'post__not_in',
			'include'        => 'post__in',
			'menu_order'     => 'menu_order',
			'offset'         => 'offset',
			'order'          => 'order',
			'orderby'        => 'orderby',
			'page'           => 'paged',
			'parent'         => 'post_parent__in',
			'parent_exclude' => 'post_parent__not_in',
			'search'         => 's',
			'slug'           => 'post_name__in',
			'status'         => 'post_status',
			'is_featured'    => 'is_featured'
		);

		/*
		 * For each known parameter which is both registered and present in the request,
		 * set the parameter's value on the query $args.
		 */
		foreach ( $parameter_mappings as $api_param => $wp_param ) {
			if ( isset( $registered[ $api_param ], $request[ $api_param ] ) ) {
				$args[ $wp_param ] = $request[ $api_param ];
			}
		}

		// Check for & assign any parameters which require special handling or setting.
		$args['date_query'] = array();

		// Set before into date query. Date query must be specified as an array of an array.
		if ( isset( $registered['before'], $request['before'] ) ) {
			$args['date_query'][0]['before'] = $request['before'];
		}

		// Set after into date query. Date query must be specified as an array of an array.
		if ( isset( $registered['after'], $request['after'] ) ) {
			$args['date_query'][0]['after'] = $request['after'];
		}

		// Ensure our per_page parameter overrides any provided posts_per_page filter.
		if ( isset( $registered['per_page'] ) ) {
			$args['posts_per_page'] = $request['per_page'];
		}

		if ( isset( $registered['sticky'], $request['sticky'] ) ) {
			$sticky_posts = get_option( 'sticky_posts', array() );
			if ( $sticky_posts && $request['sticky'] ) {
				/*
				 * As post__in will be used to only get sticky posts,
				 * we have to support the case where post__in was already
				 * specified.
				 */
				$args['post__in'] = $args['post__in'] ? array_intersect( $sticky_posts, $args['post__in'] ) : $sticky_posts;

				/*
				 * If we intersected, but there are no post ids in common,
				 * WP_Query won't return "no posts" for post__in = array()
				 * so we have to fake it a bit.
				 */
				if ( ! $args['post__in'] ) {
					$args['post__in'] = array( -1 );
				}
			} elseif ( $sticky_posts ) {
				/*
				 * As post___not_in will be used to only get posts that
				 * are not sticky, we have to support the case where post__not_in
				 * was already specified.
				 */
				$args['post__not_in'] = array_merge( $args['post__not_in'], $sticky_posts );
			}
		}

		// Force the post_type argument, since it's not a user input variable.
		$args['post_type'] = $this->post_type;

		/**
		 * Filters the query arguments for a request.
		 *
		 * Enables adding extra arguments or setting defaults for a post collection request.
		 *
		 * @since 4.7.0
		 *
		 * @link https://developer.wordpress.org/reference/classes/wp_query/
		 *
		 * @param array           $args    Key value array of query var to query value.
		 * @param WP_REST_Request $request The request used.
		 */
		$args = apply_filters( "rest_{$this->post_type}_query", $args, $request );
		$query_args = $this->prepare_items_query( $args, $request );

		$taxonomies = wp_list_filter( get_object_taxonomies( $this->post_type, 'objects' ), array( 'show_in_rest' => true ) );

		foreach ( $taxonomies as $taxonomy ) {
			$base = ! empty( $taxonomy->rest_base ) ? $taxonomy->rest_base : $taxonomy->name;
			$tax_exclude = $base . '_exclude';

			if ( ! empty( $request[ $base ] ) ) {
				$query_args['tax_query'][] = array(
					'taxonomy'         => $taxonomy->name,
					'field'            => 'term_id',
					'terms'            => $request[ $base ],
					'include_children' => false,
				);
			}

			if ( ! empty( $request[ $tax_exclude ] ) ) {
				$query_args['tax_query'][] = array(
					'taxonomy'         => $taxonomy->name,
					'field'            => 'term_id',
					'terms'            => $request[ $tax_exclude ],
					'include_children' => false,
					'operator'         => 'NOT IN',
				);
			}
		}

		// if($query_args['orderby'] == 'rating_average'){
		// 	$query_args['meta_key'] = 'rating_average';
		// 	$query_args['orderby'] = 'meta_value_num';
		// }

		if($query_args['orderby'] == 'views_count'){
			$query_args['meta_key'] = '_count-views_all';
			$query_args['orderby'] = 'meta_value_num';
		}

		if($query_args['orderby'] == 'is_featured'){
			$query_args['meta_key'] = 'is_featured';
			$query_args['orderby'] = 'meta_value_num';
		}

		if($request['is_featured'] == '1'){
			$query_args['meta_query'] = 
			array(
				array(
					'key' => 'is_featured',
					'value' => '1'
				)
			);
		}

		if($request['is_featured'] == '0'){
			$query_args['meta_query'] = 			
			array(
				array(
					'key' => 'is_featured',
					'value' => '1',
					'compare' => '!='
				)
			);
		}

		$idocalgary_query_fields = function($fields, $wp_query) use ($request) {
        	if($request['orderby'] === 'distance' || !empty($request['dist'])){
        	    $user_lng = $request->get_param('lng');
    			$user_lat = $request->get_param('lat');
    			$table = 'wp_locations';

    			$fields .= " ,6371.009*2*ATAN2(SQRT(SIN(RADIANS((wp_locations.latitude)-($user_lat))/2)*SIN(RADIANS((wp_locations.latitude)-($user_lat))/2)+COS(RADIANS(($user_lat)))*COS(RADIANS((wp_locations.latitude)))*SIN(RADIANS((wp_locations.longitude)-($user_lng))/2)*SIN(RADIANS((wp_locations.longitude)-($user_lng))/2)), SQRT(1-SQRT(SIN(RADIANS((wp_locations.latitude)-($user_lat))/2)*SIN(RADIANS((wp_locations.latitude)-($user_lat))/2)+COS(RADIANS(($user_lat)))*COS(RADIANS((wp_locations.latitude)))*SIN(RADIANS((wp_locations.longitude)-($user_lng))/2)*SIN(RADIANS((wp_locations.longitude)-($user_lng))/2)))) as distance";

    			// $fields .= " ,(6371.009 * 2 * ASIN(SQRT( POWER(SIN((ABS($user_lat) - ABS(" . $table . ".latitude)) * pi()/180 / 2), 2) +COS(ABS($user_lat) * pi()/180) * COS( ABS(" . $table . ".latitude) * pi()/180) *POWER(SIN(($user_lng - " . $table . ".longitude) * pi()/180 / 2), 2) ))) as distance ";
    		}

        	return $fields;
        };

        $idocalgary_query_join = function($join, $wp_query) use ($request){

        	if($request['orderby'] == 'rating_average'){
        		$join .= " LEFT JOIN wp_postmeta ON (wp_posts.ID = wp_postmeta.post_id AND wp_postmeta.meta_key = 'rating_average')";
        	}

        	if($request['orderby'] === 'distance' || !empty($request['dist'])){
        		$join .= " LEFT JOIN wp_locations ON (wp_locations.post_id = wp_posts.ID) ";
        	}
        	return $join;
        };

        $idocalgary_query_where = function($where, $wp_query) use ($request){
        	
        	if(!empty($request['dist'])){
        		$dist = (int)$request['dist'];
        		$user_lng = $request->get_param('lng');
    			$user_lat = $request->get_param('lat');
       //  	    $user_lng = $request->get_param('lng');
    			// $user_lat = $request->get_param('lat');


		     //    $lon1 = $user_lng - $dist / abs(cos(deg2rad($user_lat)) * 69);
		     //    $lon2 = $user_lng + $dist / abs(cos(deg2rad($user_lat)) * 69);
		     //    $lat1 = $user_lat - ($dist / 69);
		     //    $lat2 = $user_lat + ($dist / 69);


		     //    $rlon1 = is_numeric(min($lon1, $lon2)) ? min($lon1, $lon2) : '';
		     //    $rlon2 = is_numeric(max($lon1, $lon2)) ? max($lon1, $lon2) : '';
		     //    $rlat1 = is_numeric(min($lat1, $lat2)) ? min($lat1, $lat2) : '';
		     //    $rlat2 = is_numeric(max($lat1, $lat2)) ? max($lat1, $lat2) : '';

		     //    $where .= " AND ( wp_locations.latitude BETWEEN $rlat1 and $rlat2 )
		     //                AND ( wp_locations.longitude BETWEEN $rlon1 and $rlon2)";
        		$where .= " AND CONVERT(6371.009*2*ATAN2(SQRT(SIN(RADIANS((wp_locations.latitude)-($user_lat))/2)*SIN(RADIANS((wp_locations.latitude)-($user_lat))/2)+COS(RADIANS(($user_lat)))*COS(RADIANS((wp_locations.latitude)))*SIN(RADIANS((wp_locations.longitude)-($user_lng))/2)*SIN(RADIANS((wp_locations.longitude)-($user_lng))/2)), SQRT(1-SQRT(SIN(RADIANS((wp_locations.latitude)-($user_lat))/2)*SIN(RADIANS((wp_locations.latitude)-($user_lat))/2)+COS(RADIANS(($user_lat)))*COS(RADIANS((wp_locations.latitude)))*SIN(RADIANS((wp_locations.longitude)-($user_lng))/2)*SIN(RADIANS((wp_locations.longitude)-($user_lng))/2)))) ,DECIMAL(64,4)) <= " . $dist;
        	}
        	return $where;
        };

        $idocalgary_query_orderby = function($orderby, $wp_query) use ($request){

        	if($request['orderby'] == 'rating_average'){
        		if($request['order'] === 'desc'){
        			$orderby = " wp_postmeta.meta_value+0 DESC";
        		} else {
        			$orderby = " wp_postmeta.meta_value+0 ASC";
        		}
        	}


        	if($request['orderby'] === 'distance'){
        		if($request['order'] === 'desc'){
        			$orderby = " distance DESC";
        		} else {
        			$orderby = " distance ASC";
        		}
        	}
        	return $orderby;
        };

        add_filter('posts_fields', $idocalgary_query_fields, 10, 2);
        add_filter('posts_join', $idocalgary_query_join, 10, 2);
        add_filter('posts_where', $idocalgary_query_where, 10, 2);
        add_filter('posts_orderby', $idocalgary_query_orderby, 10, 2);
		
		// add_filter( 'posts_request', array($this,'my_posts_request_filter') );
		$posts_query  = new WP_Query();

		$query_result = $posts_query->query( $query_args );

        remove_filter('posts_fields', $idocalgary_query_fields, 10, 2);
        remove_filter('posts_join', $idocalgary_query_join, 10, 2);
        remove_filter('posts_where', $idocalgary_query_where, 10, 2);
        remove_filter('posts_orderby', $idocalgary_query_orderby, 10, 2);


		// Allow access to all password protected posts if the context is edit.
		if ( 'edit' === $request['context'] ) {
			add_filter( 'post_password_required', '__return_false' );
		}

		$posts = array();

		foreach ( $query_result as $post ) {
			if ( ! $this->check_read_permission( $post ) ) {
				continue;
			}

			$data    = $this->prepare_item_for_response( $post, $request );
			$posts[] = $this->prepare_response_for_collection( $data );
		}

		// Reset filter.
		if ( 'edit' === $request['context'] ) {
			remove_filter( 'post_password_required', '__return_false' );
		}

		$page = (int) $query_args['paged'];
		$total_posts = $posts_query->found_posts;

		if ( $total_posts < 1 ) {
			// Out-of-bounds, run the query again without LIMIT for total count.
			unset( $query_args['paged'] );

			$count_query = new WP_Query();
			$count_query->query( $query_args );
			$total_posts = $count_query->found_posts;
		}

		$max_pages = ceil( $total_posts / (int) $posts_query->query_vars['posts_per_page'] );
		$response  = rest_ensure_response( $posts );

		$response->header( 'X-WP-Total', (int) $total_posts );
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

	function my_posts_request_filter( $input ) {
        print_r( $input ); 
        return $input;
    }
}