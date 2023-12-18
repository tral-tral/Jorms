<?php
namespace Jorms;
if(!defined('ABSPATH')) exit; // Exit if accessed directly

class switch_field extends Jorms_field{


    function get_name(){
        return 'switch';
    }

    function render_html( $field ){
        include __DIR__ . '/render/fields/switch.php';
    }

    function validate($value, $setup ){

    }
}