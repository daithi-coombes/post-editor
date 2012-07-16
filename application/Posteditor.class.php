<?php

/**
 * File containing the Posteditor class.
 * 
 * @package post-editor
 */

/**
 * This class loads jquery-ui themes/plugins and tinymce custom plugin
 * 
 * The tinymce plugin can be found at:
 * POSTEDITOR_DIR . "/application/includes/tinymce..../posteditormodal/".
 * The plugin opens a dialog with an iframe pointing to wp-admin/admin-ajax.php.
 * The ajax call loads the view file for the PosteditorModal module.
 * 
 * For adding more buttons and actions to the dialog see the description in
 * PosteditorModal.
 * 
 *
 * @author daithi
 * @package post-editor
 */
class Posteditor {

	/** @var string Holds the html from the view file for parsing */
	private $html;

	/**
	 * constructor
	 */
	public function __construct() {
		;
	}

	/**
	 * Call methods just after dashboard admin loads.
	 */
	public function admin_init() {
		$this->load_scripts();
		$this->load_styles();
	}
	
	/**
	 * Adds global javascript vars to the &lt;head>.
	 */
	public function admin_head(){
		
		$nonce = wp_create_nonce("post editor modal");
		?>
		<script type="text/javascript">
			var posteditor_modal_nonce = '<?=$nonce?>';
		</script>
		<?php
	}
	
	/**
	 * Adds buttons to the wp editors tinymce buttons array.
	 *
	 * @see Posteditor::editor_tinymce()
	 * @param array $buttons
	 * @return array 
	 */
	public function editor_tinymce_btns($buttons) {
		array_push($buttons, "|", "posteditormodal");
		return $buttons;
	}

	/**
	 * Adds plugins to the wp editors tinymce plugins array.
	 *
	 * @see Posteditor::editor_tinymce()
	 * @param array $plugin_array
	 * @return string 
	 */
	public function editor_tinymce_plugins($plugin_array) {
		//$plugin_array['posteditormodal'] = POSTEDITOR_URL . '/application/includes/tinymce/jscripts/tiny_mce/plugins/posteditormodal/editor_plugin.js';
		$plugin_array['posteditormodal'] = POSTEDITOR_URL . '/application/includes/posteditormodal/editor_plugin.js';
		return $plugin_array;
	}

	/**
	 * Methods to be called just after wp core loads.
	 */
	public function init() {

		$this->editor_tinymce();
	}

	/**
	 * Loads javascript files
	 * 
	 * @return void 
	 */
	private function load_scripts() {

		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-ui-dialog');
		wp_enqueue_script('jquery-ui-draggable');
	}

	/**
	 * Loads css files
	 * 
	 * @return void 
	 */
	private function load_styles() {

		wp_register_style('jquery-ui-lightness', POSTEDITOR_URL . "/application/includes/jquery-ui/css/ui-lightness/jquery-ui-1.8.21.custom.css");
		wp_register_style('posteditor', POSTEDITOR_URL . "/public_html/css/Posteditor.css", array(
			'jquery-ui-lightness'
		));
		
		wp_enqueue_style('posteditor');
	}

	/**
	 * Registers callbacks to the tinymce filters.
	 *
	 * @return boolean
	 */
	private function editor_tinymce() {
		// Don't bother doing this stuff if the current user lacks permissions
		if (!current_user_can('edit_posts') && !current_user_can('edit_pages'))
			return false;

		// Add only in Rich Editor mode
		if (get_user_option('rich_editing') == 'true') {
			add_filter("mce_external_plugins", array(&$this, "editor_tinymce_plugins"));
			add_filter('mce_buttons', array(&$this, 'editor_tinymce_btns')); //'register_myplugin_button');
		}
		return true;
	}

	/**
	 * Sets values for the shortcodes in the view file.
	 * 
	 * Replaces the codes with values in @see Posteditor::$html . To add
	 * shortcodes to the view file use the syntax:
	 * <code> <!--[--identifying string--]--> </code>. In the construct of this
	 * class add the value to the array @see Posteditor::$shortcodes.
	 * eg: $this->shortcodes['identifying string'] = $this->method_returns_html()
	 * 
	 * @return void
	 */
	private function set_shortcodes() {
		foreach ($this->shortcodes as $code => $val)
			$this->html = str_replace("<!--[--{$code}--]-->", $val, $this->html);
	}

}

?>
