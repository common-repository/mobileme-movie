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

class MobileMe_Movie_Shortcode {

	function content($atts) {
		if (!isset($atts['url']))
			return;
		$url = $atts['url'];
		$type = 'm4v';
		if (isset($atts['type'])) {
			$type = $atts['type'];
		}
		$width = '640';
		$height = '360';
		if (isset($atts['width']) && isset($atts['height'])) {
			$width = $atts['width'];
			$height = $atts['height'];
		}
		
		$url = str_replace('#', '/', $url);
		$url = stristr($url, "&amp;bgcolor=", true);
		
		ob_start();
		?>
		
		<object codebase="http://www.apple.com/qtactivex/qtplugin.cab#version=6,0,2,0" 
			width="<?php echo $width; ?>" height="<?php echo $height; ?>" 
			classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B">
			<param value="<?php echo $url . '/video.' . $type; ?>" name="src">
			<object type="video/quicktime" data="<?php echo $url . '/video.' . $type; ?>" width="<?php echo $width; ?>" height="<?php echo $height; ?>">
				<param value="false" name="autoplay">
				<param value="true" name="controller">
				<param value="true" name="cache">
				<param value="false" name="kioskmode">
				<param value="false" name="showlogo">
				<param value="aspect" name="scale">
				<param value="true" name="saveembedtags">
			</object>
			<param value="false" name="autoplay">
			<param value="true" name="controller">
			<param value="true" name="cache">
			<param value="false" name="kioskmode">
			<param value="false" name="showlogo">
			<param value="aspect" name="scale">
			<param value="true" name="saveembedtags">
		</object>
		
		<?php
		$content = ob_get_contents();
		ob_end_clean();
		
		return $content;
	}
}

?>
