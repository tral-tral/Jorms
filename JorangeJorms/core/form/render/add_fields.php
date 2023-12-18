<?php
namespace Jorms;
if(!defined('ABSPATH')) exit; // Exit if accessed directly
$fields_to_add = [
    'switch_field','select', 'select2','image',
    'text', 'textarea', 'date','time', 'email', 'number',
    'tel','url','password', 'repeater',
    'radio', 'checkbox','step','place','video'];

foreach($fields_to_add as $field_name ){
    include __DIR__ . '/classes/' . $field_name . '.php';
    $field_name = '\Jorms\\' . $field_name;
    new $field_name;
}


