<?php
/*
 *
 * Plugin Name: Samx Simple Bootstrap Slider
 * Plugin URI:  http://www.xsamprasanna.com
 * Description: Easy to use slideshow plugin. Simple implementation of a bootstrap Carousel into WordPress, responsive slideshows with Bootstrap Carousel. Short code : [sxbosp_carousel] .
 * Version:     1.0
 * Author:      Sam Prasanna X
 * Author URI:  http://www.xsamprasanna.com
 * License:     GPL-2.0+
 * Copyright:   2015 SamX LTD
 *
 *
*/

//X02 Creating the Admin Slideshow Management Functionality

function sxbosp_init() {
    $args = array(
        'public' => true,
        'label' => 'Samx Bootstrap Slider',
        'supports' => array(
            'title',
            'thumbnail'
        )
    );
    register_post_type('sxbosp_images', $args);
}
add_action('init', 'sxbosp_init');


//X03 Including the Nivo Slider Scripts and Styles

add_action('wp_print_scripts', 'sxbosp_register_scripts');
add_action('wp_print_styles', 'sxbosp_register_styles');

function sxbosp_register_scripts() {

        // register
        wp_register_script('sxbosp_nivo-script', plugins_url('js/jquery-1.10.2.min.js', __FILE__), array( 'jquery' ));
        wp_register_script('sxbosp_boot_min_script', plugins_url('js/bootstrap.min.js', __FILE__), array( 'jquery' ));
        wp_register_script('sxbosp_script', plugins_url('js/sxbosp_script.js', __FILE__));

        // enqueue
        wp_enqueue_script('sxbosp_nivo-script');
        wp_enqueue_script('sxbosp_boot_min_script');
        wp_enqueue_script('sxbosp_script');

        $interval    = (get_option('sxbosp_interval') == '') ? 4000 : get_option('sxbosp_interval');
        $pause    = (get_option('sxbosp_pause') == 'hover') ? hover : false;
        
        $config_array = array(
                'interval' => $interval,
                'pause' => $pause
            );
 
        wp_localize_script('sxbosp_script', 'setting', $config_array);

}
 
function sxbosp_register_styles() {
    // register
    wp_register_style('sxbosp_styles', plugins_url('css/bootstrap.min.css', __FILE__));
    wp_register_style('sxbosp_styles_theme', 'http://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css');
    wp_register_style('sxbosp_styles_custom', plugins_url('css/sxbosp_custom.css', __FILE__));
 
    // enqueue
    wp_enqueue_style('sxbosp_styles');
    wp_enqueue_style('sxbosp_styles_theme');
    wp_enqueue_style('sxbosp_styles_custom');
}


//X04 New Image Sizes

add_image_size('sxbosp_widget', 180, 100, true);
add_image_size('sxbosp_function', 600, 280, true);
add_theme_support( 'post-thumbnails' );


//X05 The PHP Function

function sxbosp_function($type='sxbosp_function') {
    $args = array(
        'post_type' => 'sxbosp_images',
        'posts_per_page' => 1
    );
    $result = '<div id="myCarousel" class="carousel slide carousel-fade sxbosp_images">  
      <div class="carousel-inner">';
        
        //the loop
        $loop = new WP_Query($args);
        while ($loop->have_posts()) {
            $loop->the_post();
        
            $the_url = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), $type);
            
            $result .= '<div class="item active">';
            $result .='<img title="'.get_the_title().'" src="' . $the_url[0] . '" data-thumb="' . $the_url[0] . '" alt="'.get_the_title().'"/>';
            
          $result .='<div class="container">
            <div class="carousel-caption">
              <h2><span>'.get_the_title().'</span></h2>
            </div>
          </div>
        </div>';

        }
        wp_reset_postdata();
        
        $args = array(
            'post_type' => 'sxbosp_images',
            'posts_per_page' => 5, 
            'offset' => 1
        );
        $loop = new WP_Query($args);
        while ($loop->have_posts()) {
            $loop->the_post();
        
            $the_url = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), $type);
            
            $result .= '<div class="item">';
            $result .='<img title="'.get_the_title().'" src="' . $the_url[0] . '" data-thumb="' . $the_url[0] . '" alt="'.get_the_title().'"/>';
            
          $result .='<div class="container">
            <div class="carousel-caption">
              <h2><span>'.get_the_title().'</span></h2>
            </div>
          </div>
        </div>';

        }
        wp_reset_postdata();

      $result .= '</div>
      <a class="left carousel-control" href="#myCarousel" data-slide="prev"><span class="fa fa-chevron-left fa-2x"></span></a>
      <a class="right carousel-control" href="#myCarousel" data-slide="next"><span class="fa fa-chevron-right fa-2x"></span></a>  
    </div>';
    
    return $result;
}


//X06 The Shortcode

add_shortcode('sxbosp_carousel', 'sxbosp_function');


//X07 add submenu page
add_action('admin_menu', 'setting_page');

function setting_page() {
	add_submenu_page( 'edit.php?post_type=sxbosp_images', 'Settings', 'Settings', 'manage_options', 'settings', 'setting_page_content' );
}

function setting_page_content() {
 
    $interval = (get_option('sxbosp_interval') != '') ? get_option('sxbosp_interval') : '2000';
    
    $pause  = (get_option('sxbosp_pause') == 'hover') ? 'checked' : '' ;

 
    $html = '</pre>
<div class="wrap"><form action="options.php" method="post" name="options">
<h2>Select Your Settings</h2>
' . wp_nonce_field('update-options') . '
<table class="form-table" width="100%" cellpadding="10">
<tbody>


 <tr>
    <td align="left" scope="row">
    <label>If hover slider pause</label><input type="checkbox" '.$pause.' name="sxbosp_pause" 
    value="hover" />

    </td> 
</tr>

<tr>
<td scope="row" align="left">
 <label>Transition Interval</label><input type="text" name="sxbosp_interval" value="' . $interval . '" /></td>
</tr>

</tbody>
</table>
 <input type="hidden" name="action" value="update" />
 
 <input type="hidden" name="page_options" value="sxbosp_pause,sxbosp_interval" />
 
 <input type="submit" name="Submit" value="Update" /></form></div>
<pre>
';

    echo $html;

}




