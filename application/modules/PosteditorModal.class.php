<?php
/**
 * File contains the PosteditorModal class.
 * 
 * @package post-editor
 */

/**
 * This class handles the iframe contents for the modal popup tinymce plugin.
 * 
 * The format of adding an action is:
 * - The name space is in the format {sourceData}_to_{resultData} for example, 
 *   converting excel to html tables, the namespace would be excel_to_tables
 * - Register the action with the module by adding to the param $this->action in
 *   the format $action_namespace => tab text
 * - Add any js and css files to POSTEDITOR_DIR . /public_html/js|css in the
 *   formate PosteditorModal_{$namespace}.js|css respectfully. Register these in
 *   public methods $this->load_styles() and $this->load_scripts() in the new
 *   action class file.
 * - The class for the action should be located in modules folder and
 *   called PosteditorModal_{$namespace}.class.php
 *
 * @author daithi
 * @package post-editor
 */
class PosteditorModal {

	/** @var object The current action object */
	private $action;
	/** @var string Holds the html from the view file for parsing */
	private $html;
	/** @var array An array of shortcode=>value pairs for the view file */
	private $shortcodes;

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
		$this->actions = array(
			'excel_to_table' => 'Tables (Excel > HTML)',
			'' => 'Extension Title'
		);
		$this->html = "";
		$this->shortcodes = array();

		add_action('admin_init', array(&$this, 'admin_init'));	//look for actions
		//add_action('wp_head', array(&$this, 'admin_head'));		//write javascript globals to head - @deprecated
	}

	/**
	 * Constructs the action object.
	 */
	public function admin_init() {

		//check for modal action
		if(@$_REQUEST['posteditormodal_action'])
			$action = $_REQUEST['posteditormodal_action'];
		else return;

		require_once( POSTEDITOR_DIR . "/application/modules/PosteditorModal_{$action}.class.php");
		$class = "PosteditorModal_{$action}";
		$this->action = new $class();
		
	}

	/**
	 * Adds global javascript vars to the admin head.
	 * 
	 * @deprecated
	 */
	public function admin_head(){
		/**
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
		 * 
		 */
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
		$this->shortcodes['get tabs'] = $this->get_tabs();
		$this->shortcodes['action page'] = $this->action->get_page();
		$this->set_shortcodes();
		
		//scripts and style
		$this->load_scripts();
		$this->action->load_scripts();
		$this->load_styles();
		$this->action->load_styles();
		
		//iframe head
		?><html><head><?php
		wp_enqueue_style('media');
		wp_enqueue_style('colors');
		wp_head();
		?></head><?php
		
		//iframe body
		?><body id="media-upload" class="js"><?php
		print $this->html;
		
		//footer and die()
		wp_footer();
		?></body></html>
		<?php
		die();
	}

	/**
	 * Returns the html for the tabs at the top of the modal window.
	 *
	 * @return string 
	 */
	private function get_tabs(){
		
		$html = "";
		
		foreach($this->actions as $action=>$tab){
			(@$_REQUEST['posteditormodal_action']==$action) ? $class='class="current"' : $class='';
			$html .= "<li><a {$class} href=\"/wp-admin/admin-ajax.php?action=get_modal_editor&posteditormodal_action={$action}&_wpnonce={$_REQUEST['_wpnonce']}&TB_iframe=true\">{$tab}</a></li>\n";
		}
		
		return $html;
	}
	
	/**
	 * Loads javascript files
	 * 
	 * @return void 
	 */
	private function load_scripts() {
		
		wp_register_script('posteditormodal', POSTEDITOR_URL . "/public_html/js/PosteditorModal.js");
		
		wp_enqueue_script('posteditormodal',array(
			'jquery'
		));
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
