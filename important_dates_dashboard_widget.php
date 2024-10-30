<?php
/**
 * @package Important_Dates_Dashboard_Widget
 * @version 1.02
 */
/*
Plugin Name: Important Dates Dashboard Widget
Plugin URI: https://vmahq.com
Description: Display important dates in a widget on the Wordpress dashboard.
Author: Doug Higson
Version: 1.02
Author URI: https://virtualmarketadvantage.com
*/


/* !0. TABLE OF CONTENTS */

/*
	1. Includes
  2. Shortcodes
  3. The Widget
  4. Admin Pages
*/


/* !1. INCLUDES */

// Advanced Custom Fields Settings

// Define path and URL to the ACF plugin.
define( 'IDDW_MY_VMA_PATH', get_stylesheet_directory() . '/includes/acf/' );
define( 'IDDW_MY_VMA_URL', get_stylesheet_directory_uri() . '/includes/acf/' );

// Include the ACF plugin.
include_once( plugin_dir_path( __FILE__ ) .'includes/acf/acf.php' );

// (Optional) Hide the ACF admin menu item.
add_filter('acf/settings/show_admin', 'iddw_my_vma_settings_show_admin_vma');
function iddw_my_vma_settings_show_admin_vma( $show_admin ) {
    return true;
}

// Important Dates CPT
include_once( plugin_dir_path( __FILE__ ) . 'cpt/important_dates.php');

/* !2. SHORTCODES */

//get the date
function iddw_vma_date_today($atts, $content = null) {
    extract( shortcode_atts( array(
            'format' => ''
        ), $atts ) ); 
     
    if ($atts['format'] == '') {
    $date_time .= date(get_option('date_format')); 
    }  else { 
    $date_time .= date($atts['format']); 
    } 
    return $date_time;
    } 
    add_shortcode('date-today','iddw_vma_date_today'); 
    	
/* !3. THE WIDGET */

// Registers our dashboard widget

add_action( 'wp_dashboard_setup', 'iddw_dashboard_add_widgets' );

// calls and names the dashboard widget

function iddw_dashboard_add_widgets() {
	wp_add_dashboard_widget( 'dashboard_widget_iddw', __( 'Important Dates', 'dw' ), 'vma_dash_wid_iddw_burger' );
} 

// inside the widget

function vma_dash_wid_iddw_burger() {

  query_posts( array( 'post_type' => 'important_dates' ) );
  if ( have_posts() ) {
  ?><div style="text-align:right;"> 
  <? echo "Today is ";
    echo do_shortcode( '<span style="color:#2271b1;"><strong>[date-today format="F j, Y"]</strong></span>' );
    ?> 
    </div>
    <hr>
    <div>
    <?php
   
    $args = array(  
        'post_type' => 'important_dates',
        'meta_key'  => 'important_date',
        'orderby'   => 'meta_value',
        'order'		  => 'ASC',
        'posts_per_page'  => '15',
    );

    $loop = new WP_Query( $args );   
    while ( $loop->have_posts() ) : $loop->the_post(); ?>
      <table style="table-layout: fixed ;width:100%;"><tr><td style="padding-left:25px;text-align: left;"> <?php the_field('important_date_name'); ?> </td><td id="iddw-dates" style="text-align: right;"> <?php the_field('important_date'); ?></span></td></tr></table>
      
<?php

  endwhile;

    wp_reset_postdata(); ?>
    <hr></div>
   <?php 
   // add new and view all buttons 
  } else {
    
    echo '<p>Click "Add New" to add your first date.</p>';
   
}

   ?>
<div style="text-align:right; padding-top: 10px; padding-bottom:10px;">
    <button onclick=window.location.href="/wp-admin/post-new.php?post_type=important_dates"; class='button button-primary button-large'>Add New</button> <button onclick=window.location.href="/wp-admin/edit.php?post_type=important_dates" class="button button-primary button-large";>View All</button>
</div>
    <?php

}
 

//Delete Old Dates

add_action( 'init', 'iddw_vma_delete_expired_events' );

