<?php

class Ido_REST_Ads_Controller extends WP_REST_Posts_Controller{

	protected $post_type;
	protected $meta;
	public function __construct($post_type='ad'){
		$this->post_type = $post_type;
		$this->namespace = IDO_REST_SLUG . '/v' . IDO_REST_API_VERSION;
		$obj = get_post_type_object($post_type);
		$this->rest_base = !empty($obj->rest_base)?$obj->rest_base:$obj->name;
		$this->meta = new WP_REST_Post_Meta_Fields( $this->post_type );
	}

}