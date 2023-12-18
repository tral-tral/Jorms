<?php
namespace Jorms;
if(!defined('ABSPATH')) exit; // Exit if accessed directly

class select2 extends select{


    function get_name(){
        return 'select2';
    }


    function get_styles()
    {
        return ['select2'];
    }

    function get_scripts()
    {
        return ['select2.full.min'];
    }



    function validate($value, $setup ){
        return true;
    }

}