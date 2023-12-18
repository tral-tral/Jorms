<?php
if(!defined('ABSPATH')) exit; // Exit if accessed directly

define('JORMS_PREFIX','jorms_');
define('JORMS_PATH', plugin_dir_path( __FILE__ ));
define('JORMS_URL',  plugin_dir_url( __FILE__ )  );
define('JORMS_INCLUDES_PATH', JORMS_PATH . 'includes/');
define('JORMS_TEMPLATES_PATH',  JORMS_PATH . 'templates/');
define('JORMS_ASSETS_PATH',  JORMS_PATH . 'assets/');
define('JORMS_STYLE_PATH', JORMS_ASSETS_PATH . 'css/');
define('JORMS_SCRIPTS_PATH', JORMS_ASSETS_PATH . 'js/');
define('JORMS_IMAGE_PATH', JORMS_ASSETS_PATH . 'images/');
define('JORMS_IMAGE_PATH_URL', JORMS_URL . 'assets/images/');
define('JORMS_STYLE_URL', JORMS_URL . 'assets/css/');
define('JORMS_SCRIPTS_URL', JORMS_URL . 'assets/js/');


//Extra settings
define('JORMS_GOOGLE_PLACE_API_KEY', '');
