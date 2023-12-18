<?php
namespace Jorms;
if(!defined('ABSPATH')) exit; // Exit if accessed directly

class date extends Jorms_field{


    function get_name(){
        return 'date';
    }

    function render_html( $field ){
        include __DIR__ . '/fields/set_field_to_variables.php';
        include __DIR__ . '/fields/simple_input.php';
    }
    function validate($value, $setup ){
        $max_date = isset( $setup['max'] ) ? strtotime( $setup['max'] ) : false;
        $min_date = isset( $setup['min'] ) ? strtotime( $setup['min'] ) : false;
        $valid_date = validate_date( $value );

        if( !$valid_date )
            return false;

        $date = strtotime( $value );

        if( $min_date && $date < $min_date || $max_date && $date > $max_date )
            return false;

        return true;
    }

}