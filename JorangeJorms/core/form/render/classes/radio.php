<?php
namespace Jorms;
if(!defined('ABSPATH')) exit; // Exit if accessed directly

class radio extends Jorms_field{


    function get_name(){
        return 'radio';
    }

    function render_html( $field ){
        include __DIR__ . '/fields/set_field_to_variables.php';
        include __DIR__ . '/fields/radio.php';
    }

    function validate($value, $setup){
        $options = $setup['options'] ?? false;
        if (!$options) return false;
        foreach ($options as $option) {

            if (is_array($option))
                if (isset($option['value']))
                    $option_value = $option['value'];
                else $option_value = $option;
            if ($option_value === $value)
                return true;
        }
        return false;
    }
}