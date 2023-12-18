<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly


class Fukun_EventForm extends \Jorms\Jorms_form
{

    public function __construct()
    {

        $this->handle = 'new-event';
        $this->response = 'New Event Created';
        $this->submit_text = 'Add New Event';
        $this->end_action = 'reload';


    }

    function get_fields()
    {
        return [
            [
                'type' => 'image',
                'name' => 'poster',
                'label' => 'Poster',
                'required' => true,
                'amount' => 1,
            ],

            [
                'type' => 'text',
                'name' => 'title',
                'label' => 'Title',
                'required' => true,
                'maxlength' => 30,
                'minlength' => 4,
            ],

            [
                'type' => 'textarea',
                'name' => 'description',
                'label' => 'Description',
                'required' => true,
                'maxlength' => 200,
                'minlength' => 20,
            ],

            [
                'type' => 'date',
                'name' => 'date',
                'size' => '50',
                'label' => 'Date',
                'required' => true,
                'min' => date('Y-m-d'),
                'max' => date('Y-m-d', strtotime('today + 1 month')),
            ],
            [
                'type' => 'time',
                'name' => 'time',
                'size' => '50',
                'label' => 'Start time',
                'required' => true,
            ],

            [
                'type' => 'repeater',
                'label' => 'Highlights',
             //   'required' => true,
                'name' => 'highlights',
                'max'  => 4,
                'fields' => [
                    [
                        'type' => 'text',
                        'required' => true,
                        'maxlength' => 20,
                    ],
                ]
            ]
        ];
    }

    function completed(){



        $venue_id = \FukuokaNight\get_user_venue();

        if( !$venue_id ) {
            $this->invalid_action_responses[] = 'Could not locate user\'s venue.';
            $this->valid = false;
            return;
        }

        $data = [
            'ID' => 0,
            'post_title' => '%title%',
            'post_type' => 'events',
            'post_status' => 'publish',
            'post_author' => get_current_user_id(),
            'meta_input' => [
                'date' => '%date%',
                'time' => '%time%',
                'venue' => $venue_id,
                'highlights' => '%highlights%',
                'description' => '%description%',
                'poster' => '%poster%',
            //    'type' => '%type%',
            ]
        ];

        $data_parsed = $this->parse_args( $data );




        $imagesOkay = \FukuokaNight\check_attachmentIDs( $data_parsed['meta_input']['poster'] );

        if( !$imagesOkay ){
            $this->invalid_action_responses[] = 'Failed image check.';
            $this->valid = false;
            return;
        }




        //Re-sanitize description to remove extra linebreaks.
        $data_parsed['meta_input']['description'] = sanitize_text_field( $data_parsed['meta_input']['description'] );



        $new_event = wp_insert_post(  $data_parsed );



        \FukuokaNight\set_attachmentIDs( $data_parsed['meta_input']['poster'] , $new_event );

    }


}
