<?php


add_action('save_post_place', 'idocalgary_update_location_table', 10, 3);

function idocalgary_update_location_table($post_id, $post, $update){
	global $wpdb;
	$location = get_field('location',$post_id);

	$latitude = $location['lat'];
	$longitude = $location['lng'];
	$address = $location['address'];

	if(!empty($location)){
		$wpdb->replace( 'wp_locations', 
			array(
				'post_id' => $post_id, 
				'latitude' => $latitude, 
				'longitude' => $longitude,
				'address' => $address 
			)
		);
	}
}

add_action('save_post', 'idocalgary_set_views_count_equal_zero', 10, 3);

function idocalgary_set_views_count_equal_zero($post_id, $post, $update){
	add_post_meta($post_id, '_count-views_all', 0, true);
}