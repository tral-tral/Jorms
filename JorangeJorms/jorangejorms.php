<?php
/**

 * Plugin Name: Jorms
 * Plugin URI: https://orangerabbitcompany.com
 * Description: Generate forms.
 * Author: Jorange Jorm
 * Version: 0.01
 * Author URI: https://orangerabbitcompany.com
 *
 */
namespace Jorms;
if(!defined('ABSPATH')) exit; // Exit if accessed directly


require_once 'helpers/functions.php';

require_once 'jorangejorms-config.php';
require_once 'core/jorangejorms-main.php';
require_once 'core/jorangejorms-ajax.php';

require_once 'helpers/functions.php';
require_once 'helpers/extra_functions.php';
require_once 'helpers/validation-functions.php';
require_once 'helpers/action-functions.php';

require_once 'core/form/render/add_fields.php';
