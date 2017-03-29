<?php

add_action('rest_api_init', 'idocalgary_register_fields');
function idocalgary_register_fields(){


    // 广告部分
    register_rest_field('advert_category',
        'icon_url',
        array(
            'get_callback'    =>'idocalgary_get_category_icon',
            'update_callback' => null,
            'schema'          => null,
        )
    );

    register_rest_field('advert',
        'conversation_id',
        array(
            'get_callback'    => 'idocalgary_get_advert_conversation_id',
            'update_callback' => null,
            'schema'          => null,        
        )
    );

    register_rest_field('advert',
        'is_featured',
        array(
            'get_callback'    => 'idocalgary_get_if_is_featured',
            'update_callback' => null,
            'schema'          => null,        
        )
    );

    register_rest_field('advert',
        'author',
        array(
            'get_callback'    => 'idocalgary_get_topic_author',
            'update_callback' => null,
            'schema'          => null,        
        )
    );

    register_rest_field( 'advert',
        'plain_title',
        array(
            'get_callback'    => 'idocalgary_get_plain_title',
            'update_callba'   => null,
            'schema'          => null,
        )
    );

    register_rest_field('advert',
        'content',
        array(
            'get_callback'    => 'idocalgary_get_content',
            'update_callback' => 'idocalgary_update_content',
            'schema'          => null,
        )
    );

    register_rest_field('advert',
        'price',
        array(
            'get_callback'    => 'idocalgary_get_price',
            'update_callback' => 'idocalgary_update_price',
            'schema'          => null,
        )
    );

    register_rest_field('advert',
        'comments',
        array(
            'get_callback'    => 'idocalgary_get_comments',
            'update_callback' => null,
            'schema'          => null,
        )
    );

    register_rest_field('advert',
        'comments_count',
        array(
            'get_callback'    => 'idocalgary_get_comments_count',
            'update_callback' => null,
            'schema'          => null, 
        )
    );

    register_rest_field('advert',
        'gallery',
        array(
            'get_callback'    => 'idocalgary_get_advert_gallery',
            'update_callback' => null,
            'schema'          => null,
        )
    );

    register_rest_field('advert',
        'featured_image_url',
        array( 
            'get_callback' => 'idocalgary_get_advert_featured_image_url',
            'update_callback' => null,
            'schema' => null,
        )
    );


    register_rest_field('advert',
        'human_readable_time',
        array(
            'get_callback'    => 'idocalgary_get_human_readable_time',
            'update_callback' => null,
            'schema'          => null,
        )
    );

    register_rest_field('advert',
        'terms',
        array(
            'get_callback'    => 'idocalgary_get_terms',
            'update_callback' => null,
            'schema'          => null,  
        )
    );

    register_rest_field('advert',
        'img_ids',
        array(
            'get_callback'    => 'idocalgary_get_advert_img_ids',
            'update_callback' => 'idocalgary_update_advert_img_ids',
            'schema'          => null,  
        )
    );

    register_rest_field('advert',
        'views_count',
        array(
            'get_callback'    => 'idocalgary_get_views_count',
            'update_callback' => null,
            'schema'          => null, 
        )
    );

    // register_rest_field('advert',
    //     'ad_img',
    //     array(
    //         'get_callback'    => 'idocalgary_get_ad_img',
    //         'update_callback' => null,
    //         'schema'          => null
    //     )
    // );

    register_rest_field('advert',
        'phone',
        array(
            'get_callback'    => 'idocalgary_get_phone',
            'update_callback' => 'idocalgary_update_phone',
            'schema'          => null,
        )
    );

    register_rest_field('advert',
        'email',
        array(
            'get_callback'    => 'idocalgary_get_email',
            'update_callback' => 'idocalgary_update_email',
            'schema'          => null,
        )
    );

    register_rest_field('advert',
        'price',
        array(
            'get_callback'    => 'idocalgary_get_price',
            'update_callback' => 'idocalgary_update_price',
            'schema'          => null,
        )
    );

    register_rest_field('advert',
        'favorites_count',
        array(
            'get_callback'    => 'idocalgary_get_favorites_count',
            'update_callback' => null,
            'schema'          => null, 
        )
    );

    register_rest_field('advert',
        'is_favorite',
        array(
            'get_callback'    => 'idocalgary_get_is_favorite',
            'update_callback' => 'idocalgary_update_is_favorite',
            'schema'          => null, 
        )
    );

    register_rest_field('advert',
        'users_who_favorited',
        array(
            'get_callback'   => 'idocalgary_get_users_who_favorited',
            'update_callback'=> null,
            'schema'         => null,
        )
    );


    // 黄页部分

    register_rest_field('place',
        'price',
        array(
            'get_callback'    => 'idocalgary_get_price',
            'update_callback' => null,
            'schema'          => null,
        )
    );

    register_rest_field('place',
        'img_ids',
        array(
            'get_callback'    => 'idocalgary_get_img_ids',
            'update_callback' => 'idocalgary_update_img_ids',
            'schema'          => null,  
        )
    );


    register_rest_field('place_category',
        'icon_url',
        array(
            'get_callback'    => 'idocalgary_get_category_icon',
            'update_callback' => null,
            'schema'          => null,
        )
    );

    register_rest_field('place_category',
        'child',
        array(
            'get_callback'    => 'idocalgary_get_child_categories',
            'update_callback' => null,
            'schema'          => null,
        )
    );

    register_rest_field('place',
        'location',
        array(
            'get_callback'    => 'idocalgary_get_location',
            'update_callback' => null,
            'schema'          => null,
        )
    );

    register_rest_field('place',
        'address',
        array(
            'get_callback'    => 'idocalgary_get_address',
            'update_callback' => null,
            'schema'          => null,
        )
    );

    register_rest_field('place',
        'latitude',
        array(
            'get_callback'    => 'idocalgary_get_latitude',
            'update_callback' => null,
            'schema'          => null,
        )
    );

    register_rest_field('place',
        'longitude',
        array(
            'get_callback'    => 'idocalgary_get_longitude',
            'update_callback' => null,
            'schema'          => null,
        )
    );

    register_rest_field('place',
        'distance',
        array(
            'get_callback'    => 'idocalgary_get_distance',
            'update_callback' => null,
            'schema'          => null,
        )
    );

    register_rest_field('place',
        'featured_image_url',
        array(
            'get_callback'    => 'idocalgary_get_featured_image_url',
            'update_callback' => null,
            'schema'          => null,
        )
    );

    register_rest_field('place',
        'is_featured',
        array(
            'get_callback'    => 'idocalgary_get_if_is_featured',
            'update_callback' => null,
            'schema'          => null,        
        )
    );

    register_rest_field('place',
        'business_hour',
        array(
            'get_callback'    => 'idocalgary_get_business_hour',
            'update_callback' => null,
            'schema'          => null,  
        )
    );

    register_rest_field('place',
        'terms',
        array(
            'get_callback'    => 'idocalgary_get_terms',
            'update_callback' => null,
            'schema'          => null,  
        )
    );

    register_rest_field('place',
        'comments',
        array(
            'get_callback'    => 'idocalgary_get_comments',
            'update_callback' => null,
            'schema'          => null,
        )
    );

    register_rest_field('place',
        'favorites_count',
        array(
            'get_callback'    => 'idocalgary_get_favorites_count',
            'update_callback' => null,
            'schema'          => null, 
        )
    );

    register_rest_field('place',
        'is_favorite',
        array(
            'get_callback'    => 'idocalgary_get_is_favorite',
            'update_callback' => 'idocalgary_update_is_favorite',
            'schema'          => null, 
        )
    );

    register_rest_field('place',
        'views_count',
        array(
            'get_callback'    => 'idocalgary_get_views_count',
            'update_callback' => null,
            'schema'          => null, 
        )
    );

    register_rest_field('place',
        'comments_count',
        array(
            'get_callback'    => 'idocalgary_get_comments_count',
            'update_callback' => null,
            'schema'          => null, 
        )
    );

    register_rest_field('place',
        'plain_text_content',
        array(
            'get_callback'    => 'idocalgary_get_plain_text_content',
            'update_callback' => null,
            'schema'          => null,
        )
    );

    register_rest_field('place',
        'users_who_favorited',
        array(
            'get_callback'   => 'idocalgary_get_users_who_favorited',
            'update_callback'=> null,
            'schema'         => null,
        )
    );

    register_rest_field('place',
        'rating_average',
        array(
            'get_callback'    => 'idocalgary_get_rating_average',
            'update_callback' => null,
            'schema'          => null, 
        )
    );

    register_rest_field( 'place',
        'plain_title',
        array(
            'get_callback'    => 'idocalgary_get_plain_title',
            'update_callba'   => null,
            'schema'          => null,
        )
    );

    register_rest_field('place',
        'phone',
        array(
            'get_callback'    => 'idocalgary_get_phone',
            'update_callback' => null,
            'schema'          => null,
        )
    );

    register_rest_field('place',
        'email',
        array(
            'get_callback'    => 'idocalgary_get_email',
            'update_callback' => null,
            'schema'          => null,
        )
    );

    register_rest_field('place',
        'website',
        array(
            'get_callback'    => 'idocalgary_get_website',
            'update_callback' => null,
            'schema'          => null,
        )
    );

    register_rest_field('place',
        'gallery',
        array(
            'get_callback'    => 'idocalgary_get_gallery',
            'update_callback' => null,
            'schema'          => null,
        )
    );

    register_rest_field('place',
        'human_readable_time',
        array(
            'get_callback' => 'idocalgary_get_human_readable_time',
            'update_callback' => null,
            'schema' => null
        )
    );

    register_rest_field('place',
        'wechat',
        array(
            'get_callback' => 'idocalgary_get_wechat',
            'update_callback' => null,
            'schema' => null
        )
    );

    // register_rest_field('place',
    //     'ad_img',
    //      array(
    //         'get_callback'    => 'idocalgary_get_ad_img', 
    //         'update_callback' => null,
    //         'schema'          => null
    //     )
    // );



    // 新闻部分

    register_rest_field('post',
        'users_who_favorited',
        array(
            'get_callback' => 'idocalgary_get_users_who_favorited',
            'update_callback' => null,
            'shcema'          => null
        )
    );

    register_rest_field('post',
        'featured_image_url',
        array(
            'get_callback'    => 'idocalgary_get_featured_image_url',
            'update_callback' => null,
            'schema'          => null,
        )
    );

    register_rest_field( 'post',
        'plain_title',
        array(
            'get_callback'    => 'idocalgary_get_plain_title',
            'update_callba'   => null,
            'schema'          => null,
        )
    );

    register_rest_field( 'post' , 
        'news_source',
        array(
            'get_callback'    => 'idocalgary_get_news_source',
            'update_callback' => null,
            'schema'          => null,
        )
    );

    register_rest_field('post',
        'plain_text_content',
        array(
            'get_callback'    => 'idocalgary_get_plain_text_content',
            'update_callback' => null,
            'schema'          => null,
        )
    );

    register_rest_field('post',
        'comments',
        array(
            'get_callback'    => 'idocalgary_get_comments',
            'update_callback' => null,
            'schema'          => null,
        )
    );

    register_rest_field('post',
        'comments_count',
        array(
            'get_callback'    => 'idocalgary_get_comments_count',
            'update_callback' => null,
            'schema'          => null, 
        )
    );

    register_rest_field('post',
        'is_favorite',
        array(
            'get_callback'    => 'idocalgary_get_is_favorite',
            'update_callback' => 'idocalgary_update_is_favorite',
            'schema'          => null,
        )
    );

    register_rest_field('post',
        'is_like',
        array(
            'get_callback'    => 'idocalgary_get_is_like',
            'update_callback' => 'idocalgary_update_is_like',
            'schema'          => null,
        )
    );

    register_rest_field('post',
        'is_dislike',
        array(
            'get_callback'    => 'idocalgary_get_is_dislike',
            'update_callback' => 'idocalgary_update_is_dislike',
            'schema'          => null,
        )
    );

    register_rest_field('post',
        'likes_count',
        array(
            'get_callback'    => 'idocalgary_get_likes_count',
            'update_callback' => null,
            'schema'          => null,
        )
    );

    register_rest_field('post',
        'dislikes_count',
        array(
            'get_callback'    => 'idocalgary_get_dislikes_count',
            'update_callback' => null,
            'schema'          => null,
        )
    );

    register_rest_field('post',
        'views_count',
        array(
            'get_callback'    => 'idocalgary_get_views_count',
            'update_callback' => null,
            'schema'          => null, 
        )
    );

    register_rest_field('post',
        'human_readable_time',
        array(
            'get_callback' => 'idocalgary_get_human_readable_time',
            'update_callback' => null,
            'schema' => null
        )
    );

    // register_rest_field('post',
    //     'ad_url',
    //      array(
    //         'get_callback'    => 'idocalgary_get_ad_url', 
    //         'update_callback' => null,
    //         'schema'          => null
    //     )
    // );


    // 发现

    register_rest_field('topic',
        'favoriters',
        array(
            'get_callback' => 'idocalgary_get_topic_favoriters',
            'update_callback' => null,
            'schema'   => null,
        )
    );

    // register_rest_field('topic',
    //     'raw_favoriters',
    //     array(
    //         'get_callback' => 'idocalgary_get_raw_topic_favoriters',
    //         'update_callback' => null,
    //         'schema'   => null,
    //     )
    // );

    register_rest_field('topic',
        'favorites_count',
        array(
            'get_callback'      => 'idocalgary_get_topic_favorites_count',
            'udpate_callback'   => null,
            'schema'            => null,
        )
    );

    register_rest_field('topic',
        'is_favorite',
        array(
            'get_callback'     => 'idocalgary_get_topic_is_favorite',
            'update_callback'  => 'idocalgary_update_topic_is_favorite',
            'schema'           => null
        )
    );

    register_rest_field('topic',
        'topic_tags_array',
        array(
            'get_callback'     => 'idocalgary_get_topic_tags_array',
            'update_callback'  => null,
            'schema'           => null 
        )
    );

    register_rest_field('topic',
        'plain_text_content',
        array(
            'get_callback'    => 'idocalgary_get_plain_text_content',
            'update_callback' => null,
            'schema'          => null
        )
    );

    register_rest_field('topic',
        'images',
        array(
            'get_callback' => 'idocalgary_get_topic_images',
            'update_callback' => null,
            'schema' => null
        )
    );

    register_rest_field('topic',
        'human_readable_time',
        array(
            'get_callback' => 'idocalgary_get_human_readable_time',
            'update_callback' => null,
            'schema' => null
        )
    );

    register_rest_field('topic',
        'replies_count',
        array(
            'get_callback' => 'idocalgary_get_replies_count',
            'update_callback' => null,
            'schema' => null
        )
    );

    register_rest_field('topic',
        'replies',
        array(
            'get_callback' => 'idocalgary_get_replies',
            'update_callback' => null,
            'schema' => null
        )
    );

    register_rest_field('topic',
        'shares_count',
        array(
            'get_callback' => 'idocalgary_get_shares_count',
            'update_callback' => 'idocalgary_update_shares_count',
            'schema' => null
        )
    );

    register_rest_field('topic',
        'views_count',
        array(
            'get_callback' => 'idocalgary_get_views_count',
            'update_callback' => null,
            'schema' => null
        )
    );

    register_rest_field('topic',
        'featured_image_url',
        array(
            'get_callback' => 'idocalgary_get_topic_featured_image_url',
            'update_callback' => null,
            'schema' => null
        )
    );

    register_rest_field('topic',
        'img_ids',
        array(
            'get_callback'    => 'idocalgary_get_topic_img_ids',
            'update_callback' => 'idocalgary_update_topic_img_ids',
            'schema'          => null,  
        )
    );


    register_rest_field('topic',
        'author',
        array(
            'get_callback'    => 'idocalgary_get_topic_author',
            'update_callback' => null,
            'schema'          => null,  
        )
    );


    register_rest_field('topic',
        'is_like',
        array(
            'get_callback'    => 'idocalgary_get_is_like',
            'update_callback' => 'idocalgary_update_is_like',
            'schema'          => null,
        )
    );

    register_rest_field('topic',
        'likes_count',
        array(
            'get_callback'    => 'idocalgary_get_likes_count',
            'update_callback' => null,
            'schema'          => null,
        )
    );




    // 广告位

    // register_rest_field('ad', 
    //     'ad_image',
    //     array(
    //         'get_callback' => 'idocalgary_get_ad_image',
    //         'update_callback' => null,
    //         'schema' => null
    //     )
    // );

    // register_rest_field('ad',
    //     'ad_link',
    //     array(
    //         'get_callback' => 'idocalgary_get_ad_link',
    //         'update_callback' => null,
    //         'schema' => null
    //     )
    // );

    register_rest_field('ad', 
        'ads',
        array(
            'get_callback' => 'idocalgary_get_ads',
            'update_callback' => null,
            'schema' => null
        )
    );

    register_rest_field('ad',
        'ad_position',
        array(
            'get_callback' => 'idocalgary_get_ad_position',
            'update_callback' => null,
            'schema' => null
        )
    );


    // 用户 users
    register_rest_field('user',
        'followers',
        array(
            'get_callback'      => 'idocalgary_get_followers',
            'update_callback'   => 'idocalgary_update_followers',
            'schema'            => null
        )
    );

    register_rest_field('user',
        'followings',
        array(
            'get_callback'    => 'idocalgary_get_followings',
            'update_callback' => 'idocalgary_udpate_followings',
            'schema'          => null
        )
    );

    register_rest_field('user',
        'followers_count',
        array(
            'get_callback' => 'idocalgary_get_followers_count',
            'update_callback' => null,
            'schema' => null
        )
    );

    register_rest_field('user',
        'followings_count',
        array(
            'get_callback'=> 'idocalgary_get_followings_count',
            'update_callback' => null,
            'schema' => null
        )
    );

    // register_rest_field('user',
    //     'raw_avatar_url',
    //     array(
    //         'get_callback' => 'idocalgary_get_raw_avatar_url',
    //         'update_callback' => null,
    //         'schema' => null
    //     )
    // );

    register_rest_field('user',
        'avatar_url',
        array(
            'get_callback' => 'idocalgary_get_avatar_url',
            'update_callback' => null,
            'schema' => null
        )
    );


    register_rest_field('user',
        'user_email',
        array(
            'get_callback' => 'idocalgary_get_user_email',
            'update_callback' => null,
            'schema' => null
        )
    );

    // 评论
    register_rest_field('comment',
        'user_profile_url',
        array(
            'get_callback' => 'idocalgary_get_comment_user_profile_url',
            'update_callback' => null,
            'schema' => null
        )
    );
}

