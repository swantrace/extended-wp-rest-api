<?php

class Ido_REST_Posts_Controller extends WP_REST_Posts_Controller{

	protected $post_type;
	// protected $meta;
	public function __construct($post_type='post'){
		$this->post_type = $post_type;
		$this->namespace = IDO_REST_SLUG . '/v' . IDO_REST_API_VERSION;
		$obj = get_post_type_object($post_type);
		$this->rest_base = !empty($obj->rest_base)?$obj->rest_base:$obj->name;
		$this->meta = new WP_REST_Post_Meta_Fields( $this->post_type );
	}

	/**
	 * Retrieves a collection of posts.
	 *
	 * @since 4.7.0
	 * @access public
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_items( $request ) {
		$cat = $request['categories'];
		$paged = !empty($request['page'])?$request['page']:1;
		$posts_per_page = !empty($request['per_page'])?$request['per_page']:20;

		$args = array();

		if(!empty($cat)){
			$args['cat'] = $cat;
		}

		if(!empty($paged)){
			$args['paged'] = $paged;
		}

		if(!empty($posts_per_page)){
			$args['posts_per_page'] = $posts_per_page;
		}

		$args['post__not_in'] = get_option('sticky_posts');

		$raw_non_sticky_posts = get_posts($args);

		$non_sticky_posts = array_map(function($raw_non_sticky_post) use ($cat){
			$non_sticky_post = array();
			$non_sticky_post['id'] = $raw_non_sticky_post->ID;
			$non_sticky_post['plain_title'] = $raw_non_sticky_post->post_title;
			$non_sticky_post['categories'] = $cat;
			$non_sticky_post['sticky'] = false;
			$non_sticky_post['tags'] = wp_get_post_tags($raw_non_sticky_post->ID, array('fields' => 'names'));
			// image-size: 255x143
			$featured_image_url = !empty(get_the_post_thumbnail_url($raw_non_sticky_post->ID, 'thumbnail_255_143'))?get_the_post_thumbnail_url($raw_non_sticky_post->ID, 'thumbnail_255_143'):"http://s3.amazonaws.com/52calgary-media/wp-content/uploads/2017/02/14182028/no-image.png";
			$non_sticky_post['featured_image_url'] = $featured_image_url; 
			$non_sticky_post['views_count'] =  get_post_meta($raw_non_sticky_post->ID, '_count-views_all', true);;
			$non_sticky_post['comments_count'] = $raw_non_sticky_post->comment_count;
			$non_sticky_post['human_readable_time'] = human_time_diff(mysql2date( 'U', $raw_non_sticky_post->post_date_gmt), current_time('timestamp', true)) . '前';
			$non_sticky_post['excerpt']['rendered'] = '';
			return $non_sticky_post;
		}, $raw_non_sticky_posts);

		unset($args['post__not_in']);
		$args['post__in'] = get_option('sticky_posts');
		$raw_sticky_posts = get_posts($args);

		$sticky_posts = array_map(function($raw_sticky_post) use ($cat){
			$sticky_post = array();
			$sticky_post['id'] = $raw_sticky_post->ID;
			$sticky_post['plain_title'] = $raw_sticky_post->post_title;
			$sticky_post['categories'] =$cat;
			$sticky_post['sticky'] = true;
			$sticky_post['tags'] = wp_get_post_tags($raw_sticky_post->ID, array('fields' => 'names'));
			// image-size: 750x400
			$featured_image_url = !empty(get_the_post_thumbnail_url($raw_sticky_post->ID, 'thumbnail_750_400'))?get_the_post_thumbnail_url($raw_sticky_post->ID, 'thumbnail_750_400'):"http://s3.amazonaws.com/52calgary-media/wp-content/uploads/2017/02/14182028/no-image.png";
			$sticky_post['featured_image_url'] = $featured_image_url; 
			$sticky_post['views_count'] =  get_post_meta($raw_sticky_post->ID, '_count-views_all', true);;
			$sticky_post['comments_count'] = $raw_sticky_post->comment_count;
			$sticky_post['human_readable_time'] = human_time_diff(mysql2date( 'U', $raw_sticky_post->post_date_gmt), current_time('timestamp', true)) . '前';
			$sticky_post['excerpt']['rendered'] = '';  
			return $sticky_post;
		}, $raw_sticky_posts);

		$ad_args = array(
			'category_name' => 'ad',
			'orderby'       => 'rand'
		);

		$raw_ads = get_posts($ad_args);

		$ads = array_map(function($raw_ad){
			$ad = array();
			$ad['id'] = $raw_ad->ID;
			$ad['plain_title'] = $raw_ad->post_title;
			$ad['categories'] = [40];
			$ad['sticky'] = false;
			$ad['tags'] = wp_get_post_tags($raw_ad->ID, array('fields' => 'names'));
			// image-size: 255x143
			$featured_image_url = !empty(get_the_post_thumbnail_url($raw_ad->ID, 'app_image_1'))?get_the_post_thumbnail_url($raw_ad->ID, 'app_image_1'):"http://s3.amazonaws.com/52calgary-media/wp-content/uploads/2017/02/14182028/no-image.png";
			$ad['featured_image_url'] = $featured_image_url; 
			$ad['views_count'] =  get_post_meta($raw_ad->ID, '_count-views_all', true);;
			$ad['comments_count'] = $raw_ad->comment_count;
			$ad['human_readable_time'] = human_time_diff(mysql2date( 'U', $raw_ad->post_date_gmt), current_time('timestamp', true)) . '前';
			$ad['excerpt']['rendered'] = '';    
			return $ad;
		}, $raw_ads);

		$ad_count = count($ads);

		$indexs = array(5, 11, 17, 23);
		$size = count($non_sticky_posts);
		$i = 0;

		foreach ($indexs as $index) {
			if($index > $size+$i){
				break;
			}
			$j = ($paged-1)*4+$i;
			if($j>=$ad_count){
				$j = (($paged - 1) * 4 + $i) % $ad_count;
			}
			$non_sticky_posts = $this->insert($non_sticky_posts, $index, $ads[$j]);
			$i++;
		}

		if($paged == 1){
			$final_posts = array_merge($sticky_posts, $non_sticky_posts);
		} else {
			$final_posts = $non_sticky_posts;
		}		


		$response  = rest_ensure_response($final_posts);
		return $response;

	}

	private function insert($array, $index, $val)
    {
       $size = count($array); //because I am going to use this more than one time
       if (!is_int($index) || $index < 0 || $index > $size)
       {
           return -1;
       }
       else
       {
           $temp   = array_slice($array, 0, $index);
           $temp[] = $val;
           return array_merge($temp, array_slice($array, $index, $size));
       }
   }
}