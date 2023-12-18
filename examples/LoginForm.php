<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly


class LoginForm extends \Jorms\Jorms_form
{

    public function __construct()
    {
        $this->handle = 'login';
        $this->response = 'Login successful.';
        $this->submit_text = 'Login';
        $this->end_action = 'reload';

    }

    function get_fields(){

        return [
            [
                'type' => 'email',
                'name' => 'email',
                'label' => 'Your email',
                'required' => true,
            ],
            [
                'type' => 'password',
                'label' => 'Your password',
                'name' => 'password',
                'required' => true,
            ]
        ];
    }

    function completed(){

        $user_data = [
            'user_login' => '%email%',
            'user_password' =>  '%password%',
        ];

        $user_creds = $this->parse_args( $user_data );

        $signed_in_user = wp_signon($user_creds, is_ssl());

        if( is_wp_error( $signed_in_user ) ){
            $this->invalid_action_responses[] = 'Email or password is incorrect.';
            $this->valid = false;
        }

    }



}