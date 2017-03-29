<?php
class Ido_REST_Forms_Controller extends WP_REST_Controller{


	public function __construct(){
		$this->namespace = IDO_REST_SLUG . '/v' . IDO_REST_API_VERSION;
		$this->rest_base = 'ido_forms';
	}

	public function register_routes(){


		register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<id>[\d]+)', array(
			'args' => array(
				'id' => array(
					'description' => __( 'Unique identifier for the object.' ),
					'type'        => 'integer',
				),
			),
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_item' ),
			),
		));
	}

	public function get_item($request){

		$form_id           = $request['id'];

        $raw_all_fields = get_fields($form_id);

        $raw_text_fields = $raw_all_fields['text'];
        $raw_single_select_fields = $raw_all_fields['single_select'];
        $raw_multiple_select_fields = $raw_all_fields['multiple_select'];
        $raw_textarea_fields = $raw_all_fields['textarea'];

        $all_input_fields = array();
        $text_fields = array();
        $single_select_fields = array();
        $multiple_select_fields = array();
        $textarea_fields = array();

        if($raw_text_fields){
        	$text_fields = array_map(function($raw_text_field){
        		$raw_text_field['type'] = 'text';
        		return $raw_text_field;
        	}, $raw_text_fields);
        	$all_input_fields = array_merge($all_input_fields, $text_fields);
        }

        if($raw_single_select_fields){
        	$single_select_fields = array_map(function($raw_single_select_field){
        		$raw_single_select_field['type'] = 'singleSelect';
        		return $raw_single_select_field;
        	}, $raw_single_select_fields);
        	$all_input_fields = array_merge($all_input_fields, $single_select_fields);
        }

        if($raw_multiple_select_fields){
        	$multiple_select_fields = array_map(function($raw_multiple_select_field){
        		$raw_multiple_select_field['type'] = 'multipleSelect';
        		return $raw_multiple_select_field;
        	}, $raw_multiple_select_fields);
        	$all_input_fields = array_merge($all_input_fields, $multiple_select_fields);
        }

        if($raw_textarea_fields){
        	$textarea_fields = array_map(function($raw_textarea_field){
        		$raw_textarea_field['type'] = 'textarea';
        		return $raw_textarea_field;
        	}, $raw_textarea_fields);
        	$all_input_fields = array_merge($all_input_fields, $multiple_select_fields);
        }

		$form = $all_input_fields;

		$response  = rest_ensure_response($form);
		return $response;

	}
}