function idocalgary_get_user_email($object, $field_name, $request){
    $user_id = $object['id'];
    return get_userdata($user_id)->user_email;
}

function idocalgary_get_comment_user_profile_url($object, $field_name, $request){
    
    $user_id = $object['author'];

    if(get_user_meta($user_id, 'profile_photo', true) == 'profile_photo.jpg'):

        return 'http://52calgary.com/wp-content/uploads/ultimatemember/' . $user_id . '/profile_photo-40.jpg';

    else:

        return 'http://gravatar.com/avatar/d4180cb63752fd75540135b382ff6039?s=100';

    endif;    
}

function idocalgary_get_avatar_url($object, $field_name, $request){

    $user_id = $object['id'];

    if(get_user_meta($user_id, 'profile_photo', true) == 'profile_photo.jpg'):

        return 'http://52calgary.com/wp-content/uploads/ultimatemember/' . $user_id . '/profile_photo-40.jpg';

    else:

        return 'http://gravatar.com/avatar/d4180cb63752fd75540135b382ff6039?s=100';

    endif;

    // $avatar_img_tag = get_avatar( $object['id'], 24 );

    // $dom = new DOMDocument('1.0', 'UTF-8');

    // @$dom->loadHTML(mb_convert_encoding($avatar_img_tag, 'HTML-ENTITIES', 'UTF-8'));

    // $finder = new DomXPath($dom);

    // $avatar_url = $finder->query('//img')->item(0)->attributes->getNamedItem('src')->nodeValue;

    // return $avatar_url; 

}

