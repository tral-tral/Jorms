<?php
namespace Jorms;
if(!defined('ABSPATH')) exit; // Exit if accessed directly

class checkbox extends Jorms_field{


    function get_name(){
        return 'checkbox';
    }

    function render_html( $field ){
        include __DIR__ . '/fields/set_field_to_variables.php';
        include __DIR__ . '/fields/checkbox.php';
    }

    function validate($value, $setup){
        $options = $setup['options'] ?? false;
        if (!$options) return false;

        if (!is_array($value))
            $value = array($value);


        $count  = count( $value );
        $min    = $setup['min'] ?? 0;
        $max    = $setup['max'] ?? false;

        if( $count < $min ||  $max && $count > $max  )
            return false;

        foreach($value as $v) {
            $found_match = false;
            foreach ($options as $option) {
                if (is_array($option))
                    if (isset($option['value']))
                        $option_value = $option['value'];
                    else $option_value = $option;
                if ($option_value === $v) {
                    $found_match = true;
                    break;
                }
            }
            if( !$found_match ) return false;
        }
        return true;
    }

}