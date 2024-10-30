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

class MobileMe_Movie_Admin {

	function menu() {
		// Options Page
		$page = add_plugins_page(
			__('MobileMe Movie', 'mobileme-movie'),
			__('MobileMe Movie', 'mobileme-movie'),
			'manage_options',
			'mobileme-movie',
			array(&$this, 'form')
		);
		if (!$page)
			return;
		
		// Help
		add_contextual_help($page, '<p>' . __('', 'mobileme-movie') . '</p>' .
			'<p><strong>' . __('For more information:', 'mobileme-movie') . '</strong></p>' .
			'<p><a href="http://www.evolonix.com/" target="_blank">' . __('Evolonix', 'mobileme-movie') . '</a></p>');
		
		// Options page dependent actions
		add_action("admin_print_scripts-$page", array(&$this, 'enqueue_scripts'));
		add_action("admin_print_styles-$page", array(&$this, 'print_styles'));
		add_action("admin_notices", array(&$this, 'notices'));
		// Options page dependent filters
		add_filter('plugin_action_links', array(&$this, 'action_links'), 10, 2);
	}
	
	function init() {
		// Options
		$options = get_option('mobileme_movie');
		if ($options === false)
			add_option('mobileme_movie', $this->get_default_options());
		
		// Settings
		register_setting(
			'mobileme_movie_options',
			'mobileme_movie',
			array(&$this, 'validate_options')
		);
		
		// Actions
		add_action('admin_footer', array(&$this, 'footer'));
		// Filters
		add_filter('plugin_row_meta', array(&$this, 'row_meta'), 10, 2);
	}
	
	function enqueue_scripts() {
		wp_register_script('mobileme_movie_admin_script', plugins_url('js/admin.js', __FILE__), array('jquery'), false, true);
		wp_enqueue_script('mobileme_movie_admin_script');
	}
	
	function print_styles() {
		wp_register_style('mobileme_movie_admin_style', plugins_url('css/admin.css', __FILE__));
		wp_enqueue_style('mobileme_movie_admin_style');
	}
	
	function crop_image($src_file, $src_x, $src_y, $src_w, $src_h, $dst_w, $dst_h, 
		$src_abs = false, $dst_file = false, $image_type = 2) {
		
		if (is_numeric($src_file)) // Handle int as attachment ID
			$src_file = get_attached_file($src_file);
	
		$src = wp_load_image($src_file);
	
		if (!is_resource($src))
			return new WP_Error('error_loading_image', $src, $src_file);
	
		$dst = wp_imagecreatetruecolor($dst_w, $dst_h);
	
		if ($src_abs) {
			$src_w -= $src_x;
			$src_h -= $src_y;
		}
	
		if (function_exists('imageantialias'))
			imageantialias($dst, true);
	
		imagecopyresampled($dst, $src, 0, 0, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
	
		imagedestroy($src); // Free up memory
	
		if (!$dst_file)
			$dst_file = str_replace(basename($src_file), 'cropped-' . basename($src_file), $src_file);
	
		if ($image_type == 3) {
			$dst_file = preg_replace('/\\.[^\\.]+$/', '.png', $dst_file);
			if (imagepng($dst, $dst_file))
				return $dst_file;
		}
		else {
			$dst_file = preg_replace('/\\.[^\\.]+$/', '.jpg', $dst_file);
			if (imagejpeg($dst, $dst_file, apply_filters('jpeg_quality', 90, 'wp_crop_image')))
				return $dst_file;
		}
		
		return false;
	}
	
	function fetch_file($url) {
		if ( function_exists("curl_init") ) {
			return $this->curl_fetch_file($url);
		} else if ( ini_get("allow_url_fopen") ) {
			return $this->fopen_fetch_file($url);
		}
	} // fetch_file
	function curl_fetch_file($url) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$file = curl_exec($ch);
		curl_close($ch);
		return $file;
	} // curl_fetch_file
	function fopen_fetch_file($url) {
		$file = file_get_contents($url, false, $context);
		return $file;
	} // fopen_fetch_file
	
