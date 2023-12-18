<?php
namespace Jorms;
if(!defined('ABSPATH')) exit; // Exit if accessed directly

class place extends Jorms_field{


    function get_name(){
        return 'place';
    }

    function register_scripts()
    {
        script_reg_src('places', 'https://maps.googleapis.com/maps/api/js?libraries=places&language=ja&key=' . JORMS_GOOGLE_PLACE_API_KEY );
    }

    function get_scripts()
    {
       return ['places'];
    }

    function render_html( $field ){
        include __DIR__ . '/fields/set_field_to_variables.php';
        include __DIR__ . '/fields/place.php';
    }


    function validate($value, $setup ){

        return true;

    }

}

