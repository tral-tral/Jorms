<?php
namespace Jorms;
if(!defined('ABSPATH')) exit; // Exit if accessed directly


class Jorms_form{


    private $counter;

    protected $handle = 'not-set';
    protected $fields = [];

    protected $actions = false;
    protected $title = '';


    //The protected id can be used to store set inputs that are included in the nonce. For example, the ID of a post you want to have updated.
    protected $protected_id = 0;


    //Used for loading in default values during render.
    protected $values_map = [];

    protected $end_action = false;
    protected $other_data = '';
    protected $response = '';

    protected $submit_text = 'submit';
    protected $submit_on_enter = false;

    protected $invalid_responses = [];
    protected $invalid_action_responses = [];
    protected $valid = true;

    //Variables only used for processing input into a form.
    private $input;





    public function __construct(){}


    //Can define actions in here instead of the actions array.
    protected function completed(){}


    public function get_handle(){
        return $this->handle;
    }

    public function get_input(){
        return $this->input;
    }


    //Can override and use a query parameter to load in post IDs you want to edit.
    public function get_protected_id(){
        return $this->protected_id;
    }

    function prepare( ){
        $this->counter = Jorms()->get_counter();
    }

    //Override these functions to use dynamic parameters that are only accessed when needed.
    function get_fields(){
        return $this->fields;
    }

    //Used to load in default values during render.
    function get_values_map(){
        return $this->values_map;
    }

    function get_actions(){
        return $this->actions;
    }

    function submit_on_enter(){
        return $this->submit_on_enter;
    }

    function filter_setup_fields(){
        $this->fields = $this->get_fields();
        if( !is_array( $this->fields ) )
            $this->fields = [];
        foreach( $this->fields as $index=>$field ){
            if( !isset( $field['type'] ) || !Jorms()->get_field( $field['type'] ) )
                unset( $this->fields[$index] );
        }
    }


    function enqueue_styles_and_scripts(){


        Jorms()->enqueue_styles_and_scripts();

        foreach( $this->fields ?? [] as $field){
            $type = $field['type'] ?? false;
            $field_object = Jorms()->get_field( $type );

            if( $field_object ){
                $field_scripts = $field_object->get_scripts();
                $field_styles  = $field_object->get_styles();
                if( !empty( $field_scripts )) enqueue_scripts( $field_scripts );
                if( !empty( $field_styles ))  enqueue_styles( $field_styles );
            }

        }
    }




    function process( $input, $protected_id = 0 ){


     //   wp_send_json_success( $input );

        $this->filter_setup_fields();

        $protected_id = sanitize_text_field( $protected_id );

        $field_handler = new Jorms_form_fields_handler( $this->fields, $input  );
        $field_handler->process();
        $this->invalid_responses = $field_handler->get_invalid_responses();
        $this->input = $field_handler->get_input(); //Keep sanitized and filtered inputs for action processing.

        //Insert the protected id into the input to use for actions.
        $this->input['jorms_protected_id'] = $protected_id;



        if( !empty( $this->invalid_responses ) )
            return [ 'valid' => false, 'response' => $this->invalid_responses ];


        $response = $this->get_response();

        $this->process_actions();
        $this->completed();

        if( !empty( $this->invalid_action_responses ) )
            $response = $this->invalid_action_responses;

        return [ 'valid' => $this->valid , 'response' => $response ];

    }

    protected function get_response(){
        return $this->response;
    }


    //Process the post validated actions, such as emailing or creating/editing a post.
    private function process_actions(){
        $this->actions = $this->get_actions();
        if( !empty( $this->actions ) )
            foreach( $this->actions as $action=>$args ){
                $action_settings = Jorms()->get_action( $action );
                if( $action_settings ){
                    $args_to_pass = [];
                    if( !empty( $action_settings['args'] ) && is_array( $args ) ){
                        foreach( $action_settings['args'] as $arg ){
                            if( isset( $args[ $arg ] ) )
                                $args_to_pass[ $arg ] = $this->parse_args( $args[ $arg ] );
                        }
                    }
                    $response = $action_settings['callback'](...$args_to_pass);
                    if( isset( $response['valid'] ) && !$response['valid'] ){
                        $this->invalid_action_responses[] = $response['response'] ?? $action . ' action failed.';
                    }

                } else $this->invalid_responses[] = 'Action not found ' .  $action;
            }
    }

