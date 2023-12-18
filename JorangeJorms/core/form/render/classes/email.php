<?php
namespace Jorms;
if(!defined('ABSPATH')) exit; // Exit if accessed directly

class email extends Jorms_field{


    function get_name(){
        return 'email';
    }

    function render_html( $field ){
        include __DIR__ . '/fields/set_field_to_variables.php';
        include __DIR__ . '/fields/simple_input.php';
    }

    function validate($value, $setup ){
        return is_email( $value );
    }

    function sanitize_input( $value ){
        return strtolower( parent::sanitize_input( $value ) );
    }

}