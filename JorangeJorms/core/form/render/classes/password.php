<?php
namespace Jorms;
if(!defined('ABSPATH')) exit; // Exit if accessed directly

class password extends Jorms_field{


    function get_name(){
        return 'password';
    }

    function render_html( $field ){
        include __DIR__ . '/fields/set_field_to_variables.php';
        include __DIR__ . '/fields/simple_input.php';
    }

    function validate($value, $setup ){
        return true;
    }
}