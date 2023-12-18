<?php
namespace Jorms;
if(!defined('ABSPATH')) exit; // Exit if accessed directly


class Jorms_form_fields_handler{

    private $fields;
    private $input;
    private $relevant_fields = [];
    private $invalid_responses = [];
    
    public function __construct( $fields, $input ){
        $this->fields = $fields;
        $this->input  = $input;
    }
    
    
    function process(){
        $this->set_inputs();
        $this->set_relevant_fields();
        $this->sanitize_inputs();
        $this->validate();
    }

    function set_inputs(){
        $names = $this->get_field_names();
        if( !is_array( $this->input ) )
            $this->input = [ $names[0] => $this->input ];

        else {
            foreach ($this->input as $key => $value) {
                if (!in_array($key, $names)) {
                    unset($this->input[$key]);
                }
            }
        }
    }
    
    function get_field_names(){
        $names = [];
        foreach( $this->fields as $field){
            if( isset( $field['name'] ) && !isset( $names[ $field['name'] ] ) )
                $names[] = $field['name'];
        }
        return $names;
    }

    function set_relevant_fields(){

        $relevant_fields = [];

        foreach( $this->fields as $field){

            $name       = $field['name'] ?? 'not-set';
            $required   = $field['required'] ?? false;
            $conditions = $field['conditions'] ?? false;
            $disabled   = false;
            $is_set     = isset( $this->input[ $name ] );

            if( !empty( $conditions ) ){
                //TODO: Add conditions for fields.
              //  $disabled = $this->check_condition( $conditions, $name );
            }

            //If the field is disabled or is not required and isn't sent in the input, do not include in processing.
            if( $disabled || !$required && !$is_set )
                continue;

            $relevant_fields[] = $field;
        }

        $this->relevant_fields = $relevant_fields;
    }
    
    
    function sanitize_inputs(){
        foreach( $this->relevant_fields as $field) {
            $name = $field['name'] ?? 'not-set';
            if (isset($this->input[$name])) {
                $this->input[$name] = Jorms()->get_field( $field['type'] )->sanitize($this->input[$name]);
            }
        }

    }


    function get_invalid_responses(){
        return $this->invalid_responses;
    }

    function get_input(){
        return $this->input;
    }

    function check_condition( $rules, $name ) {
        return false;
    }



    function validate(){
        if( empty( $this->input ) )
            return;
        foreach( $this->relevant_fields as $field ){
            $required   = $field['required'] ?? false;
            $name       = $field['name'] ?? false;
            $validation = $field['validation'] ?? [];
            $is_set     = isset( $this->input[ $name ] );
            
            if( $required && ( !$is_set || jorms_empty( $this->input[ $name] )  ) ){
                $this->invalid_responses[] = sprintf('Required field (%s) is missing', $name );
                continue;
            }
            $validated = Jorms()->get_field( $field['type'] )->validate_field( $this->input[ $name] , $field );

            if( $validated  && !empty( $validation ) )
                foreach( $validation as $rule ){
                    if( !Jorms()->get_rule( $rule )( $this->input[ $name ] ) ){
                        $validated = false;
                        break;
                    }
                }



            if( !$validated )
                $this->invalid_responses[] = sprintf('Field (%s) failed validation', $name );
        }
    }
    
}