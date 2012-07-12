<?php
/**
 * @package tableizer
 */
/*
Plugin Name: Tableizer
Plugin URI: http://david-coombes.com
Description: create html tables from excel data copy and pasted to a textarea
Version: 2.5.6
Author: David Coombes
Author URI: http://david-coombes.com
*/

//debug?
error_reporting(E_ALL);
ini_set("display_errors", "on");

//constants
define("TABLEIZER_DIR", WP_PLUGIN_DIR . "/" . basename(dirname(__FILE__)));
define("TABLEIZER_URL", WP_PLUGIN_URL . "/" . basename(dirname(__FILE__)));

//vars
$tableizer_action = @$_REQUEST['tableizer_action'];
$tableizer_error = array();
$tableizer_message = array();

//include files
require_once( TABLEIZER_DIR . "/application/includes/debug.func.php");
require_once( TABLEIZER_DIR . "/application/Tableizer.class.php");

//construct plugin objects
$tableizer = new Tableizer();

/**
 * Actions and Filters
 */
add_action('admin_head', array($tableizer, 'admin_head'));
add_action('admin_menu', array($tableizer, 'admin_menu'));
register_activation_hook(__FILE__, 'tableizer_INSTALL');

/**
 * Adds an error to the errors array.
 *
 * @global array $tableizer_error
 * @param string $msg The error message
 */
function tableizer_error( $msg ){
	global $tableizer_error;
	$tableizer_error[] = $msg;
}

/**
 * Builds up the error box from the errors array.
 *
 * @global array $tableizer_error
 * @return string 
 */
function tableizer_get_errors(){
	
	global $tableizer_error;
	$html = "<div id=\"message\" class=\"error\"><ul>\n";

	if (!count($tableizer_error))
		return false;

	foreach ($tableizer_error as $error)
		$html .= "<li>{$error}</li>\n";

	return $html . "</ul></div>\n";
}

/**
 * Builds up the messages box from the messages array.
 *
 * @global array $tableizer_message
 * @return string 
 */
function tableizer_get_messages(){
	
	global $tableizer_message;

	$html = "<div id=\"message-1\" class=\"updated\"><ul>\n";

	if (!count($tableizer_message))
		return false;

	foreach ($tableizer_message as $msg)
		$html .= "<li>{$msg}</li>\n";

	return $html . "</ul></div>\n";
}

/**
 * Adds a message to the messages array.
 *
 * @global array $tableizer_message
 * @param string $msg 
 */
function tableizer_message( $msg ){
	
	global $tableizer_message;
	$tableizer_message[] = $msg;
}

/**
 * The plugin install callback.
 * 
 * @deprecated
 */
function tableizer_INSTALL(){
	;
}
?>