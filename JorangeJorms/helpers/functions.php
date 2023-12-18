<?php
namespace Jorms;
if(!defined('ABSPATH')) exit; // Exit if accessed directly


function jorms_empty( $value ){
    return empty( $value ) && $value!=='0' && $value!==0;
}

function style_reg( $name ){
    add_action("wp_enqueue_scripts", function() use ($name){
        if (file_exists(JORMS_STYLE_PATH . $name . '.css')) {
            $ver = filemtime(JORMS_STYLE_PATH . $name . '.css');
            wp_register_style( $name, JORMS_STYLE_URL . $name . '.css', [], $ver);
        }
    },1);
}
function script_reg( $name, $vars = [], $includes = [] ){
    add_action("wp_enqueue_scripts", function() use ($name, $vars, $includes){

        if (file_exists(JORMS_SCRIPTS_PATH . $name . '.js')) {
            $ver = filemtime(JORMS_SCRIPTS_PATH . $name . '.js');
            wp_register_script( $name, JORMS_SCRIPTS_URL . $name . '.js', $includes, $ver, true );
            if( !empty( $vars ) )
                wp_localize_script( $name, sanitize_title_with_underscores($name) . '_vars',
                    $vars);
        }
    },1);
}

function script_reg_src( $handle, $src ){
    add_action("wp_enqueue_scripts", function() use ( $handle, $src ){
        wp_register_script( $handle , $src , '', '', false );
    },1);
}


function enqueue_styles( $names ){
    if( !is_array( $names ) ) $names = array( $names );
    foreach( $names as $name ){
        enqueue_style( $name );
    }
}

function enqueue_scripts( $names ){
    if( !is_array( $names ) ) $names = array( $names );
    foreach( $names as $name ){
        enqueue_script( $name );
    }
}

function enqueue_style( $name ){
  //  add_action("wp_enqueue_scripts", function() use ($name){
        wp_enqueue_style( $name );
 //   },999);
}

function enqueue_script( $name ){
  // add_action("wp_enqueue_scripts", function() use ($name) {
        wp_enqueue_script( $name );
  //  },999);
}

function style_($name){
    style_reg( $name );
    enqueue_style($name);
}

function script_( $args ){
    script_reg( $args );
    enqueue_script( $args );
}

function sanitize_title_with_underscores( $title ){
    return str_replace( '-', '_', sanitize_title_with_dashes( $title ) );
}

function checkauthor( $id ){
    $id = absint( $id );
    return ( is_user_admin() || ( ( get_post_field( 'post_status' , $id ) == 'publish' || get_post_field( 'post_status' , $id ) == 'inherit' ) && get_post_field( 'post_author', $id ) == get_current_user_id() ) );
}

function has_role( $role , $user = false ){

    if( !$user ) $user = wp_get_current_user();


    if( !is_array( $role ) ) $role = array( $role );

    $role[]='administrator';
    $user_roles = $user->roles;

    foreach( $role as $role_check ){
        if( in_array( $role_check , $user_roles ) ) {
            return true;
        }
    }

    return false;

}


function is_user_admin( $user = false ){
    return has_role( 'administrator', $user);
}

