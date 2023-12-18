<?php
namespace Jorms;
if(!defined('ABSPATH')) exit; // Exit if accessed directly

class text extends Jorms_field{


    function get_name(){
        return 'text';
    }

    function render_html( $field ){
        include __DIR__ . '/fields/set_field_to_variables.php';
        include __DIR__ . '/fields/simple_input.php';
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