// function idocalgary_get_avatar_url($object, $field_name, $request){
    
//     if(get_site_url() === 'http://52calgary.com'):
//         return $object['raw_avatar_url'];
//     endif;
    
//     $response = wp_remote_get('http://52calgary.com/wp-json/ido/v1/users/' . $object['id'], array('timeout'=> 30));
//     $user =  json_decode( wp_remote_retrieve_body( $response ), true );
    
//     return $user['raw_avatar_url'];
// }


function idocalgary_get_followers($object, $field_name, $request){
    $user_id =  $object['id'];
    $current_followers = get_user_meta($user_id, '_bbpresslist_followers', true );
    if(empty($current_followers)):
        return array();
    endif;
    return $current_followers; 
}

function idocalgary_get_followings($object, $field_name, $request){
    $user_id = $object['id'];
    $current_followings = get_user_meta($user_id, '_bbpresslist_following', true);
    if(empty($current_followings)):
        return array();
    endif;
    return $current_followings;
}

function idocalgary_get_followers_count($object, $field_name, $request){
    return count($object['followers']);
}

function idocalgary_get_followings_count($object, $field_name, $request){
    return count($object['followings']);
}

function idocalgary_update_followers($value, $object, $field_name){  
    
    $user_id = (string)get_current_user_id();
    $follower_id = $value;

    $new_follow = new BBPressList_User;
    $current_followers = get_user_meta($user_id, '_bbpresslist_followers', true);
    if(empty($current_followers)):
        $current_followers = array();
    endif;

    if(in_array($follower_id, $current_followers)):
        $new_follow->remove_user_to_list($follower_id, $user_id);
    else:
        $new_follow->add_user_to_list($follower_id,$user_id );
    endif;
}