	function notices() {
		global $current_screen;
		
		if ($current_screen->id == 'plugins_page_mobileme-movie') {
		    settings_errors('mobileme_movie_options');
		}
	}
		
	function action_links($links, $file) {
	    if ($file == plugin_basename(plugin_dir_path(__FILE__) . 'mobileme-movie.php')) {
			$links[] = '<a href="'. admin_url('plugins.php?page=mobileme-movie') . 
				'" title="' . __('Options for this plugin', 'mobileme-movie') . '">' . __('Options', 'mobileme-movie') . '</a>';
		}
		return $links;
	}
	
	function get_default_options() {
		$defaults = array(
			'example_textbox' => 'This is an example textbox.',
			'example_checkbox' => true,
			'example_dropdown' => 1,
			'example_radiobuttons' => 2,
			'example_textarea' => 'This is an example textarea.'
		);
		return $defaults;
	}
	
	function validate_options($options) {
		// Check if options should be reset to defaults.
		if (isset($_POST['reset'])) {
			add_settings_error(
				'mobileme_movie_options', 
				'reset', 
				__('Options have been reset.', 'mobileme-movie'), 
				'updated'
			);
			return $this->get_default_options();
		}
		
		$has_errors = false;
		
		// Validate the options.
		if (empty($options['example_textbox'])) {
			add_settings_error(
				'mobileme_movie_options', 
				'example_textbox_error', 
				__('Example Textbox is required.', 'mobileme-movie'), 
				'error'
			);
			$has_errors = true;
		}
		
		// Check for successful validation.
		if ($has_errors === false) {
			add_settings_error(
				'mobileme_movie_options', 
				'updated', 
				__('Options have been updated.', 'mobileme-movie'), 
				'updated'
			);
		}
		
		return $options;
	}
	
	function form() {
		if (!current_user_can('manage_options'))
			wp_die(__('You do not have permission to customize the options for this plugin.', 'mobileme-movie') );
		
		$options = get_option('mobileme_movie');
		
		// Use the step to have more options pages.
		/** Example form tag that might include an file input posting to a step 2:
		<form enctype="multipart/form-data" method="post" 
			action="<?php echo esc_attr(add_query_arg('step', 2)); ?>">
		</form>
		*/
		$step = 1;
		if (isset( $_GET['step']))
			$step = (int)$_GET['step'];
		if ($step < 1 || $step > 4)
			$step = 1;
		
		$this->options_form();
	}
	
