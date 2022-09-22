<?php

// Токен для доступа по api
const JWT_AUTH_TOKEN = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJuYW1lIjoidGVwbGl0c2EifQ.bhdl3rqM-6ZiBViQXuJLnoQn8HI_XkGXDh1oDxb5MbI';


add_action( 'rest_api_init', function () {
  register_rest_route( 'teplitsa/v1', '/communities/' , array(

    // Get
    array (
      'methods'               => 'GET',
      'callback'              => 'rest_get_communities',
      'permission_callback'   => '__return_true',
    ),
    // Post
    array (
      'methods'               => 'POST',
      'callback'              => 'rest_post_community',
      'permission_callback'   => 'test_token_verification',
    ),
  ) 
);
} );


add_action( 'rest_api_init', function () {
  register_rest_route( 'teplitsa/v1', '/communities/(?P<community_id>\d+)' , array(

    // Get
    array (
      'methods'               => 'GET',
      'callback'              => 'rest_get_community',
      'permission_callback'   => '__return_true',
      'args' => array(
        'id' => array(
          'validate_callback' => function($param, $request, $key) {
            return is_numeric( $param );
          }
        ),
      ),
    ),
    // Post
    array (
      'methods'               => 'POST',
      'callback'              => 'rest_post_community',
      'permission_callback'   => 'test_token_verification',
    ),
    // Put
    array (
      'methods'               => 'PUT',
      'callback'              => 'rest_put_community',
      'permission_callback'   => 'test_token_verification',
      'args' => array(
        'id' => array(
          'validate_callback' => function($param, $request, $key) {
            return is_numeric( $param );
          }
        ),
      ),
    ),
    // Delete
    array (
      'methods'               => 'DELETE',
      'callback'              => 'rest_delete_community',
      'permission_callback'   => 'test_token_verification',
      'args' => array(
        'id' => array(
          'validate_callback' => function($param, $request, $key) {
            return is_numeric( $param );
          }
        ),
      ),
    ),
  ) 
);
} );


// Получаем все communities и отдаем согласно данным для возврата. 
function rest_get_communities( WP_REST_Request $request ){

	$communities    = get_posts( [
    'numberposts'     => 0,
    'post_type'       => 'community',
    'post_status'     => 'publish',
  ] );

	if ( empty( $communities ) )
		return new WP_Error( 'rest_no_communities_error', __('Нет записей типа communities'), array( 'status' => 405 ) );

	// Данные для возврата
  $communities = array_map( function ( $post ) {
    $post_data['id']    = (int) $post->ID;
    $post_data['title'] = esc_html( $post->post_title );
    $post_data['url']   = esc_url( get_the_permalink( $post ) );
    
    return $post_data;
  }, $communities );

  return $communities;
}

// Создание нового Community
function rest_post_community( WP_REST_Request $request ) {

  $post['post_title']   = sanitize_text_field( $request->get_param( 'title' ) );
  $post['post_content'] = sanitize_text_field( $request->get_param( 'content' ) );
  $post['post_status']  = 'publish';
  $post['post_type']    = 'community';
  
  $new_community = wp_insert_post( $post );

  if( !is_wp_error( $new_community ) ){
    return new WP_Error( 'rest_community_create', __('community создано'), array( 'status' => 200 ) );
  }
  
  return new WP_Error( 'rest_community_create_error', __('Что-то пошло не так('), array( 'status' => 405 ) );
}

// Получаем Community по id
function rest_get_community( WP_REST_Request $request ) {

  $community_id = $request->get_param('community_id');
  $community = get_post( $community_id );

  if ( empty($community) || get_post_type($community_id) != 'community' ) {
    return new WP_Error( 'rest_no_community_id_error', __('community_id не найдено'), array( 'status' => 405 ) );
  }
  
  return $community;
}

// Обновление Community
function rest_put_community( $request ){
  $community_id = $request->get_param('community_id');
  $community = get_post( $community_id );

  $post['ID'] = $community_id;
  $post['post_title']     = sanitize_text_field( $request->get_param( 'title' ) );
  $post['post_content']   = sanitize_text_field( $request->get_param( 'content' ) );
  $post['post_author']    = sanitize_text_field( $request->get_param( 'author' ) );
  
  $community_update = wp_update_post( $post, true );

  if ( empty($community) || get_post_type($community_id) != 'community' ) {
    return new WP_Error( 'rest_no_community_id_error', __('community_id не найдено'), array( 'status' => 405 ) );
  }

  wp_update_post( $post, true );

  

  return new WP_Error( 'rest_community_update', __('community обновлено'), array( 'status' => 200 ) );
}

// Удаление Community
function rest_delete_community( $request ){
  $community_id = $request->get_param('community_id');
  $community    = get_post( $community_id );

  if ( empty($community) || get_post_type($community_id) != 'community' ) {
    return new WP_Error( 'rest_no_community_id_error', __('community_id не найдено'), array( 'status' => 405 ) );
  }

  wp_delete_post($community_id);

  return new WP_Error( 'rest_community_deleted', __('community удалено'), array( 'status' => 200 ) );
}



function test_token_verification() {

  // Проверяем в heders HTTP_AUTHORIZATION. Если нет, возвращаем пользователя.
	$auth = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : false;

	// Дополнительно проверяем в heders REDIRECT_HTTP_AUTHORIZATION. Некоторые запросы в т.ч. Postman были с REDIRECT_HTTP_AUTHORIZATION.
	if (!$auth) {
		$auth = isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION']) ? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] : false;
	}

	// Если нет токена отдаем ошибку.
	if (!$auth) {
    return new WP_Error( 'rest_no_token_error', __('Токен не найден'), array( 'status' => 403 ) );
	}

	// Проверяем формат AUTHORIZATION, Если неверный, возвращаем ошибку.
	list($token) = sscanf($auth, 'Bearer %s');
	if (!$token) {
			return new WP_Error( 'rest_token_format_error', __('Ошибка формата токена'), array( 'status' => 403 ) );
		}

		if( $token == JWT_AUTH_TOKEN ) {
			$access = true;
		}
	 	else {
			$access = false;
		}

		return $access;
}