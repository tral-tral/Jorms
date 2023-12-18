<?php
namespace Jorms;
if(!defined('ABSPATH')) exit; // Exit if accessed directly

class step extends Jorms_field{


    function get_name(){
        return 'step';
    }

    function render_html( $field ){
        include __DIR__ . '/fields/set_field_to_variables.php';
        include __DIR__ . '/fields/step.php';
    }
    function validate($value, $setup ){

    }

}