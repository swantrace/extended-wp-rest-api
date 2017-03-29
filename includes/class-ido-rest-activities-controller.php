<?php

class Ido_REST_Activities_Controller extends WP_REST_Posts_Controller{

	protected $post_type;
	// protected $meta;
	public function __construct($post_type='activity'){
		$this->post_type = $post_type;
		$this->namespace = IDO_REST_SLUG . '/v' . IDO_REST_API_VERSION;
		$obj = get_post_type_object($post_type);
		$this->rest_base = !empty($obj->rest_base)?$obj->rest_base:$obj->name;
		$this->meta = new WP_REST_Post_Meta_Fields( $this->post_type );
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

		$activity_id = (int)$request['id'];

		$raw_activity = get_post($activity_id);

		if(empty($raw_activity)){
			return new WP_Error( 'invalid_post', __( 'Invalid post ID.' ) );
		}

		$raw_content = $raw_activity->post_content;

        $dom = new DOMDocument('1.0', 'UTF-8');
        @$dom->loadHTML(mb_convert_encoding($raw_content, 'HTML-ENTITIES', 'UTF-8'));
        $finder = new DomXPath($dom);
        $content_object = $finder->query('//ul | //p | //img | //h1 | //h2 | //h3 | //h4 | //h5 | //h6 ');
        $length = $content_object->length;     
        $content_array = array();

        for ($i = 0; $i < $length; $i++) {
            $element  = $content_object->item($i);
            $tag_name = $element->tagName;
            $text     = $element->textContent; 
            switch ($tag_name) {
                case 'img':
                    $src = $element->attributes->getNamedItem('src')->nodeValue;
                    $content_array[] = ['img'=>$src];
                    break;
                case 'p':
                    if(!empty(trim($text))){
                        $content_array[] = ['p'=>$text];
                    }
                    break;
                case 'h1':
                    if(!empty(trim($text))){
                        $content_array[] = ['h1'=>$text];
                    }
                    break;
                case 'h2':
                    if(!empty(trim($text))){
                        $content_array[] = ['h2'=>$text];
                    }
                    break;
                case 'h3':
                    if(!empty(trim($text))){
                        $content_array[] = ['h3'=>$text];
                    }
                    break;
                case 'h4':
                    if(!empty(trim($text))){
                        $content_array[] = ['h4'=>$text];
                    }
                    break;
                case 'h5':
                    if(!empty(trim($text))){
                        $content_array[] = ['h5'=>$text];
                    }
                    break;
                case 'h6':
                    if(!empty(trim($text))){
                        $content_array[] = ['h6'=>$text];
                    }
                    break;
                case 'ul':
                    if(!empty(trim($text))){
                        $content_array[] = ['ul'=>$text];
                    }
                    break;
                default:
            }
        }

        $activity['id']                 = $activity_id;
        $activity['plain_title']        = $raw_activity->post_title;
        $activity['plain_text_content'] = $content_array;

        $form_id                        = get_field('related_form', $activity_id);

        if($form_id){
        	$form = $this->get_form_by_id($form_id);
        } else {
        	$form_id = 0;
        	$form = null;
        }

        $activity['form']['version']    = '1.0.0';
        $activity['form']['id']         = $form_id;
        $activity['form']['questions']  = $form;

		$response  = rest_ensure_response($activity);
		return $response;         
	}



	private function get_form_by_id($form_id){

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

		return $all_input_fields;
	}

}