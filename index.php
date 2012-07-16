<?php
/**
 * @package post-editor
 */
/*
Plugin Name: Post Editor
Plugin URI: http://david-coombes.com
Description: Add a model for manipulating pasted data on the wordpress posts/page editor. Modal will be called from button on normal tinyMCE toolbar
Version: 0.1
Author: David Coombes
Author URI: http://david-coombes.com
*/

//debug?
error_reporting(E_ALL);
ini_set("display_errors", "on");

//constants
define("POSTEDITOR_DIR", WP_PLUGIN_DIR . "/" . basename(dirname(__FILE__)));
define("POSTEDITOR_URL", WP_PLUGIN_URL . "/" . basename(dirname(__FILE__)));

//vars
$posteditor_action = @$_REQUEST['posteditor_action'];
$posteditor_error = array();
$posteditor_message = array();

//include files
require_once( POSTEDITOR_DIR . "/application/includes/debug.func.php");
require_once( POSTEDITOR_DIR . "/application/Posteditor.class.php");
require_once( POSTEDITOR_DIR . "/application/modules/PosteditorModal.class.php");

//construct plugin objects
$posteditor = new Posteditor();
$posteditor_modal = new PosteditorModal();

/**
 * Actions and Filters
 */
add_action('admin_init', array($posteditor, 'admin_init'));
add_action('admin_head', array($posteditor, 'admin_head'));
add_action('init', array($posteditor, 'init'));
add_action('wp_ajax_get_modal_editor',array($posteditor_modal,'get_page'));
register_activation_hook(__FILE__, 'posteditor_INSTALL');

/**
 * Adds an error to the errors array.
 *
 * @global array $posteditor_error
 * @param string $msg The error message
 */
function posteditor_error( $msg ){
	global $posteditor_error;
	$posteditor_error[] = $msg;
}

/**
 * Builds up the error box from the errors array.
 *
 * @global array $posteditor_error
 * @return string 
 */
function posteditor_get_errors(){
	
	global $posteditor_error;
	
	$html = "<div id=\"message\" class=\"error\"><ul>\n";

	if (!count($posteditor_error))
		return false;

	foreach ($posteditor_error as $error)
		$html .= "<li>{$error}</li>\n";

	return $html . "</ul></div>\n";
}

/**
 * Builds up the messages box from the messages array.
 *
 * @global array $posteditor_message
 * @return string 
 */
function posteditor_get_messages(){
	
	global $posteditor_message;

	$html = "<div id=\"message-1\" class=\"updated\"><ul>\n";

	if (!count($posteditor_message))
		return false;

	foreach ($posteditor_message as $msg)
		$html .= "<li>{$msg}</li>\n";

	return $html . "</ul></div>\n";
}

/**
 * Adds a message to the messages array.
 *
 * @global array $posteditor_message
 * @param string $msg 
 */
function posteditor_message( $msg ){
	
	global $posteditor_message;
	$posteditor_message[] = $msg;
}
?>