function idocalgary_udpate_followings($value, $object, $field_name){
   
    $user_id = (string)get_current_user_id();
    $following_id = $value;

    $new_follow = new BBPressList_User;
    $current_followings = get_user_meta($user_id, '_bbpresslist_following', true);
    if(empty($current_followings)):
        $current_followings = array();
    endif;

    if(in_array($following_id, $current_followings)):
        $new_follow->remove_user_to_list($user_id, $following_id);
    else:
        $new_follow->add_user_to_list($user_id, $following_id);
    endif;
}





// function idocalgary_get_ad_image($object, $field_name, $request){
//     return get_field('ad_image', $object['id'], true );
// }

// function idocalgary_get_ad_link($object, $field_name, $request){
//     $ad_link_type = get_field('ad_link_type', $object['id'], true );
    
//     $ad_link = array();

//     $ad_link['type'] = $ad_link_type;

//     $ad_link['resource'] = $ad_link_type == "external"? get_field('ad_external_link', $object['id'], true) : get_field( $ad_link_type . '_item', $object['id'], true);

//     return $ad_link;
// }

function idocalgary_get_ads($object, $field_name, $request){
    
    $raw_ads = get_field('ad', $object['id'], true );
    
    $ads = array();

    foreach ($raw_ads as $raw_ad):
        $ad['ad_image'] = $raw_ad['ad_image'];
        $ad['type'] = $raw_ad['ad_link_type'];
        if($ad['type'] == 'nolink'):
            $ad['resource'] = null;
        endif;
        if($ad['type'] == 'post' || $ad['type'] == 'place' || $ad['type'] == 'advert' || $ad['type'] == 'topic' || $ad['type'] == 'activity'):
            $resource_type = $ad['type'] . '_item';
            $ad['resource'] = $raw_ad[$resource_type];
        endif;
        if($ad['type'] == 'external'):
            $ad['resource'] = $raw_ad['ad_external_link'];
        endif;
        $ads[] = $ad;
    endforeach;
    
    return $ads;
}

function idocalgary_get_ad_position($object, $field_name, $request){
    return get_field('ad_position', $object['id'], true );
}


function idocalgary_get_content($object, $field_name, $request){
    return preg_replace('/\s+/', '', strip_tags(get_field('content', $object['id'], true)));
}


function idocalgary_update_content($value, $object, $field_name){
    update_field('content', $value, $object->ID);
}


function idocalgary_get_shares_count($object, $field_name, $request){
    
    $shares_count = get_post_meta($object['id'], 'shares_count', true);

    if(empty($shares_count)):
        return 0;
    endif;
    return $shares_count; 
}

function idocalgary_update_shares_count($value, $object, $field_name){

    if(isset($value) && $value ==='true'){
        if (!add_post_meta($object->ID, 'shares_count', 1, true)) {
            $shares_count = (int)get_post_meta($object->ID, 'shares_count', true);
            $shares_count++;
            update_post_meta($object->ID, 'shares_count', $shares_count);
        } 
    }
}


function idocalgary_get_topic_favoriters($object, $field_name, $request){
    
    $favoriter_ids = (array)bbp_get_topic_favoriters($object['id']);
    
    $favoriters = array();

    foreach ($favoriter_ids as $favoriter_id) {
        
        $favoriter['id'] = $favoriter_id;
        
        $favoriter['user_profile_url'] = (get_user_meta($favoriter_id, 'profile_photo', true) == 'profile_photo.jpg')?'http://52calgary.com/wp-content/uploads/ultimatemember/' . $favoriter_id . '/profile_photo-40.jpg':'http://gravatar.com/avatar/d4180cb63752fd75540135b382ff6039?s=100';
        
        $favoriters[] = $favoriter;
    }
    return $favoriters;
}

