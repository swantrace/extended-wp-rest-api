<?php
class Ido_REST_Terms_Controller extends WP_REST_Terms_Controller{


	protected $taxonomy;
	protected $meta;
	protected $sort_column;
	protected $total_terms;
	public function __construct( $taxonomy ) {
		$this->taxonomy = $taxonomy;
		$this->namespace = IDO_REST_SLUG . '/v' . IDO_REST_API_VERSION;
		$tax_obj = get_taxonomy( $taxonomy );
		$this->rest_base = ! empty( $tax_obj->rest_base ) ? $tax_obj->rest_base : $tax_obj->name;

		$this->meta = new WP_REST_Term_Meta_Fields( $taxonomy );
	}

}