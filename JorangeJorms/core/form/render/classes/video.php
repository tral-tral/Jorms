<?php
namespace Jorms;
if(!defined('ABSPATH')) exit; // Exit if accessed directly

class video extends Jorms_field{


    function get_name(){
        return 'video';
    }

    function render_html( $field ){
        include __DIR__ . '/fields/set_field_to_variables.php';
        include __DIR__ . '/fields/video.php';
    }



    function validate($value, $setup ){

        if( !is_array( $value ) ) $value = array( $value );

        $count  = count( $value );
        $min    = $setup['min'] ?? 0;
        $max    = $setup['max'] ?? false;

        if( $count < $min ||  $max && $count > $max  )
            return false;

        return true;

    }


}