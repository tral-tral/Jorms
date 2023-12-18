<?php
namespace Jorms;
if(!defined('ABSPATH')) exit; // Exit if accessed directly

class time extends Jorms_field{


    function get_name(){
        return 'time';
    }

    function render_html( $field ){
        include __DIR__ . '/fields/set_field_to_variables.php';
        include __DIR__ . '/fields/simple_input.php';
    }
    function validate($value, $setup ){
        $max_time = isset( $setup['max'] ) ? strtotime( $setup['max'] ) : false;
        $min_time = isset( $setup['min'] ) ? strtotime( $setup['min'] ) : false;

        $valid_time = validate_time( $value );

        if( !$valid_time )
            return false;

        $time = strtotime( $value );

        if( $min_time && $time < $min_time || $max_time && $time > $max_time )
            return false;

        return true;
    }

}