function iddw_vma_delete_expired_events() 
{

$args = [
    'post_type'      => 'important_dates',
    'posts_per_page' => -1,
    'fields'         => 'ids', //only get post id's
    'meta_query'     => [
        [
           'key'     => 'important_date',
           'value'   => current_time( 'Ymd' ),
           'compare' => '<'
        ]
    ]
];

$importantdates = get_posts( $args );

if ( $importantdates ) {
    // Loop through the post ID's and delete the post
    foreach ( $importantdates as $id )
        wp_trash_post( $id );
    }
}


/* !4. ADMIN PAGES */

//Admin Columns

/*
 * Register columns to Important Dates post list
 */
function iddw_add_vma_columns ( $columns ) {
  return array_merge ( $columns, array ( 
    'important_date_name' => __ ( 'Name' ),
    'important_date'   => __ ( 'Date' ),

  ) );
}
add_filter ( 'manage_important_dates_posts_columns', 'iddw_add_vma_columns' );


/*
* Add column data to Important Dates post list
*/

function iddw_vma_important_dates_custom_column ( $column, $post_id ) {
  switch ( $column ) {
    case 'important_date_name':
      echo esc_html(get_post_meta ( $post_id, 'important_date_name', true ));
      break;
    case 'important_date':
      $important_date = esc_html (get_post_meta ( $post_id, 'important_date',  true ));
      echo esc_html ($fDate = date("F j, Y", strtotime($important_date)));
      break;
  }
}
add_action ( 'manage_important_dates_posts_custom_column', 'iddw_vma_important_dates_custom_column', 10, 2 );

// make edit all sortable

add_filter( 'manage_important_dates_posts_sortable_columns', 'iddw_vma_my_sortable_event_column' );

function iddw_vma_my_sortable_event_column( $columns ) {
  $columns['important_date_name'] = 'important_date_name';
  $columns['date_name'] = 'date_name';

  return $columns;
}

add_action( 'pre_get_posts', 'iddw_manage_wp_posts_be_qe_pre_get_posts', 1 );
function iddw_manage_wp_posts_be_qe_pre_get_posts( $query ) {

 if ( $query->is_main_query() && ( $orderby = $query->get( 'orderby' ) ) ) {
    switch( $orderby ) {
       case 'important_date_name':
          $query->set( 'meta_key', 'important_date_name' );
          $query->set( 'orderby', 'meta_value' );      
          break;

      case 'important_date':
          $query->set( 'meta_key', 'important_date' );
          $query->set( 'orderby', 'meta_value' );      
          break;
    }
 }
}

//remove admin columns

add_filter('manage_important_dates_posts_columns', function ( $columns ) 
{
  unset($columns['title'], $columns['date']);
  return $columns;
} );

//remove admin sub menus links

add_filter( 'post_row_actions', 'iddw_vma_remove_myposttype_row_actions' );
function iddw_vma_remove_myposttype_row_actions( $action )
{
    if ('important_dates' == get_post_type()) {
        unset($action['view']);
        unset($action['inline hide-if-no-js']);
    }
    return $action;
}




// remove admin notices

/*

add_filter( 'post_updated_messages', 'iddw_vma_post_published' );

function iddw_vma_post_published( $messages )
{
  if ( 'important_dates' === get_post_type() ){
    unset($messages['posts'][6]);
  } else {
    return $messages;
}
}


/**
* Replaces "Post" in the update messages for custom post types on the "Edit"post screen.
* For example, for a "Product" custom post type, "Post updated. View Post." becomes "Product updated. View Product".
*
* @param array $messages The default WordPress messages.
*/

function iddw_custom_update_messages_hamburgers( $messages ) {
  global $post, $post_ID;
  
  $post_types = get_post_types( array( 'show_ui' => true, '_builtin' => false ), 'objects' );
  
  foreach( $post_types as $post_type => $post_object ) {
  
      $messages[$post_type] = array(
          
          6  => sprintf( __( '%s published. ' ), $post_object->labels->singular_name, esc_url( get_permalink( $post_ID ) ), $post_object->labels->singular_name ),
          
          );
  }
  
  return $messages;
  }
  add_filter( 'post_updated_messages', 'iddw_custom_update_messages_hamburgers' );