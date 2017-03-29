<?php
class Ido_REST_Messages_Controller extends WP_REST_Controller{
	// function ido_register_message_endpoints{
	// 	register_rest_route('ido', '/v1', array(
	// 		'methods' => 'GET',
	// 		'callback'=> 'get_conversations'
	// 	));
	// }

	public function __construct(){
		$this->namespace = IDO_REST_SLUG . '/v' . IDO_REST_API_VERSION;
		$this->rest_base = 'messages';
	}

	public function register_routes(){
		register_rest_route($this->namespace, '/' . $this->rest_base, array(
			array(
				'methods'  => WP_REST_Server::READABLE,
				'callback' => array($this, 'get_items')
			),
			array(
				'methods' => WP_REST_Server::CREATABLE,
				'callback'=> array($this, 'create_item')
			)
		));

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
			// array(
			// 	'methods'             => WP_REST_Server::DELETABLE,
			// 	'callback'            => array( $this, 'delete_item' ),
			// )
		));
	}

	public function create_item($request){

		global $wpdb;

		$current_user_id = get_current_user_id();

		if(empty($current_user_id)){
			return new WP_Error('rest_not_logged_in',__('You are not currently logged in.'), array('status'=>401));
		}
		
		$recipient     = !empty($request['recipient'])?$request['recipient']:1;
		$message       = !empty($request['message'])?$request['message']:"用户{$current_user_id}戳了你一下";

		$um_messaging  = new UM_Messaging_Main_API();
		
		$conversation_id = $this->ido_create_conversation($recipient, $current_user_id, $message, $um_messaging);
		
		// Get conversation ordered by time and show only 1000 messages
		$raw_messages = array_reverse($wpdb->get_results("SELECT * FROM {$um_messaging->table_name2} WHERE conversation_id=$conversation_id ORDER BY time DESC LIMIT 0, 50"));

		$messages = array_map(function($raw_message) use($current_user_id, $um_messaging){		
			$message                    = array();
			$author_id                  = $raw_message->author;
			$message['author_id']       = $author_id;
			$message['author_name']     = get_the_author_meta( 'display_name', $author_id );  
			$message['author_avatar']   = $this->ido_get_avatar($author_id);
			$message['content']         = $raw_message->content;
			$message_timestamp          = mysql2date( 'U', $raw_message->time ); 
			$message['time']            = $um_messaging->human_time_diff($message_timestamp);
			$message['is_current_user'] = ($author_id == $current_user_id);
			$message['is_unread']       = !($raw_message->status);			
			return $message;
		}, $raw_messages);

		$conversation = array();
		$conversation['id']       = $conversation_id;
		$conversation['messages'] = $messages;

		$response  = rest_ensure_response($conversation);
		return $response;
	}

	public function get_items($request){

		global $wpdb;

		$current_user_id = get_current_user_id();

		if ( empty( $current_user_id ) ) {
			return new WP_Error( 'rest_not_logged_in', __( 'You are not currently logged in.' ), array( 'status' => 401 ) );
		}

		$um_messaging = new UM_Messaging_Main_API();
		$raw_conversations = !empty($um_messaging->get_conversations($current_user_id))?$um_messaging->get_conversations($current_user_id):array();

        //{ ["conversation_id"]=> string(1) "1" ["user_a"]=> string(1) "1" ["user_b"]=> string(1) "3" ["last_updated"]=> string(19) "2017-02-14 20:39:53" } }

		$conversations = array_map(function($raw_conversation) use ($current_user_id, $um_messaging,$wpdb){
			$conversation = array();
 			$other_user_id = ($raw_conversation->user_a == $current_user_id)?$raw_conversation->user_b:$raw_conversation->user_a;
 			$conversation_id = $raw_conversation->conversation_id;	
			$conversation['conversation_id'] = $conversation_id;	
 			$conversation['other_user_id'] = $other_user_id;
			$conversation['other_user_avatar'] = $this->ido_get_avatar($other_user_id);
			$conversation['other_user_name'] = get_the_author_meta( 'display_name', $other_user_id );
			$last_updated_timestamp = mysql2date( 'U', $raw_conversation->last_updated ); 
			$conversation['last_updated'] = $um_messaging->human_time_diff($last_updated_timestamp);
			$conversation['is_unread'] = $um_messaging->unread_conversation( $conversation_id, $current_user_id );
			$conversation['unread_count'] = $this->ido_get_unread_count($current_user_id, $other_user_id, $um_messaging);
			
			//$sql_statement = "SELECT content FROM {$um_messaging->table_name2} WHERE conversation_id={$conversation_id} AND recipient={$current_user_id} ORDER BY time DESC LIMIT 1";
			$sql_statement = "SELECT content FROM {$um_messaging->table_name2} WHERE conversation_id={$conversation_id} ORDER BY time DESC LIMIT 1";

			$raw_last_message_result = $wpdb->get_results($sql_statement, ARRAY_N);
			if(!empty($raw_last_message_result)){
				$conversation['last_message'] = $raw_last_message_result[0][0];
			} else {
				$conversation['last_message'] = '尚未答复';
			}
			return $conversation;

		}, $raw_conversations);

		$response  = rest_ensure_response($conversations);
		return $response;


	}

	public function get_item($request){

		global $wpdb;

		$current_user_id = get_current_user_id();
		
		if(empty($current_user_id)){
			return new WP_Error('rest_not_logged_in', __('You are not currently logged in.'), array('status'=>401));
		}

		$conversation_id   = $request['id'];

		$page              = !empty($request['page'])?(int)$request['page']:1;
		$messages_per_page = !empty($request['per_page'])?(int)$request['per_page']:50;

		$um_messaging    = new UM_Messaging_Main_API(); 

		$related_users_array = $wpdb->get_results("SELECT user_a, user_b FROM {$um_messaging->table_name1} WHERE conversation_id={$conversation_id}", ARRAY_N);

		if(!empty($related_users_array)){
			$related_users = $related_users_array[0];
		} else {
			return new WP_Error('rest_cannot_find', __('There is no this conversation with the id you provide'), array('status'=>404));			
		}

		if(!in_array($current_user_id, $related_users)){
			return new WP_Error('rest_cannot_read', __('You are not allowed to read other people\'s conversation'), array('status'=>401));
		}

		// change status of all the messages that is in current conversation and recipient is current user
		$wpdb->query("UPDATE {$um_messaging->table_name2} SET status=1 WHERE conversation_id={$conversation_id} AND recipient={$current_user_id}");

		$start = $messages_per_page*($page-1);

		$number = $messages_per_page;

		$raw_messages= array_reverse($wpdb->get_results("SELECT * FROM {$um_messaging->table_name2} WHERE conversation_id={$conversation_id} ORDER BY time DESC LIMIT $start, $number" ));

		$messages = array_map(function($raw_message) use($current_user_id, $um_messaging){		
			$message                    = array();
			$author_id                  = $raw_message->author;
			$message['author_id']       = $author_id;
			$message['author_name']     = get_the_author_meta( 'display_name', $author_id );  
			$message['author_avatar']   = $this->ido_get_avatar($author_id);
			$message['content']         = $raw_message->content;
			$message_timestamp          = mysql2date( 'U', $raw_message->time );
			$message['timestamp']       = $message_timestamp; 
			$message['time']            = $um_messaging->human_time_diff($message_timestamp);
			$message['is_current_user'] = ($author_id == $current_user_id);
			$message['is_unread']       = !($raw_message->status);			
			return $message;
		}, $raw_messages);

		$conversation = array();
		$conversation['id']       = $conversation_id;
		$conversation['messages'] = $messages;

		$response  = rest_ensure_response($conversation);
		return $response;
	}

	// public function delete_item($request){

	// }

	private function ido_get_avatar($user_id){
		
		if(get_user_meta($user_id, 'profile_photo', true) == 'profile_photo.jpg'):

       		return 'http://52calgary.com/wp-content/uploads/ultimatemember/' . $user_id . '/profile_photo-40.jpg';

    	else:

        	return 'http://gravatar.com/avatar/d4180cb63752fd75540135b382ff6039?s=100';

    	endif;
	}

	// user2 should be current_user_id or author, user1 should be other_user_id or recipient

	private function ido_create_conversation(int $user1, int $user2, string $content, UM_Messaging_Main_API $um_messaging ) {
		global $wpdb;
		
		$conversation_id = false;

		// Test for previous conversation
		$conversation_id = $wpdb->get_var(
			$wpdb->prepare("SELECT conversation_id FROM {$um_messaging->table_name1} WHERE user_a = %d AND user_b = %d LIMIT 1",
				$user1,
				$user2
			)
		);

		if ( empty( $conversation_id ) ) {
			$conversation_id = $wpdb->get_var(
				$wpdb->prepare("SELECT conversation_id FROM {$um_messaging->table_name1} WHERE user_a = %d AND user_b = %d LIMIT 1",
					$user2,
					$user1
				)
			);
		}

		// Build new conversation
		if ( ! $conversation_id ) {

			$wpdb->insert(
				$um_messaging->table_name1,
				array(
					'user_a' => $user1,
					'user_b' => $user2
				)
			);

			$conversation_id = $wpdb->insert_id;

			do_action('um_after_new_conversation', $user1, $user2, $conversation_id );

		} else {

			do_action('um_after_existing_conversation', $user1, $user2, $conversation_id );

		}

		// Insert message
		$wpdb->update(
			$um_messaging->table_name1,
			array(
				'last_updated' 			=> current_time( 'mysql', true ),
			),
			array(
				'conversation_id' 		=> $conversation_id,
			)
		);

		$wpdb->insert(
				$um_messaging->table_name2,
				array(
					'conversation_id' => $conversation_id,
					'time' => current_time( 'mysql' ),
					'content' => strip_tags( $content ),
					'status' => 0,
					'author' => $user2,
					'recipient' => $user1
				)
		);

		$um_messaging->update_user( $user2 );

		$hidden = (array) get_user_meta( $user1, '_hidden_conversations', true );
		if ( in_array( $conversation_id, $hidden ) ) {
			$hidden = array_diff($hidden, array( $conversation_id ) );
			update_user_meta( $user1, '_hidden_conversations', $hidden );
		}

		$hidden = (array) get_user_meta( $user2, '_hidden_conversations', true );
		if ( in_array( $conversation_id, $hidden ) ) {
			$hidden = array_diff($hidden, array( $conversation_id ) );
			update_user_meta( $user2, '_hidden_conversations', $hidden );
		}

		do_action('um_after_new_message', $user1, $user2, $conversation_id );

		$wpdb->close();
		$wpdb->db_connect();

		return $conversation_id;

	}

	private function ido_get_unread_count(int $current_user_id, int $other_user_id, UM_Messaging_Main_API $um_messaging){
		global $wpdb;

		$count = $wpdb->get_var("SELECT COUNT(message_id) FROM {$um_messaging->table_name2} WHERE recipient={$current_user_id} AND author={$other_user_id} AND status=0");

		return $count;

	}
}