// function idocalgary_get_topic_favoriters($object, $field_name, $request){

//     if(get_site_url() === 'http://52calgary.com'):
//         return $object['raw_favoriters'];
//     endif;
    
//     $response = wp_remote_get('http://52calgary.com/wp-json/ido/v1/topics/' . $object['id'], array('timeout'=> 30));
//     $topic =  json_decode( wp_remote_retrieve_body( $response ), true );
    
//     return $topic['raw_favoriters'];
    
// }

function idocalgary_get_topic_author($object, $field_name, $request){
    
    $current_user_id = get_current_user_id();
    
    $user = array();
    $post = get_post($object['id']);
    
    $user['user_id'] = $post->post_author;
    $user['user_name'] = get_the_author_meta( 'display_name', $user['user_id'] );

    if(get_user_meta($user['user_id'], 'profile_photo', true) == 'profile_photo.jpg'):

        $user['user_profile_url'] = 'http://52calgary.com/wp-content/uploads/ultimatemember/' . $user['user_id'] . '/profile_photo-40.jpg';

    else:

        $user['user_profile_url'] = 'http://gravatar.com/avatar/d4180cb63752fd75540135b382ff6039?s=100';

    endif;
    
    // $profile_img = get_avatar( $user['user_id'], 32 );

    // $dom = new DOMDocument('1.0', 'UTF-8');
    // @$dom->loadHTML(mb_convert_encoding($profile_img, 'HTML-ENTITIES', 'UTF-8'));
    // $finder = new DomXPath($dom);
    // $user['user_profile_url'] = $finder->query('//img')->item(0)->attributes->getNamedItem('src')->nodeValue;
    
    // if(empty($user['user_profile_url'])):
    //     $user['user_profile_url'] = get_avatar_url($user['user_id']);
    // endif;

    $bbpresslist_user = new BBPressList_User;

    $user['followed'] = $bbpresslist_user->is_following( $current_user_id, $user['user_id'] );
    
    return $user;
}

function idocalgary_get_replies($object, $field_name, $request){
    $reply_ids = bbp_get_all_child_ids( $object['id'],  $post_type = 'reply' );
    $replies = array();
    foreach ($reply_ids as $reply_id) {
        $reply_object = get_post($reply_id);
        $user_id = $reply_object->post_author;
        $user_name = get_the_author_meta( 'display_name', $user_id );

        if(get_user_meta($user_id, 'profile_photo', true) == 'profile_photo.jpg'):

            $user_profile_url = 'http://52calgary.com/wp-content/uploads/ultimatemember/' . $user_id . '/profile_photo-40.jpg';

        else:

            $user_profile_url = 'http://gravatar.com/avatar/d4180cb63752fd75540135b382ff6039?s=100';

        endif;
        
        // $profile_img = get_avatar( $user_id, 32 );

        // $dom = new DOMDocument('1.0', 'UTF-8');
        // @$dom->loadHTML(mb_convert_encoding($profile_img, 'HTML-ENTITIES', 'UTF-8'));
        // $finder = new DomXPath($dom);

        // $user_profile_url = $finder->query('//img')->item(0)->attributes->getNamedItem('src')->nodeValue;
        
        // if(empty($user_profile_url)):
        //     $user_profile_url = get_avatar_url($user_id);
        // endif;

        $raw_content = $reply_object->post_content;
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
        $reply['reply_id'] = $reply_id;
        $reply['user_id'] = $user_id;
        $reply['user_name'] = $user_name;
        $reply['user_profile_url'] = $user_profile_url;
        $reply['reply_content'] = $content_array;
        $replies[] = $reply;
    }

    return $replies;
}

function idocalgary_get_replies_count($object, $field_name, $request){
    $reply_ids = bbp_get_all_child_ids( $object['id'],  $post_type = 'reply' );
    return count($reply_ids);
}


function idocalgary_get_human_readable_time($object, $field_name, $request){
    $post_date_gmt_timestamp = mysql2date( 'U', $object['date_gmt'] ); 
    return human_time_diff($post_date_gmt_timestamp, current_time('timestamp', true)) . '前';  
}

function idocalgary_get_price($object, $field_name, $request){
    return get_post_meta($object['id'], 'price', true);
}

function idocalgary_update_price($value, $object, $field_name){
    update_field('price',$value, $object->ID);
}

function idocalgary_get_phone($object, $field_name, $request){
    return get_post_meta($object['id'], 'phone',true);
}

function idocalgary_update_phone($value, $object, $field_name){
    update_field('phone',$value, $object->ID);
}

function idocalgary_get_email($object, $field_name, $request){
    return get_post_meta($object['id'], 'email',true);
}

function idocalgary_update_email($value, $object, $field_name){
    update_field('email',$value, $object->ID);
}

function idocalgary_get_website($object, $field_name, $request){
    return get_post_meta($object['id'], 'website',true);
}

function idocalgary_get_gallery($object, $field_name, $request){
    $images = array();

    $raw_images = get_field('gallery', $object['id']);

    if(!empty($raw_images)):
        foreach ($raw_images as $raw_image):
            $images[] = $raw_image['sizes']['app_image_1'];
        endforeach;
    endif;
    return $images;
}

function idocalgary_get_advert_gallery($object, $field_name, $request){
    $images = array();

    $rows = get_field('gallery', $object['id']);

    if(!empty($rows)):
        foreach ($rows as $row):
            $images[] = wp_get_attachment_image_url($row['image'], 'app_image_1', false);
        endforeach;
    endif;
    return $images;
}

function idocalgary_get_advert_featured_image_url($object, $field_name, $present){
    
    if(empty($object['gallery'])):
        return "http://s3.amazonaws.com/52calgary-media/wp-content/uploads/2017/02/14182028/no-image.png";
    endif;

    return $object['gallery'][0];
}

function idocalgary_get_img_ids($object, $field_name, $request){
    return get_post_meta($object['id'], 'gallery', true);
}

function idocalgary_get_topic_img_ids($object, $field_name, $request){
    return get_post_meta($object['id'], 'gallery', true);
}

function idocalgary_get_advert_img_ids($object, $field_name, $request){
    $img_ids = array();

    $rows = get_field('gallery', $object['id']);

    if(!empty($rows)):
        foreach ($rows as $row):
            $img_ids[] = $row['image'];
        endforeach;
    endif;
    return $img_ids;
}

function idocalgary_update_img_ids($value, $object, $field_name){
    $img_ids = explode(',', $value);
    update_field( 'gallery', $img_ids , $object->ID );
}

