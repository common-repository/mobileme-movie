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

class MobileMe_Movie {

	function init() {
		// Actions
		add_action('wp_enqueue_scripts', array(&$this, 'enqueue_scripts'));
		add_action('wp_print_styles', array(&$this, 'print_styles'));
	}
	
	function enqueue_scripts() {
		wp_register_script('mobileme_movie_script', plugins_url('js/script.js', __FILE__), array('jquery'), false, true);
		wp_enqueue_script('mobileme_movie_script');
	}
	
	function print_styles() {
		wp_register_style('mobileme_movie_style', plugins_url('css/style.css', __FILE__));
		wp_enqueue_style('mobileme_movie_style');
	}
	
	function ensure_protocol($url) {
		if ( ! isset( $url ) )
			return '';
	
		if ( is_ssl() )
			$url = str_replace( 'http://', 'https://', $url );
		else
			$url = str_replace( 'https://', 'http://', $url );
	
		return esc_url_raw( $url );
	}
	
	function the_content($content) {
		return $content;
	}
	
}

?>
