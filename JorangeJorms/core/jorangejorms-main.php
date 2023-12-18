<?php
namespace Jorms;
if(!defined('ABSPATH')) exit; // Exit if accessed directly

class Jorms{

    public static $instance;

    private $forms = [];
    private $counter = 0;
    private $validation_rules = [];
    private $actions = [];

    private $fields = [];

    private $script_data = [];

    public function __construct(){
        require_once( __DIR__ . '/form/jorangejorms-class.php');
        require_once( __DIR__ . '/form/jorms-field-handler.php');
        require_once( __DIR__ . '/form/render/enum.php');
        require_once( __DIR__ . '/form/render/field_class.php');
        add_shortcode( 'jorms_form', [$this, 'shortcode_form'] );

        style_reg('jorms');
        script_reg('jorms', ['ajaxurl' => admin_url('admin-ajax.php'),], [ 'jquery', 'jquery-ui-sortable' ] );

    }

    function form( $handle, $setup ){
        $setup['counter'] = count( $this->forms );
        $this->forms[ $handle ] = new Jorms_form( $handle, $setup );
        return $this->forms[ $handle ];
    }

    function add_form( Jorms_form $form ){

        $handle = $form->get_handle();
        $this->forms[ $handle ] = $form;
        $form->prepare();

        $this->counter = $this->counter + 1;

    }

    function get_counter(){
        return $this->counter;
    }

    function add_validation_rule( $rule, $callback ){
        if( is_callable( $callback ) ){
            $this->validation_rules[ $rule ] = $callback;
        }
    }

    function add_action( $action, $callback, $args = false ){
        if( is_callable( $callback ) ){
            if( !is_array( $args ) ) $args = array( $args );
            $this->actions[ $action ] = [ 'callback' => $callback , 'args' => $args ];
        }
    }


    function get_action( $action ){
        return $this->actions[$action] ?? false;
    }

    function get_rule( $rule ){
        return $this->validation_rules[ $rule ] ?? false;
    }



    function add_field( $field ){
        $field_name = $field->get_name();
        $this->fields[ $field_name ] = $field;
    }

    function get_field( $name ){
        return $this->fields[ $name ] ?? false;
    }


    function render_form( $handle ){
        if( isset( $this->forms[ $handle ] ) ){
            $this->forms[ $handle ]->render();
        }
    }


    function shortcode_form( $args ){
        if( empty( $args['handle'] ) )
            return '';
        ob_start();
        $this->render_form( $args['handle'] );
        return ob_get_clean();
    }




    function get_form( $handle ){
        return $this->forms[ $handle] ?? false;
    }

    function process( $handle, $input ){

        $form = $this->get_form( $handle );
        if( $form ) return $form->process( $input );

        return ['valid'=> false, 'response' => 'Form not found' ];
    }


    function get_validation_rule( $rule ){
        return $this->validation_rules[ $rule ] ?? [ 'callback'=> false, 'response' => 'Validation rule ({$rule}) not found' ];
    }


    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}

Jorms::instance();
