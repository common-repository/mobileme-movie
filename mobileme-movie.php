<?php
/*
Plugin Name: MobileMe Movie
Plugin URI: http://www.evolonix.com/wordpress/plugins/mobileme-movie
Description: This plugin allows you to insert a movie you have hosted on your MobileMe Gallery's website in your post or page. Use [mobileme url="http://gallery.me.com/appleid#albumid/moviename" type="m4v or mov" width="width" height="height"] to insert the movie. The easiest way to get the "url" is to copy it from your browser's address bar when viewing the movie on the Gallery's site. The "type" attribute is optional if the movie is an .m4v file, which it uses by default, but required for .mov files. If it doesn't work without specifying the "type" attribute, then try adding it with the "mov" value. The "width" and "height" attributes are optional and default to width="640" and height="360". If one is specified, the other must be also, otherwise it uses the defaults.
Version: 1.3
Author: Evolonix
Author URI: http://www.evolonix.com
License: GPL2
Text Domain: mobileme-movie
*/
?>
<?php
/*  Copyright 2011  Evolonix  (email : info@evolonix.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
?>
<?php

// Install
// NOTE: References shortcode.
require_once(plugin_dir_path(__FILE__) . 'class-mobileme-movie-install.php');
register_activation_hook(__FILE__, array('MobileMe_Movie_Install', 'activate'));
register_deactivation_hook(__FILE__, array('MobileMe_Movie_Install', 'deactivate'));

// I18n
add_action('init', function() { load_plugin_textdomain('mobileme-movie', false, basename(dirname(__FILE__)) . '/languages'); });

// Admin
require_once(plugin_dir_path(__FILE__) . 'class-mobileme-movie-admin.php');
$mobileme_movie_admin = new MobileMe_Movie_Admin();
add_action('admin_init', array(&$mobileme_movie_admin, 'init'));
add_action('wp_before_admin_bar_render', array(&$mobileme_movie_admin, 'before_admin_bar_render')); // (optional)

// Front-end (optional)
require_once(plugin_dir_path(__FILE__) . 'class-mobileme-movie.php');
$mobileme_movie = new MobileMe_Movie();
add_action('plugins_loaded', array(&$mobileme_movie, 'init'));

// Shortcode (optional)
require_once(plugin_dir_path(__FILE__) . 'class-mobileme-movie-shortcode.php');
$mobileme_movie_shortcode = new MobileMe_Movie_Shortcode();
add_shortcode('mobileme', array(&$mobileme_movie_shortcode, 'content'));

?>
