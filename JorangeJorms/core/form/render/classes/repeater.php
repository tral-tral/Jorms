<?php
namespace Jorms;
if(!defined('ABSPATH')) exit; // Exit if accessed directly

class repeater extends Jorms_field{


    function get_name(){
        return 'repeater';
    }

    function render_html( $field ){
        add_filter('jorms_field_class', [$this, 'add_to_class' ] );
        add_filter('jorms_field_id', [$this, 'add_counter_to_id' ] );

        include __DIR__ . '/fields/set_field_to_variables.php';
        include __DIR__ . '/fields/repeater.php';
        remove_filter('jorms_field_class', [$this, 'add_to_class' ] );
        remove_filter('jorms_field_id', [$this, 'add_counter_to_id' ] );

    }

    function add_counter_to_id( $field_id ){
        return $field_id . '-{0}';
    }

    function add_to_class( $class ){
        return $class . '-repeater';
    }


    function validate( $value, $setup){

        if( !isset( $setup['fields'] ) ) return true; //Ignore this field if it has no fields.

        $is_single_field = count( $setup['fields'] ) === 1;
        $repeated_count  = $is_single_field ? count( $value ) : count( $value[0] );

        $fields = $setup['fields'];
        $min    = $setup['min'] ?? 0;
        $max    = $setup['max'] ?? false;

        if( $repeated_count < $min ||  $max && $repeated_count > $max  )
            return false;

        foreach( $value as $repeated_value ){

            if( $is_single_field ) {
                $repeated_value = ['not-set' => $repeated_value];
                $fields[0]['name'] = 'not-set';
            }

            $field_handler = new Jorms_form_fields_handler( $repeated_value, $fields  );
            $field_handler->process();
            if( !empty( $field_handler->get_invalid_responses() ) )
                return false;
        }

        return true;
    }

}