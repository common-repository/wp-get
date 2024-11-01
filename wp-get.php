<?php
/*
Plugin Name: WP Get
Description: WP Get allows you to display content in posts dynamically, based on URL parameters.
Version: 1.1
Plugin URI: http://tinsology.net/plugins/wp-get/
Author URI: http://tinsology.net
Author: Mathew Tinsley
*/
/*
	Copyright 2011 Mathew Tinsley (email: tinsley@tinsology.net)
	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
class WPGetPlugin
{
	
	public function __construct()
	{
		add_shortcode('get', array($this, 'get'));
		add_shortcode('get_n', array($this, 'get'));
		add_shortcode('get_param', array($this, 'param'));
	}
	
	public function get($atts, $content = null)
	{
		$status = true;
		$or = false;
		$strict = false;
		
		if(isset($atts['_or']) && $atts['_or'] == 1)
		{
			$or = true;
			unset($atts['_or']);
		}
		
		if(isset($atts['_strict']) && $atts['_strict'] == 1)
		{
			$strict = true;
			unset($atts['_strict']);
		}
		
		foreach($atts as $param => $value)
		{
			if($strict && !isset($_GET[$param]))
				$status = false;
			else
			{
				$get = isset($_GET[$param]) ? $_GET[$param] : '';
				$status = $this->evaluateParam($get, $value);
			}

			if($or && $status == true)
				break; /* I'm ok with break in PHP foreach loops. Deal with it. */
			elseif(!$or && $status == false)
				break;
		}
		
		if($status)
			return do_shortcode($content);
			
		return '';
	}
	
	public function evaluateParam($get, $value)
	{
		$not = false;
		if($value[0] == '!')
		{
			$not = true;
			$value = substr($value, 1);
		}
		
		if($not && $get != $value)
			return true;
		elseif(!$not && $get == $value)
			return true;
			
		return false;
	}
	
	public function param($atts, $content = null)
	{
		$param = isset($atts['param']) ? $atts['param'] : '';
		
		$value = isset($_GET[$param]) ? $_GET[$param] : '';
		return esc_html($value);
	}
}
new WPGetPlugin();
?>