<?php
if(!defined('ABSPATH')) exit; // Exit if accessed directly


class Fukun_UpdateVenueForm extends \Jorms\Jorms_form{

    public function __construct(){

        $this->handle = 'update-venue';
        $this->response = 'Venue updated';
        $this->submit_text = 'Update venue';
        $this->end_action   = 'reload';
    }




    function get_values_map(){
        $post_id = \FukuokaNight\get_user_venue();
        return [
            'highlights' =>  get_post_meta($post_id, 'highlights', true),
            'description' => get_post_meta($post_id, 'description', true),
        ];
    }

    function get_fields(){

        return [
            [
                'type' => 'textarea',
                'name' => 'description',
                'label' => 'Description',
                'required' => true,
                'maxlength' => 200
            ],

            [
                'type' => 'repeater',
                'label' => 'Highlights',
                //   'required' => true,
                'name' => 'highlights',
                'max'  => 6,
                'fields' => [
                    [
                        'type' => 'text',
                        'required' => true,
                        'maxlength' => 20,
                    ],
                ]
            ],
        ];

    }



    function completed(){

        $venue_id = \FukuokaNight\get_user_venue();

        if( !$venue_id ) {
            $this->invalid_action_responses[] = 'Could not locate user\'s venue.';
            $this->valid = false;
            return;
        }

        $venue_data = [
            'ID' => $venue_id,
            'meta_input' => [
                'description' => '%description%',
                'highlights'  => '%highlights%',
            ]
        ];


        $venue_data_parsed = $this->parse_args( $venue_data );

        //Re-sanitize description to remove extra linebreaks.
        $venue_data_parsed['meta_input']['description'] = sanitize_text_field( $venue_data_parsed['meta_input']['description'] );

        $updated_venue = wp_update_post(  $venue_data_parsed );

        if( is_wp_error( $updated_venue ) ){
            $this->invalid_action_responses[] = 'Update unexpected failed.';
        }

    }



}