function idocalgary_update_topic_img_ids($value, $object, $field_name){
    $img_ids = explode(',', $value);
    update_field( 'gallery', $img_ids , $object->ID );    
}

function idocalgary_update_advert_img_ids($value, $object, $field_name){   
    $img_ids = explode(',', $value);

    $new_added_rows = array();

    foreach ($img_ids as $img_id) {

        $img_id = (int)$img_id;

        $row = array(
            'field_5862a84b1f85c' => $img_id,
            'field_5862aad9de3f3' => ''
        );

        $new_added_rows[] = $row;

    }


    $old_rows = get_field('field_5862a3d3307ea', $object->ID);

    if( NULL == $old_rows):
        $old_rows = array();
    endif;

    $current_rows = array_merge($old_rows, $new_added_rows);

    update_field( 'field_5862a3d3307ea', $current_rows , $object->ID );
}

function idocalgary_get_topic_images($object, $field_name, $request){
    // global $post;
    // $dom = new DOMDocument('1.0', 'UTF-8');
    // @$dom->loadHTML(mb_convert_encoding($post->post_content, 'HTML-ENTITIES', 'UTF-8'));
    // $finder = new DomXPath($dom);
    // $content_object = $finder->query('//img');
    // $length = $content_object->length;
    
    // $images_1 = array();

    // for ($i = 0; $i < $length; $i++) {
    //     $element  = $content_object->item($i);
    //     $src = $element->attributes->getNamedItem('src')->nodeValue;
    //     $images_1[] = $src;
    // }


    // images in gallery field
    $images_2 = array();

    $raw_images = get_field('gallery', $object['id']);

    if(!empty($raw_images)):
        foreach ($raw_images as $raw_image):
            $images_2[] = $raw_image['url'];
        endforeach;
    endif;

    // $images = array_unique(array_merge($images_2, $images_1));

    // return $images;
    return $images_2;
}

function idocalgary_get_topic_featured_image_url($object, $field_name, $request){

    global $post;
    $dom = new DOMDocument('1.0', 'UTF-8');
    @$dom->loadHTML(mb_convert_encoding($post->post_content, 'HTML-ENTITIES', 'UTF-8'));
    $finder = new DomXPath($dom);
    $content_object = $finder->query('//img');
    $length = $content_object->length;
    
    $images_1 = array();

    for ($i = 0; $i < $length; $i++) {
        $element  = $content_object->item($i);
        $src = $element->attributes->getNamedItem('src')->nodeValue;
        $images_1[] = $src;
    }

    $images_2 = array();

    $raw_images = get_field('gallery', $object['id']);

    if(!empty($raw_images)):
        foreach ($raw_images as $raw_image):
            $images_2[] = $raw_image['url'];
        endforeach;
    endif;

    $images = array_unique(array_merge($images_2, $images_1));

    if(!empty($images)):
        return $images[0];
    endif;
    return null;
}

function idocalgary_get_topic_tags_array($object, $field_name, $request){
    $topic_tags_id_array = $object['topic-tags'];
    $topic_tags_array = array();
    $i = 0;
    foreach ($topic_tags_id_array as $topic_tags_id) {
        $topic_tags_array[$i]['name'] = get_term_by('id', $topic_tags_id, 'topic-tag', OBJECT, 'raw')->name;
        $topic_tags_array[$i]['link'] = get_term_link($topic_tags_id, 'topic-tag');
        $i++;
    }
    return $topic_tags_array;
}

function idocalgary_update_topic_is_favorite($value, $object, $field_name){
    if(isset($value) && $value==true){
        $user_id = get_current_user_id();
        $topic_id = $object->ID;
        $is_favorite = bbp_is_user_favorite($user_id, $topic_id);
        if($is_favorite){
            bbp_remove_user_favorite($user_id, $topic_id);
        }else{
            bbp_add_user_favorite($user_id, $topic_id);
        }
    }
}

function idocalgary_get_topic_is_favorite($object, $field_name, $request){
    $user_id = get_current_user_id();
    $topic_id = $object['id'];

    return bbp_is_user_favorite($user_id, $topic_id);
}

function idocalgary_get_topic_favorites_count($object, $field_name, $request){
    $users = (array)bbp_get_topic_favoriters($object['id']);
    return count($users);
}

function idocalgary_get_topic_replies_count($object, $field_name, $request){
    $users = (array)bbp_get_topic_favoriters($object['id']);
    return count($users);
}

function idocalgary_update_is_favorite($value, $object, $field_name){
    if( isset($value) && $value === 'true' ){
        $favorite = new  SimpleFavorites\Entities\Favorite\Favorite;
        $user = new SimpleFavorites\Entities\User\UserRepository;
        $user_id = get_current_user_id();
        $isFavorite = $user->isFavorite($object->ID, $site_id = 1, $user_id)?'inactive':'active';
        $favorite->update($object->ID, $isFavorite, $site_id = 1);
    }  
}

function idocalgary_get_rating_average($object, $field_name, $request){
    return get_post_meta($object['id'], 'rating_average',true);
}


function idocalgary_get_likes_count($object, $field_name, $request){
    return get_post_meta($object['id'], 'vortex_system_likes',true);
}

function idocalgary_get_dislikes_count($object, $field_name, $request){
    return get_post_meta($object['id'], 'vortex_system_dislikes',true);
}

function idocalgary_get_news_source($object, $field_name, $request){
    return get_post_meta($object['id'], 'news_source', 'true');
}

function idocalgary_get_plain_title($object, $field_name,$request){
    return preg_replace('/\s+/', '', trim($object['title']['raw']));
}

function idocalgary_update_is_like($value, $object, $field_name) {
    if(isset($value) && $value === 'true'){
        $current_user_id = get_current_user_id();
        // 判断是否已经登录？
        $user_key = 'vortex_system_user_'.$current_user_id;
        $user_data = get_post_meta($object->ID,$user_key,true);
        if(empty($user_data) || !isset($user_data['liked']) || !isset($user_data['disliked'])){
            $user_data = array(
                'liked'=>'liked',
                'disliked'=>'nodisliked'
            );
            update_post_meta($object->ID, 'vortex_system_likes', 1);
        }else{
            if($user_data['liked'] === 'liked'){
                $user_data['liked'] = 'noliked';
                $likes_count = get_post_meta($object->ID,'vortex_system_likes',true);
                $likes_count = $likes_count >=1 ?(--$likes_count):0;
                update_post_meta($object->ID, 'vortex_system_likes', $likes_count);
            }elseif($user_data['liked'] === 'noliked'){
                $user_data['liked'] = 'liked';
                $likes_count = get_post_meta($object->ID,'vortex_system_likes',true);
                $likes_count++;
                update_post_meta($object->ID, 'vortex_system_likes', $likes_count);
                if($user_data['disliked'] === 'disliked'){
                    $user_data['disliked'] = 'nodisliked';
                    $dislikes_count = get_post_meta($object->ID,'vortex_system_dislikes',true);
                    $dislikes_count = $dislikes_count >=1 ?(--$dislikes_count):0;
                    update_post_meta($object->ID, 'vortex_system_dislikes', $dislikes_count);
                }
            }
        }
        update_post_meta($object->ID,$user_key,$user_data);
    }
}