    protected function parse_args( $value ){
        return is_array( $value ) ? $this->iterate_args_array( $value ) : $this->parse_action_arg_value( $value );
    }

    protected function iterate_args_array( $array ){
        foreach( $array as $key=>$value ){
            if( is_array( $value ) )
                $array[ $key ] = $this->iterate_args_array( $value );
            else $array[ $key ] = $this->parse_action_arg_value( $value );
        }
        return $array;
    }


    protected function parse_action_arg_value($string){
        if (is_array($string)) {
            wp_send_json_error(var_export($string, true), 500);
        }

        $placeholders = $this->input;
        $pattern = '/%([^%]+(?:\[[^\]]+\])*)%/'; // Regular expression pattern to match placeholders and dimensions

        $check_for_one = [];
        preg_match_all($pattern, $string, $check_for_one );

        if ( count($check_for_one[0]) === 1 && $check_for_one[0][0]===$string && isset( $placeholders[$check_for_one[1][0]] )  )
            return $this->get_array_value_recursive($placeholders, $check_for_one[1][0] );

        $replacedString = preg_replace_callback($pattern, function ($matches) use ($placeholders) {
            $placeholder = $matches[1];
            $value = $this->get_array_value_recursive($placeholders, $placeholder);

            if (is_array($value)) {
                return implode(', ', $value);
            }

            return $value;
        }, $string);

        return $replacedString;
    }

    private function get_array_value_recursive($array, $placeholder){
        $dimensions = explode('[', str_replace(']', '', $placeholder));

        foreach ($dimensions as $dimension) {
            if (isset($array[$dimension])) {
                $array = $array[$dimension];
            } else {
                return null; // Return null if any dimension is not found
            }
        }

        return $array;
    }

    function get_counter(){
        return $this->counter;
    }

    function get_title(){
        return $this->title;
    }
    function get_other_data(){
        return $this->other_data;
    }

    function render(){
        $this->filter_setup_fields();

        $this->enqueue_styles_and_scripts();

        $nonce_suffix = $this->get_protected_id();
        $nonce_string = 'jorms_form_nonce_' . $this->handle . $this->counter . $nonce_suffix;

        $nonce = wp_create_nonce(  $nonce_string );

        $values_map = $this->get_values_map();

        $title = $this->get_title();

        $submit_on_enter = $this->submit_on_enter();

        $other_data = $this->get_other_data();

        echo "<form novalidate='novalidate' class='jorms-form' data-jorms-form-action='".$this->end_action."' data-jorms='".$other_data."' data-jorms-form-handle='". $this->handle."' data-jorms-form-nonce-suffix='".$nonce_suffix."' data-jorms-form-nonce='".$nonce."' method='get' action=''>";

        if( !$submit_on_enter )
            echo "<button type='submit' disabled style='display: none' aria-hidden='true'></button>";

        if( !empty( $title ) )
            echo '<div class="jorms-form-form-title">' . $title . '</div>';

        foreach( $this->fields as $field){

            //Set value from values map for default values.
            if( isset( $values_map[ $field['name'] ] ) )
                $field['value'] = $values_map[ $field['name'] ];

            $this->render_field( $field );
        }

        echo '<input type="submit" value="' . $this->submit_text .'" /><div class="jorms-form-message"></div></form>';
    }



    function render_field( $field ){

        $field['counter'] = $this->counter;
        $type = $field['type'] ?? false;
        $field_object = Jorms()->get_field( $type );

        if( $field_object ){
            $field_object->render( $field );
        }

    }




}
