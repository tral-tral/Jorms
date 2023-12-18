<?php
if(!defined('ABSPATH')) exit; // Exit if accessed directly

function Jorms(){
    return \Jorms\Jorms::instance();
}

function jorms_send_mail( $to, $subject, $body ){
   // wp_send_json_success('Mail thing ' . $to . $subject . $body );
    $headers = array('Content-Type: text/plain; charset=UTF-8','From: Fukuoka Night <info@fukuokanight.com>');
    wp_mail( $to , $subject , $body, $headers);
}

//Returns as 'label' => post_title, 'value' => post_id
function jorms_posts_for_form( $posts ){
    $parsed_posts = [];
    foreach( $posts as $post ){
        $parsed_posts[] = ['label'=> $post->post_title , 'value' => $post->ID ];
    }

    return $parsed_posts;
}

function jorms_get_posts_form( $query ){

  //  $author = $query['author'] ?? get_current_user();
    $post_type = $query['post_type'] ?? 'post';

    $query = new WP_Query( $query );
    return jorms_posts_for_form( $query->get_posts() );

}

