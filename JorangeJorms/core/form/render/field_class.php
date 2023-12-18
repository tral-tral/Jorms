<?php
namespace Jorms;
if(!defined('ABSPATH')) exit; // Exit if accessed directly

abstract class Jorms_field{

    private Jorms_sanitize_type $sanitize_type = Jorms_sanitize_type::text;

    public function __construct(){
        Jorms()->add_field( $this );


        $this->register_scripts();
        $this->register_styles();

        $this->init();
    }



    public function init(){}

    abstract function get_name();
    abstract function render_html( $field );
    abstract function validate( $value, $setup );

    function register_scripts(){


        $scripts = $this->get_scripts();

        if( empty( $scripts ) ) return;

        if( !is_array( $scripts ) ) $scripts = array( $scripts );

        foreach( $scripts as $script ){
            script_reg( $script );
        }

    }

    function register_styles(){

        $styles = $this->get_styles();

        if( empty( $styles ) ) return;

        if( !is_array( $styles ) ) $styles = array( $styles );

        foreach( $styles as $style ){
            style_reg( $style );
        }

    }

    function get_scripts(){
        return false;
    }

    function get_styles(){
        return false;
    }


    function sanitize_type(): Jorms_sanitize_type
    {
       return Jorms_sanitize_type::text;
    }

    function sanitize( $value ){

        if( is_array( $value ) )
            return $this->iterate_input_array( $value );
        return $this->sanitize_input( $value );

    }

    private function iterate_input_array( $array ){
        foreach( $array as $key=>$value ){
            iF( is_array( $value ) )
                $array[ $key ] = $this->iterate_input_array( $value  );
            else $array[ $key ] =$this->sanitize_input( $value );
        }
        return $array;
    }

    function sanitize_input( $value ){
        if( jorms_empty($value ) ) return $value;
        $type = $this->sanitize_type();

        $value = str_replace("'",'’', $value );
        $value = str_replace('"','”', $value );

        return match ($type) {
            Jorms_sanitize_type::textarea => sanitize_textarea_field($value),
            Jorms_sanitize_type::int => absint($value),
            Jorms_sanitize_type::text => sanitize_text_field($value),
            default => sanitize_text_field($value),
        };

    }



    function validate_field( $value, $setup){

        if(  empty($setup['required']) && jorms_empty( $value ) ) return true;

        $valid = $this->validate( $value, $setup ) && $this->validate_extra_rules( $value, $setup);
        $valid = apply_filters('jorms_validate_field', $valid, $setup );
        $valid = apply_filters('jorms_validate_field_' . $this->get_name(), $valid, $setup );

        return $valid;
    }

    function validate_extra_rules( $value, $setup ){
        //Check rules block
        $valid = true;
        $validation = $setup['validation'] ?? [];
        foreach( $validation as $rule ){
            $valid = $valid && $this->check_validation_rule( $rule, $value );
        }
        return $valid;
    }

    function check_validation_rule( $rule, $value ){
        $rule = Jorms()->get_validation_rule( $rule );
        return $rule && $rule( $value );
    }

    function render( $field ){
        $field = $this->prepare_field_data( $field );
        $field_class =  apply_filters('jorms_field_class', 'jorms-form-field' , $field );
        $size        = apply_filters('jorms_field_class_size', 'jorms-form-size-' . $field['size'], $field );
        echo '<div class="' . $field_class . ' ' . $size . '" data-type="'. $field['type'] . '" data-name="' . $field['name'] . '"' .  ( $field['required'] ? "required": '' ) . '>';
        $this->render_html( $field );
        echo '</div>';

    }

    function prepare_field_data( $field ){

        $field['options'] = $field['options'] ?? [];
        $field['options'] = is_array( $field['options'] ) ? $field['options'] : array( $field['options'] );

     //   $field['value_array'] = apply_filters('jorms_field_value_array', $field['value_array'] , $field );
        $field['amount']      = apply_filters('jorms_field_amount', $field['amount'] ?? 1, $field );
        $field['label']       = apply_filters('jorms_field_label', $field['label'] ?? '', $field );
        $field['data'] 	      = apply_filters('jorms_field_data', $field['data'] ?? array(), $field );
        $field['name']        = apply_filters('jorms_field_name', $field['name'] ?? '', $field );
        $field['value']       = apply_filters('jorms_field_value', $field['value'] ?? false , $field );

        $field['required']    = apply_filters('jorms_field_required', $field['required'] ?? '', $field );

        $field['readonly']    = apply_filters('jorms_field_readonly', $field['readonly'] ?? '', $field );

        $field['type']        = apply_filters('jorms_field_type', $field['type'] ?? '', $field );
        $field['class']       = apply_filters('jorms_field_class', $field['class'] ?? '', $field );
        $field['style']       = apply_filters('jorms_field_style', $field['style'] ?? '', $field );
        $field['size']	      = apply_filters('jorms_field_size', $field['size'] ?? '100', $field );
        $field['options']     = apply_filters('jorms_field_options', $field['options'] , $field );
        $field['max'] 	      = apply_filters('jorms_field_max', $field['max'] ?? false, $field );
        $field['min']	      = apply_filters('jorms_field_min', $field['min'] ?? false, $field );
        $field['step']	      = apply_filters('jorms_field_step', $field['step'] ?? false, $field );
        $field['maxlength']   = apply_filters('jorms_field_max', $field['maxlength'] ?? false, $field );
        $field['minlength']	  = apply_filters('jorms_field_min', $field['minlength'] ?? false, $field );
        $field['field_id']    = apply_filters('jorms_field_id', $field['name'] . $field['counter']  , $field );

        return $field;
    }

    public function get_html( $field ){
        ob_start();
        $this->render( $field );
        return  ob_get_clean();
    }

}