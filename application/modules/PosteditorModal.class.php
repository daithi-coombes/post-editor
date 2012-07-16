<?php
/**
 * File contains the PosteditorModal class.
 * 
 * @package post-editor
 */

/**
 * This class handles the iframe contents for the modal popup tinymce plugin.
 * 
 * Over time more actions will be added to this module, the format of adding an
 * action is:
 *  * namepsace: the name space is in the format {sourceData}_to_{resultData}
 * for example, converting excel to html tables, the namespace would be
 * excel_to_tables
 *  * Add a button to the view file PosteditorModal.php with a name of
 * "posteditormodal_action" and value of "{$namespace}"
 *  * add method to this class called: action_{$namespace} This method should
 * load the below class and act as a router for that classes methods and calls.
 *  * class: the class for the action should be located in modules folder and
 * called: PosteditorModal_{$namespace}.class.php
 *
 * @author daithi
 * @package post-editor
 */
class PosteditorModal {

	/** @var string Holds the html from the view file for parsing */
	private $html;

	/** @var string The html result for the modal popup */
	private $modal_result;

	/** @var array An array of shortcode=>value pairs for the view file */
	private $shortcodes;

	/** @var string The content for the textarea in the modal popup */
	private $textarea_content;

	/**
	 * constructor
	 */
	public function __construct() {

		global $posteditor_action;

		require_once( ABSPATH . 'wp-includes/pluggable.php');
		
		//security check
		if(@$_REQUEST['action']=='get_modal_editor')
			if(!wp_verify_nonce($_REQUEST['_wpnonce'],"post editor modal")) die('Invalid nonce');
		
		//default params
		$this->html = "";
		$this->modal_result = "";
		$this->shortcodes = array();
		(@$_POST['data']) ? $this->textarea_content=$_POST['data'] : $this->textarea_content = "";

		add_action('admin_init', array(&$this, 'admin_init'));	//look for actions
		add_action('wp_head', array(&$this, 'admin_head'));		//write javascript globals to head
	}

	/**
	 * Calls any actions for the PosteditorModal.
	 */
	public function admin_init() {

		//check for modal action
		(@$_POST['posteditormodal_action']) ?
			$action = "action_{$_POST['posteditormodal_action']}" :
			$action = false;

		//if modal action call it
		if ($action)
			if (method_exists($this, $action))
				$this->$action();		

	}

	/**
	 * Adds global javascript vars to the admin head.
	 */
	public function admin_head(){
		
		(!empty($this->modal_result)) ? $show_results='true' : $show_results='false';
		
		//print global vars
		?>
		<script type="text/javascript">
			var posteditor_url = '<?=POSTEDITOR_URL?>';
			var theme_dir = '<?=bloginfo('template_directory')?>';
			var posteditor_show_results = <?=$show_results?>;
			var posteditor_modal_nonce = '<?php echo wp_create_nonce("post editor modal"); ?>';
		</script>
		<?php
	}
	
	/**
	 * Prints the view html.
	 * 
	 * Loads the html then sets shortcodes ( @see PosteditorModal::set_shortcodes() )
	 * then loads scripts (@see PosteditorModal::load_scripts() ) and styles
	 * (@see PosteditorModal::load_styles() ) then prints html
	 * @return void
	 */
	public function get_page() {

		//set params
		$this->html = file_get_contents(POSTEDITOR_DIR . "/public_html/PosteditorModal.php");
		$this->shortcodes['modal result'] = $this->modal_result;
		$this->shortcodes['textarea content'] = $this->textarea_content;

		//build includes
		$this->load_scripts();
		$this->load_styles();
		$this->set_shortcodes();

		//head
		?><head><?php
		wp_head();
		?></head><body><?php
		
		//print view file
		print $this->html;

		//print footer and die() for ajax
		wp_footer();
		?></body></html><?php
		die();
	}

	/**
	 * Runs any excel_to_table actions.
	 * 
	 * @see PosteditorModal_excel_to_table
	 */
	private function action_excel_to_table() {

		//load modal action module
		require_once( POSTEDITOR_DIR . "/application/modules/PosteditorModal_excel_to_table.class.php");
		$action = new PosteditorModal_excel_to_table();

		$this->modal_result = $action->build_table();
	}

	/**
	 * Loads javascript files
	 * 
	 * @return void 
	 */
	private function load_scripts() {
		
		wp_register_script('posteditormodal-tinymce', POSTEDITOR_URL . "/application/includes/tinymce/jscripts/tiny_mce/jquery.tinymce.js");
		wp_register_script('posteditormodal', POSTEDITOR_URL . "/public_html/js/PosteditorModal.js", array(
			'jquery',
			'posteditormodal-tinymce'
		));
		
		wp_enqueue_script('posteditormodal');
	}

	/**
	 * Loads css files
	 * 
	 * @return void 
	 */
	private function load_styles() {
		;
	}

	/**
	 * Sets values for the shortcodes in the view file.
	 * 
	 * Replaces the codes with values in @see PosteditorModal::$html . To add
	 * shortcodes to the view file use the syntax:
	 * <code> <!--[--identifying string--]--> </code>. In the construct of this
	 * class add the value to the array @see PosteditorModal::$shortcodes.
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
