<?php

/**
 * Contains the main class for the tableizers package.
 * 
 * @package tableizer
 */

/**
 * Tableizer class.
 *
 * @author daithi
 */
class Tableizer {

	/** @var string Holds the html from the view file for parsing */
	private $html;
	/** @var array Associative array of shortcode=>value pairs */
	private $shortcodes;
	/** @var array An array of calls to be executed as the view file is being
	 *  printed.
	 */
	private $shortcodes_runtime;
	/** @var string The table html in the textarea - defaults to '' */
	private $table_html;

	/**
	 * constructor
	 */
	public function __construct() {

		global $tableizer_action;

		//default params
		$this->shortcodes = array();
		$this->shortcodes_runtime = array();
		$this->table_html = '';

		//calls for just after wp admin core loads
		add_action('admin_init', array(&$this, 'admin_init'));

		//check for actions
		if (!empty($tableizer_action))
			if (method_exists($this, $tableizer_action))
				add_action('admin_init', array(&$this, $tableizer_action));
	}

	/**
	 * @deprecated
	 */
	public function admin_head() {
		return;
		wp_tiny_mce();
		wp_enqueue_script('tiny_mce');
		?>
		<script type="text/javascript">
			tinyMCE.init({
				mode : "exact",
				elements : "data-form", //putting extra tetarea id seperated by comma to show WYSIWYG
				theme : "advanced",
				height:"250",

				// Theme options
				theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull",
				theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,| formatselect,fontselect,fontsizeselect",
				theme_advanced_buttons3 : "",
				theme_advanced_toolbar_location : "top",
				theme_advanced_toolbar_align : "left",
				theme_advanced_statusbar_location : "bottom",
				theme_advanced_resizing : true
			});
		</script>
		<?php

	}

	/**
	 * Calls required for just after wp admin core loads.
	 * 
	 * Calls $this->load_styles() and $this->load_scripts()
	 */
	public function admin_init() {

		$this->load_scripts();
		$this->load_styles();
	}

	/**
	 * Build the admin menu.
	 * 
	 * The root menu and menu's for modules are created here.
	 */
	public function admin_menu() {

		$this->menu = "tableizer";
		add_menu_page("Tableizer", "Tableizer", "administrator", "tableizer", array(&$this, 'get_page'));
	}

	/**
	 * Action for creating a new table.
	 * 
	 * Data is taken from $_POST['data'] and a html table is created. The html
	 * data is stored in $this->table_html. This method error reports and
	 * sets a success message if no errors.
	 *
	 * @return boolean
	 */
	public function create_tables() {

		//security check
		if (!wp_verify_nonce($_POST['_wpnonce'], 'create tables')) {
			tableizer_error("Invalid nonce");
			return false;
		}
		if (empty($_POST['data'])) {
			tableizer_error("No data sent");
			return false;
		}

		$html = "<table>\n";
		$data = array();
		$_POST['data'] = trim($_POST['data']);

		//split into rows
		$rows = explode("\n", $_POST['data']);

		//build data
		foreach ($rows as $row) {
			$data[] = explode("\t", $row);
		}

		//build html table
		foreach ($data as $row) {
			$html .= "\t<tr>\n";
			foreach ($row as $cell)
				$html .= "\t\t<td>" . trim($cell) . "</td>\n";
			$html .= "\t</tr>\n";
		}
		$html .= "</table>\n";

		$this->table_html = $html;
		tableizer_message("Table created successfully");

		return true;
	}

	/**
	 * Prints the view html.
	 * 
	 * Loads the html then sets shortcodes ( @see Tableizer::set_shortcodes() )
	 * then loads scripts (@see Tableizer::load_scripts() ) and styles
	 * (@see Tableizer::load_styles() ) then prints html
	 * @return void
	 */
	public function get_page() {

		$this->html = file_get_contents(TABLEIZER_DIR . "/public_html/Tableizer.php");
		$this->shortcodes['create tables nonce'] = wp_create_nonce("create tables");
		$this->shortcodes['errors'] = tableizer_get_errors();
		$this->shortcodes['messages'] = tableizer_get_messages();
		$this->shortcodes['table html'] = $this->table_html;
		$this->set_shortcodes();
		
		//check for runtime codes - if not then print html
		preg_match_all("/<\!--\[--\[--(.+)--\]--\]-->/", $this->html, $matches);
		if(!@count($matches[1])) print $this->html;
		
		//if runtime codes then split html by codes
		$parts = explode("<!--[--[--", $this->html);
		
		//print each part
		foreach($parts as $key => $part){
			
			//search for runtime code at start of this part, run it and remove code from part
			$code = trim(substr($part, 0, strpos($part, "--]--]-->")));
			if(method_exists($this, $code)) $this->$code();
			$part = str_replace("{$code}--]--]-->", "", $part);
			
			//print this part
			print $part;			
		}
	}
	
	/**
	 * Loads javascript files
	 * 
	 * @return void 
	 */
	private function load_scripts() {
		;
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
	 * Prints the wordpress textarea tinymce editor.
	 */
	private function print_wp_editor(){
		
		wp_editor($this->table_html, "data", array(
			"textarea_name" => "data",
			"wpautop" => false,
			"media_buttons" => false
		));
	}
	
	/**
	 * Sets values for the shortcodes in the view file.
	 * 
	 * Replaces the codes with values in @see FSNetworkRegister::$html . To add
	 * shortcodes to the view file use the syntax:
	 * <code> <!--[--identifying string--]--> </code>. In the construct of this
	 * class add the value to the array @see Tableizer::$shortcodes.
	 * eg: $this->shortcodes['identifying string'] = 
	 * $this->method_returns_html().
	 * 
	 * 
	 * 
	 * @return void
	 */
	private function set_shortcodes() {
		
		//replace shortcodes with values
		foreach ($this->shortcodes as $code => $val)
			$this->html = str_replace("<!--[--{$code}--]-->", $val, $this->html);
	}
}
?>
