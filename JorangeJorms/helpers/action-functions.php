<?php
//namespace Jorms;
if(!defined('ABSPATH')) exit; // Exit if accessed directly


Jorms()->add_action('email', 'jorms_email', 'emails' );
Jorms()->add_action('create_update_post', 'jorms_create_update_post', 'data' );

function jorms_email( $emails ){

    if( !is_array( $emails ) )
        $emails = array( $emails );
    foreach( $emails as $email ){
        if( !isset( $email['to'] ) || !isset( $email['subject'] ) ||  !isset( $email['body'] ) )
            continue;
        $to = $email['to'];
        $subject = $email['subject'];
        $body = $email['body'];
        jorms_send_mail( $to, $subject, $body );
    }
}


function jorms_create_update_post( $data  ){

    $post_id = absint( $data['ID'] ?? 0 );


    if( $post_id !== 0 && current_user_can( 'edit_posts'  ) )
        wp_update_post( $data );
    else if( current_user_can( 'edit_post', $post_id ) )
        wp_insert_post(  $data );

}