function idocalgary_update_is_dislike($value, $object, $field_name) {
    if(isset($value) && $value === 'true'){
        $current_user_id = get_current_user_id();
        $user_key = 'vortex_system_user_'.$current_user_id;
        $user_data = get_post_meta($object->ID,$user_key,true);
        if(empty($user_data)){
            $user_data = array(
                'liked'=>'noliked',
                'disliked'=>'disliked'
            );
            add_post_meta($object->ID, 'vortex_system_dislikes', 1);
        }else{
            if($user_data['disliked'] === 'disliked'){
                $user_data['disliked'] = 'nodisliked';
                $dislikes_count = get_post_meta($object->ID,'vortex_system_dislikes',true);
                $dislikes_count = $dislikes_count >=1 ?(--$dislikes_count):0;
                update_post_meta($object->ID, 'vortex_system_dislikes', $dislikes_count);
            }elseif($user_data['disliked'] === 'nodisliked'){
                $user_data['disliked'] = 'disliked';
                $dislikes_count = get_post_meta($object->ID,'vortex_system_dislikes',true);
                $dislikes_count++;
                update_post_meta($object->ID, 'vortex_system_dislikes', $dislikes_count);
                if($user_data['liked'] === 'liked'){
                    $user_data['liked'] = 'noliked';
                    $likes_count = get_post_meta($object->ID,'vortex_system_likes',true);
                    $likes_count = $likes_count >=1 ?(--$likes_count):0;
                    update_post_meta($object->ID, 'vortex_system_likes', $likes_count);
                }
            }
        }
        update_post_meta($object->ID,$user_key,$user_data);
    }
}

function idocalgary_get_is_like($object, $field_name,$request) {
    if(!is_user_logged_in()){  
        return 0;
    }

    $current_user_id = get_current_user_id();
    $user_key = 'vortex_system_user_'.$current_user_id;

    $user_data = get_post_meta($object['id'],$user_key,true);

    if(empty($user_data)){
        $user_data = array(
            'liked'=>'noliked',
            'disliked'=>'nodisliked'
        );
        add_post_meta($object['id'],$user_key,true);
        return 0;
    } else {
        $is_like = isset($user_data['liked'])&&($user_data['liked'] === 'liked')?1:0;
    }
    return $is_like;
}

function idocalgary_get_is_dislike($object, $field_name,$request) {
    if(!is_user_logged_in()){  
        return 0;
    }

    $current_user_id = get_current_user_id();
    $user_key = 'vortex_system_user_'.$current_user_id;

    $user_data = get_post_meta($object['id'],$user_key,true);
    if(empty($user_data)){
        return 0;
    } else {
        $is_dislike = isset($user_data['disliked'])&&($user_data['disliked'] === 'disliked')?1:0;
    }
    return $is_dislike;
}

function idocalgary_get_users_who_favorited($object, $field_name,$request){
    $simplified_users = array();
    $detailed_users = get_users_who_favorited_post($object['id']);

    foreach( $detailed_users as $detailed_user ) {
        $simplified_users[] = array(
            'id'            => $detailed_user->data->ID,
            'user_email'    => $detailed_user->data->user_email,
            'display_name'  => $detailed_user->data->display_name,
            'profile_url'   => (get_user_meta($detailed_user->data->ID, 'profile_photo', true) == 'profile_photo.jpg')?'http://52calgary.com/wp-content/uploads/ultimatemember/' . $detailed_user->data->ID . '/profile_photo-40.jpg':'http://gravatar.com/avatar/d4180cb63752fd75540135b382ff6039?s=100'
        );
    }
    return $simplified_users;    
}

function idocalgary_get_plain_text_content($object, $field_name, $request){
    
    global $post;
    $dom = new DOMDocument('1.0', 'UTF-8');
    @$dom->loadHTML(mb_convert_encoding($post->post_content, 'HTML-ENTITIES', 'UTF-8'));
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
    return $content_array;   

}

function idocalgary_get_views_count($object, $field_name, $request){

    $count = get_post_meta($object['id'], '_count-views_all', true);

    if($count == null || $count == false || empty($count)){
        
        $count = (int)0;

        update_post_meta($object['id'], '_count-views_all', 0);
    
    }
    
    if (preg_match('/\/ido\/v1\/\w+\/\d+/',print_r($request->get_route(), true))){
            
            $count++;

        update_post_meta($object['id'], '_count-views_all', $count);


    }

    return $count; 
}

function idocalgary_get_comments_count($object, $field_name, $request){
    return get_comments_number($object['id']);
}

function idocalgary_get_is_favorite($object, $field_name, $request){
    $user = new SimpleFavorites\Entities\User\UserRepository;
    $user_id = get_current_user_id();
    return $user->isFavorite($object['id'], $site_id = 1, $user_id);  
}


function idocalgary_get_favorites_count($object, $field_name, $request){
    return get_favorites_count($object['id']);
}

