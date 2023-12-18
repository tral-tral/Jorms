<?php
if(!defined('ABSPATH')) exit; // Exit if accessed directly

function Jorms(){
    return \Jorms\Jorms::instance();
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

