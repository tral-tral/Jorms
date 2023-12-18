<?php
namespace Jorms;
if(!defined('ABSPATH')) exit; // Exit if accessed directly

class textarea extends Jorms_field{


    function get_name(){
        return 'textarea';
    }

    function render_html( $field ){
        include __DIR__ . '/fields/set_field_to_variables.php';
        include __DIR__ . '/fields/textarea.php';
    }

    function sanitize_type(): Jorms_sanitize_type
    {
        return Jorms_sanitize_type::textarea;
    }

    function validate($value, $setup ){

        if( !is_string($value) )
            return false;

        $minlength = $setup['minlength'] ?? false;
        $maxlength = $setup['maxlength'] ?? false;
        $length = strlen( $value );

        if( $minlength && $length < $minlength || $maxlength && $length > $maxlength )
            return false;

        return true;

    }

}