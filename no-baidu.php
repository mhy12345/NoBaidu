<?php  
/*
 * Plugin Name: No-baidu plugin
 * Plugin URI: http://mhy12345.xyz/no-baidu/
 * Description: 当访问者通过百度访问网站时，此插件将在页面的顶端，显示一段抵制信息.
 * Version: 1.1
 * Author: mhy12345
 * Author URI: http://mhy12345.xyz/no-baidu/
 * License: GPL
 * */

include 'no-baidu-options.php';

class NoBaiduPlugin
{
	protected static $instance;
	private $options;

	private function __construct() {
		add_filter('robots_txt', array($this, 'robots_txt_edit'), 10, 2);
		add_action("wp_head", array($this, "no_baidu_main"));
		register_activation_hook( __FILE__, array($this, 'no_baidu_install'));
		register_deactivation_hook( __FILE__, array($this, 'no_baidu_remove'));
		$this->options = get_option('no_baidu_option', false);
	}

	public static function get_instance() {
		if (null === self::$instance) {
			self::$instance = new self();
		}
		return self::$instance;

	}

	public function no_baidu_main(){
		if(stristr($_SERVER["HTTP_REFERER"],"www.baidu.com"))
		{
			if ($this->options['method'] == 0) {
				wp_enqueue_style( 'no_baidu_style', plugins_url( 'css/no-baidu-view-header.css', __FILE__ ));
				include "views/header.php";
			}
			else {
				wp_enqueue_style( 'no_baidu_style', plugins_url( 'css/no-baidu-view-page.css', __FILE__ ));
				include "views/page.php";
			}
		}
	}

	public function no_baidu_install() {  
		$no_baidu_options = Array();
		$no_baidu_options['method'] = 0;
		$no_baidu_options['change_robots'] = 0;
		$no_baidu_options['warn_text'] = '我们发现您是通过百度找到这个页面的，不过我们并不推荐这么做，不妨考虑用其他搜索引擎。';
		add_option("no_baidu_option", $no_baidu_options);
	}

	public function no_baidu_remove() {  
		delete_option("no_baidu_option");
	}
	public function robots_txt_edit($output, $public)
	{
		if ($this->options['change_robots'] == 1) {
			$output .= "User-agent: baiduspider\nDisallow: /\n";
			$output .=  apply_filters('robots_txt_rewrite_footer', "\n\n\n\n# This robots.txt file was modified by No-baidu: https://wordpress.org/plugins/robotstxt-rewrite/\n");
		}
		return $output;
	}

}



$no_baidu_plugin = NoBaiduPlugin::get_instance();

if (is_admin()) include_once ('no-baidu-options.php');
?>
