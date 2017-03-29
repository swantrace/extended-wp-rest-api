<?php
class Ido_REST_Registrations_Controller extends WP_REST_Controller{


	public function __construct(){
		$this->namespace = IDO_REST_SLUG . '/v' . IDO_REST_API_VERSION;
		$this->rest_base = 'registrations';
	}

	public function register_routes() {
		register_rest_route($this->namespace, '/' . $this->rest_base, array(
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'create_item' ),
			)
		));
	}

	public function create_item($request){
		
		$form_id = $request['form'];

		if(empty($form_id)){
			return new WP_Error('no_form_id', __('You have to provide a form id.'));
		}

		$json_content = $request->get_body();

		$fields = json_decode($json_content, true);

		if(empty($fields)){
			// return new WP_Error('no_form_content', __('You have to provide form content.'));
			$success = array('success'=>false);
			return rest_ensure_response($success);
		}

		$form_url = 'http://' . $_SERVER['HTTP_HOST'] . '/wp-json/ido/v1/ido_forms/' . $form_id;

		$form = wp_remote_get($form_url,  array( 'timeout' => 120, 'httpversion' => '1.1' ));
		
		if(is_array($form)) {
		  $form_header = $form['headers']; 
		  $form_body = json_decode($form['body'], true);
		}

		foreach ($form_body as $form_field) {
			if($form_field['is_required']){
				$title = $form_field['title'];
				if(!array_key_exists($title, $fields) || empty(trim($fields[$title]))){
					$success = array('success'=>false);
					return rest_ensure_response($success);					
				}
			}
		}

		$content  = '<ul>';

		foreach ($fields as $key => $value) {
			$content .= '<li>' . $key . ': ' . $value . '</li>';
		}

		$content .= '</ul>';

		$author = get_current_user_id();
		if(!$author){
			$author = 1;
		}

		$form      = get_post($form_id);
		$form_name = $form->post_title;
		$post_title= $form_name . '_' . time();

		$postarr = array(
			'post_author' => $author,
			'post_title'  => $post_title,
			'post_type'   => 'registration',
			'post_status' => 'publish'
		);

		$registration_id = wp_insert_post($postarr, true);

		if(is_int($registration_id)){
			update_field('ido_form', $form_id, $registration_id);
			update_field('content', $content, $registration_id);
			$success = array('success'=>true);
			return rest_ensure_response($success);;
		} else {
			$success = array('success'=>false);
			return rest_ensure_response($success);
		}
	}
}