<?php
namespace Jorms;
if(!defined('ABSPATH')) exit; // Exit if accessed directly

class checkboxradio extends Jorms_field{


    function get_name(){
        return 'checkboxradio';
    }

    function render_html( $field ){
        include __DIR__ . '/fields/set_field_to_variables.php';
        include __DIR__ . '/render/fields/checkboxradio_input.php';
    }

    function validate($value, $setup ){

    }

}