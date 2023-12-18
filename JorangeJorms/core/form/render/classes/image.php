<?php
namespace Jorms;
if(!defined('ABSPATH')) exit; // Exit if accessed directly

class image extends Jorms_field{


    function get_name(){
        return 'image';
    }


    function get_scripts(){
        return ['dropzone-min'];
    }

    function get_styles(){
        return ['dropzone'];
    }

    function render_html( $field ){
        include __DIR__ . '/fields/set_field_to_variables.php';
        include __DIR__ . '/fields/image.php';
    }

    function sanitize_type(): Jorms_sanitize_type
    {
        return Jorms_sanitize_type::int;
    }

    function validate($value, $setup ){

        if( !is_array( $value ) ) $value = array( $value );
        $count  = count( $value );
        $amount = $setup['amount'] ?? false;

        if( $amount && $count !== $amount )
            return false;

        foreach($value as $post_id ){

            if( get_post_type( $post_id ) != 'attachment' ||  !checkauthor( $post_id ) )
                return false;
        }

        return true;
    }


}