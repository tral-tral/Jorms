<?php

if(!defined('ABSPATH')) exit; // Exit if accessed directly


add_action('wp_ajax_jorms_form', 'jorms_form_ajax'  );
add_action('wp_ajax_nopriv_jorms_form', 'jorms_form_ajax' );

function jorms_form_ajax(){

    if( !isset( $_POST['input'] ) || !isset( $_POST['nonce'] ) || !isset( $_POST['handle'] )  || !isset( $_POST['protected_id'] ) )
        wp_send_json_error('Missing data', 400);


    $input  = $_POST['input']; //, true );
    $nonce  = $_POST['nonce'];
    $handle = $_POST['handle'];
    $protected_id = $_POST['protected_id'];

    $form = Jorms()->get_form( $handle );



    if( !$form )
        wp_send_json_error('Invalid form handle.', 403);

    $nonce_action = 'jorms_form_nonce_' . $handle . $form->get_counter() . $protected_id;



    if( !wp_verify_nonce( $nonce, $nonce_action ) )
        wp_send_json_error('Form has expired; Refresh and try again', 403);



    do_action('jorms_before_process_form');
    $response = $form->process( $input, $protected_id );
    do_action('jorms_after_process_form');

    if( !$response['valid'] ) {
        do_action('jorms_form_invalid');
        wp_send_json_error($response['response'], 422);
    }

    do_action('jorms_form_valid');
    wp_send_json_success( $response[ 'response' ] );

}

//Add nonce during upload and check that nonce during remove for deleting the image on the form you uploaded it from.
/*
 * add_action( 'wp_ajax_handle_deleted_media', 'handle_deleted_media' );

function handle_deleted_media(){

    if( isset($_REQUEST['media_id']) ){
        $post_id = absint( $_REQUEST['media_id'] );

        $status = wp_delete_attachment($post_id, true);

        if( $status )
            echo json_encode(array('status' => 'OK'));
        else
            echo json_encode(array('status' => 'FAILED'));
    }

    die();
}
    addRemoveLinks: true,
    removedfile: function(file) {
        var attachment_id = file.attachment_id;
        jQuery.ajax({
            type: 'POST',
            url: dropParam.delete,
            data: {
                media_id : attachment_id
            }
        });
        var _ref;
        return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
    }


 */

add_action( 'wp_ajax_jorms_handle_dropped_media', 'jorms_handle_dropped_media' );
add_action( 'wp_ajax_nopriv_jorms_handle_dropped_media', 'jorms_handle_dropped_media' );
function jorms_handle_dropped_media() {

    //$allowed_users_roles = jorms_allow_uploads_roles();

    /*
   if( !jorms_can_uploaded() ) {
       status_header(403);
       die('You are not allowed to upload.');
   }
    */

   if( !isset( $_GET['nonce'] ) || !isset( $_GET['field_id'] ) || !wp_verify_nonce( $_GET['nonce'] , 'jorms_upload_nonce_' . $_GET['field_id'] ) ) {
       status_header(403);
       die('Permissions may have expired. Refresh and try again.');
   }
    /*
    $upload_dir = wp_upload_dir();
    $upload_path = $upload_dir['path'] . DIRECTORY_SEPARATOR;
    $num_files = count($_FILES['file']['tmp_name']);
    */



    $allowed_mime = ['image/jpeg', 'image/png', 'image/jpg'];
    $max_file_size = 2 * 1024 * 1024; // 2 MB
    $newupload = 0;

    if (!empty($_FILES)) {

        do_action('jorms_before_upload', $_FILES);

        $files = $_FILES;
        foreach ($files as $file) {
            if ($file['error'] !== UPLOAD_ERR_OK) {
                status_header(403);
                die('File upload error: ' . $file['error']);
            }

            if ($file['size'] > $max_file_size) {
                status_header(403);
                die('File size exceeds the maximum limit.');
            }

            $file_type = wp_check_filetype_and_ext($file['tmp_name'], $file['name']);
            if (!in_array($file_type['type'], $allowed_mime)) {
                status_header(403);
                die('That is a forbidden file type.');
            }

            $newfile = array(
                'name' => $file['name'],
                'type' => $file['type'],
                'tmp_name' => $file['tmp_name'],
                'error' => $file['error'],
                'size' => $file['size']
            );

            $_FILES = array('upload' => $newfile);
            foreach ($_FILES as $file => $array) {
                $newupload = media_handle_upload($file, 0);
                if (!is_wp_error($newupload)) {
                    update_post_meta($newupload, '_jorms_upload', true);
                }
            }
        }
    }


    status_header(200);

    echo $newupload;

    do_action('jorms_after_uploaded', $newupload );

    die();
}

function jorms_can_uploaded(){
    return apply_filters('jorms_can_upload', \Jorms\has_role( jorms_allow_uploads_roles()  ), );
}

function jorms_allow_uploads_roles(){
    $allowed_roles = [];
    return apply_filters('jorms_allow_upload_roles', $allowed_roles );

}

