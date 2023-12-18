<?php
namespace Jorms;
if(!defined('ABSPATH')) exit; // Exit if accessed directly



Jorms()->add_validation_rule( 'zip', 'validate_zip');


function validate_zip( $zip ){
    return preg_match( '/^\d{3}-\d{4}$/', $zip ) || preg_match( '/^\d{7}$/', $zip );
}

function validate_tele( $tele ){
    return preg_match('/^[\+0-9\-\(\)\s]*$/', $tele );
}


function validate_date( $date ){
    return preg_match( '/^\d{4}[\/\-](0?[1-9]|1[012])[\/\-](0?[1-9]|[12][0-9]|3[01])$/', $date );
}

function validate_time( $time ){
    return preg_match( '/^(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/', $time );
}