	function options_form() {
		$options = get_option('mobileme_movie');
		?>
		
		<div class="wrap">
			<?php screen_icon(); ?>
			<h2><?php _e('MobileMe Movie', 'mobileme-movie'); ?></h2>
			
			<?php if (isset($_GET['settings-updated']) && $_GET['settings-updated']) : ?>
			<div id="message" class="updated">
				<p><?php printf(__('MobileMe Movie updated. <a href="%s">Visit your site</a> to see how it looks.', 'mobileme-movie'), home_url('/')); ?></p>
			</div>
			<?php endif; ?>
			
			<form method="post" action="options.php">
				<?php settings_fields('mobileme_movie_options'); ?>
				<table class="form-table">
					<tr valign="top">
						<th scope="row"><?php _e('Example Textbox', 'mobileme-movie'); ?></th>
						<td>
							<input type="text" id="example_textbox" name="mobileme_movie[example_textbox]"
								value="<?php echo $options['example_textbox']; ?>" />
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e('Example Checkbox', 'mobileme-movie'); ?></th>
						<td>
							<label for="example_checkbox" class="selectit">
								<input type="checkbox" id="example_checkbox" name="mobileme_movie[example_checkbox]"
									value="1" <?php checked($options['example_checkbox']); ?> />
								<span>This is an example checkbox.</span>
							</label>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e('Example Dropdown', 'mobileme-movie'); ?></th>
						<td>
							<select id="example_dropdown" name="mobileme_movie[example_dropdown]">
								<option value="0" <?php selected($options['example_dropdown'], 0); ?>></option>
								<option value="1" 
									<?php selected($options['example_dropdown'], 1); ?>>This is an example dropdown list.</option>
							</select>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e('Example Radiobuttons', 'mobileme-movie'); ?></th>
						<td>
							<label for="example_radiobutton1">
								<input type="radio" id="example_radiobutton1" name="mobileme_movie[example_radiobuttons]"
									value="1" <?php checked($options['example_radiobuttons'], 1); ?> />
								<span>This is the first example radiobutton.</span>
							</label><br />
							<label for="example_radiobutton2">
								<input type="radio" id="example_radiobutton2" name="mobileme_movie[example_radiobuttons]"
									value="2" <?php checked($options['example_radiobuttons'], 2); ?> />
								<span>This is the second example radiobutton.</span>
							</label><br />
							<label for="example_radiobutton3">
								<input type="radio" id="example_radiobutton3" name="mobileme_movie[example_radiobuttons]"
									value="3" <?php checked($options['example_radiobuttons'], 3); ?> />
								<span>This is the third example radiobutton.</span>
							</label>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e('Example Textarea', 'mobileme-movie'); ?></th>
						<td>
							<textarea id="example_textarea" name="mobileme_movie[example_textarea]"
								rows="5" cols="30"><?php echo $options['example_textarea']; ?></textarea>
						</td>
					</tr>
				</table>
				<p class="submit">
					<?php submit_button(null, 'primary', 'submit', false); ?>
					<?php submit_button(__('Reset Options', 'mobileme-movie'), 'secondary', 'reset', false, array(
						'onclick' => "javascript:return confirm('Are you sure you want to reset the options?');"
					)); ?>
				</p>
			</form>
		</div>
		
		<?php
	}
	
	function row_meta($links, $file) {
	    if ($file == plugin_basename(plugin_dir_path(__FILE__) . 'mobileme-movie.php')) {
			$links[] = '<a href="http://www.evolonix.com/donate/" target="_blank" title="' . 
				__('Donate', 'mobileme-movie') . '">' . __('Donate', 'mobileme-movie') . '</a>';
		}
		return $links;
	}
	
	function footer() {
		global $current_screen;
		
		if ($current_screen->id == 'plugins') {
			?>
			
			<script type="text/javascript">
				jQuery(document).ready(function($) {
					var column = $('tr#mobileme-movie td.column-description');
					$(column).addClass('clearfix');
					var logo = $('<a href="http://www.evolonix.com" target="_blank"><img src="<?php echo plugins_url('images/logo.png', __FILE__); ?>" alt="" /></a>');
					$(logo).css('border', 'dashed 1px #ddd');
					$(logo).css('padding', '2px');
					$(logo).css('float', 'right').css('margin-bottom', '5px').css('margin-left', '5px');
					$(logo).prependTo(column);
				});
			</script>
			
			<?php
		}
	}
	
	function before_admin_bar_render() {
	    global $wp_admin_bar;
	    
	    if (!current_user_can('manage_options'))
			return;
	    
	    if (!isset($wp_admin_bar->menu->evolonix)) {
	    	$wp_admin_bar->add_menu( array(
		        'parent' => false,
		        'id' => 'evolonix',
		        'title' => __('Evolonix'),
		        'href' => 'http://www.evolonix.com',
		        'meta' => array(
		        	'target' => '_blank',
		        	'title' => 'Evolonix - Software Built to Perfection'
		        )
		    ) );
	    }
	
	    $wp_admin_bar->add_menu( array(
	        'parent' => 'evolonix',
	        'id' => 'mobileme-movie',
	        'title' => __('MobileMe Movie', 'mobileme-movie'),
	        'href' => 'http://www.evolonix.com/wordpress/plugins/mobileme-movie',
	        'meta' => array(
	        	'target' => '_blank'
	        )
	    ) );
	}

}

?>
