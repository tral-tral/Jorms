<?php
namespace Jorms;
if(!defined('ABSPATH')) exit; // Exit if accessed directly

class select extends Jorms_field{


    function get_name(){
        return 'select';
    }

    function render_html( $field ){
        include __DIR__ . '/fields/set_field_to_variables.php';
        include __DIR__ . '/fields/select.php';
    }

    function print_options( $options, $value = false ){
        if( $value && !is_array( $value ) ) $value = [ $value ];
    //    echo var_export( $options, true );
        foreach ($options as $option) {
            if( isset( $option['group'] ) ){
                echo '<optgroup label="' . $option['group'] . '">';
                if( isset( $option['options'] ) ) $this->print_options( $option['options'], $value );
                echo '</optgroup>';
                continue;
            }
            if( !is_array( $option ) ){
                $option_label = $option;
                $option_value = $option;
            }
             else {
            $option_label = $option['label'] ?? '';
            $option_value = $option['value'] ?? $option_label;
             }
            echo '<option value="' . $option_value . '" ' . ($value && in_array($option_value, $value) ? 'selected' : '') . '>' . $option_label . '</option>';

        }
    }


    //Add option to check if select allows multiple?
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
                if (is_array($option) && isset($option['value']) )
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