function idocalgary_get_comments($object, $field_name, $request){
    $comments_array = array();
    $comments_object = get_comments(
        array(
            'post_id' => $object['id']
        )
    );

    foreach ($comments_object as $comment_object) {

        $comments_array_element                     = array();

        $comment_id                                 = $comment_object->comment_ID;
        $comments_array_element['comment_id']       = $comment_id;
        $comments_array_element['comment_content']  = $comment_object->comment_content;
        
        $user_id                                    = (0 == $comment_object->user_id)?1:$comment_object->user_id;
        $user                                       = get_user_by('id', $user_id);

        $comments_array_element['user_id']          = $user_id;     
        $comments_array_element['user_email']       = $comment_object->comment_author_email;
        $comments_array_element['user_display_name']= $comment_object->comment_author;
        $comments_array_element['user_roles']       = $user->roles;
        $comments_array_element['user_profile_url'] = (get_user_meta($user_id, 'profile_photo', true) == 'profile_photo.jpg')?'http://52calgary.com/wp-content/uploads/ultimatemember/' . $user_id . '/profile_photo-40.jpg':'http://gravatar.com/avatar/d4180cb63752fd75540135b382ff6039?s=100';

        $comments_array_element['comment_time']     = human_time_diff( strtotime($comment_object->comment_date_gmt . ' GMT'), current_time('timestamp', true)). '前';

        $comments_array_element['comment_rating']   = get_comment_meta($comment_id, 'rating', true);

        $comment_image_ids                          = get_comment_meta($comment_id, 'comment_image_reloaded', true);
        // var_dump($comment_image_ids);

        $comment_image_urls                         = array();
        if(!empty($comment_image_ids)){
            foreach ($comment_image_ids as $comment_image_id) {
               $comment_image_urls[]                    = wp_get_attachment_url((int)$comment_image_id);
            }
        }
        $comments_array_element['comment_image_urls'] = $comment_image_urls;
        
        $comments_array[]                           = $comments_array_element;
    }

    return $comments_array;
}

function idocalgary_get_terms($object, $field_name, $request){
    $taxonomy_names = get_post_taxonomies();
    $terms          = array();

    foreach ($taxonomy_names as $taxonomy_name) {
        if (!empty($object[$taxonomy_name])) {
            $term_ids = $object[$taxonomy_name];
            foreach ($term_ids as $term_id) {
                $term = get_term((int)$term_id, $taxonomy_name);
                $term_link = get_term_link( $term, $taxonomy_name );
                $term_name = $term->name;
                $terms[] = array(
                    'term_id' => $term_id,
                    'term_name' => $term_name,
                    'term_link' => $term_link,
                );
            }
        }
    }

    return $terms;
}

function idocalgary_get_business_hour($object, $field_name, $request){
    if(!get_field('has_business_hour', $object['id'])){
        return null;
    }
    $business_hour   = array();
    $business_hour['sunday'] = get_field('sun', $object['id']);
    $business_hour['monday'] = get_field('mon', $object['id']);
    $business_hour['tuesday'] = get_field('tue', $object['id']);
    $business_hour['wednesday'] = get_field('wed', $object['id']);
    $business_hour['thursday'] = get_field('thu', $object['id']);
    $business_hour['friday'] = get_field('fri', $object['id']);
    $business_hour['saturday'] = get_field('sat', $object['id']);

    return $business_hour;
}

function idocalgary_get_if_is_featured($object, $field_name, $request){

    return get_field('is_featured', $object['id']) == null?false:get_field('is_featured', $object['id']);
}

function idocalgary_get_category_icon($object, $field_name, $request){
    // return $object['taxonomy'] . '_' . $object['id'];
    return get_field('category_icon', 'term_' . $object['id']);
}

function idocalgary_get_location($object, $field_name, $request){
    return get_field('location', $object['id']);
}

function idocalgary_get_address($object, $field_name, $request){
    return empty(get_field('location', $object['id']))?'': get_field('location', $object['id'])['address'];
}

function idocalgary_get_latitude($object, $field_name, $request){
 return empty(get_field('location', $object['id']))? '51.0530588' : get_field('location', $object['id'])['lat'];
}

function idocalgary_get_longitude($object, $field_name, $request){
 return empty(get_field('location', $object['id']))?'-114.0625613': get_field('location', $object['id'])['lng'];
}

function idocalgary_get_child_categories($object, $field_name, $request){
    $child_terms_array = array();
    $child_terms_object = get_terms(
        array(
            'taxonomy'        => $object['taxonomy'],
            'hide_empty'      => false,
            'parent'          => $object['id'],
            'orderby'         => 'count',
            'order'           => 'DESC'
        )
    );
    foreach ($child_terms_object as $term) {
        $term_id = $term->term_id;
        $term_array = array();
        $term_array['id']            = $term_id;
        $term_array['name']          = $term->name;
        $term_array['count']         = $term->count;
        $term_array['link']          = get_term_link($term_id);
        $child_terms_array[]         = $term_array;
    }
    return $child_terms_array;
}

function idocalgary_get_distance($object, $field_name, $request){
    $user_lng = $request->get_param('lng');
    $user_lat = $request->get_param('lat');

    if($user_lng == null || $user_lat == null):
        return '';
    endif;

    $user_location = array(
        'lng' => $user_lng,
        'lat' => $user_lat
    );

    $place_lng = !empty($object['location']['lng'])?$object['location']['lng']:(-114.0625613);
    $place_lat = !empty($object['location']['lat'])?$object['location']['lat']:51.0530588;
    $place_location = array(
        'lng' => $place_lng,
        'lat' => $place_lat
    );

    $distance = get_distance_between_two_points($user_location, $place_location);
    
    return $distance;  
}

function idocalgary_get_featured_image_url($object, $field_name, $request){
    $featured_image_id =  $object['featured_media'];
    // 需要设定size吗？

    if(wp_get_attachment_image_src($featured_image_id)[0]!=null){
        return wp_get_attachment_image_src($featured_image_id)[0];
    }

    if(isset($object['images']) && !empty($object['images'])){
        return $object['images'][0];
    }


    // default image

    return 'http://s3.amazonaws.com/52calgary-media/wp-content/uploads/2017/02/14182028/no-image.png';
}



function idocalgary_get_advert_conversation_id($object, $field_name, $request){
    global $wpdb;

    $current_user = get_current_user_id();
    
    if(empty($current_user)){
        return 0;
    }

    $advert_author = $object['author'];

    $conversation_id = null;

    // Test for previous conversation

    $conversation_table = $wpdb->prefix . "um_conversations";

    $conversation_id = $wpdb->get_var(
        $wpdb->prepare("SELECT conversation_id FROM $conversation_table WHERE user_a = %d AND user_b = %d LIMIT 1",
            $advert_author,
            $current_user
        )
    );

    if (empty($conversation_id)) {
        $conversation_id = $wpdb->get_var(
            $wpdb->prepare("SELECT conversation_id FROM $conversation_table WHERE user_a = %d AND user_b = %d LIMIT 1",
                $current_user,
                $advert_author
            )
        );
    }

    if (empty($conversation_id)) {
        $conversation_id = 0;
    }

    return $conversation_id;
}

function idocalgary_get_wechat($object, $field_name, $request){
    return get_post_meta($object['id'], 'wechat', true);
}