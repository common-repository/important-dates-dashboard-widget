<?php

function vma_register_my_vma_important_dates_iddw() {

	/**
	 * Post Type: Important Dates.
	 */

	$labels = [
		"name" => "Important Dates",
		"singular_name" => "Important Date",
        "add_new_item" => __("Add New Date"),
        "edit_item"   => __("Edit Date"),
	];

	$args = [
        
		"label" => $labels,
		"labels" => $labels,
		"description" => "",
		"public" => false,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => true,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"has_archive" => true,
		"show_in_menu" => false,
		"show_in_nav_menus" => true,
		"delete_with_user" => false,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"rewrite" => [ "slug" => "important_dates", "with_front" => true ],
		"query_var" => true,
		"menu_icon" => "dashicons-calendar-alt",
		"supports" => false,
		"show_in_graphql" => false,
        
	];

	register_post_type( "important_dates", $args );
}

add_action( 'init', 'vma_register_my_vma_important_dates_iddw' );



    



//ACF Data for Widget



if( function_exists('acf_add_local_field_group') ):

    acf_add_local_field_group(array(
        'key' => 'group_61e97ae02287e',
        'title' => 'Important Dates',
        'fields' => array(
            array(
                'key' => 'field_61e97b1b21057',
                'label' => 'Important Date Name',
                'name' => 'important_date_name',
                'type' => 'text',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => 'iddw-date-name',
                ),
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'maxlength' => '',
            ),
            array(
                'key' => 'field_61e97aee21056',
                'label' => 'Important Date',
                'name' => 'important_date',
                'type' => 'date_picker',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => 'iddw-important-date',
                ),
                'display_format' => 'm/d/Y',
                'return_format' => 'F j, Y',
                'first_day' => 1,
            ),
        
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'important_dates',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => '',
        'show_in_rest' => 0,
    ));
    
    endif;		



    