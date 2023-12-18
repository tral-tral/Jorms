<?php
namespace Jorms;
if(!defined('ABSPATH')) exit; // Exit if accessed directly

class number extends Jorms_field{


    function get_name(){
        return 'number';
    }

    function render_html( $field ){
        include __DIR__ . '/fields/set_field_to_variables.php';
        include __DIR__ . '/fields/simple_input.php';
    }

    function sanitize_type(): Jorms_sanitize_type
    {
        return Jorms_sanitize_type::int;
    }

    function validate($value, $setup ){

        $max = $setup['max'] ?? false;
        $min = $setup['min'] ?? false;

        $step = $setup['step'] ?? false;

        if( $min && $value < $min || $max && $value > $max )
            return false;

        if ( $step && !(fmod($value, $step) == 0 || abs($value % $step) < 1e-9)  )
            return false;


        return true;
    }

}