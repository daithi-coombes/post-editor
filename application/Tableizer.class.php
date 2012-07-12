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
	/** @var string The table html in the textarea - defaults to '' */
	private $table_html;

	/**
	 * constructor
	 */
	public function __construct() {
		
		global $tableizer_action;
		
		//default params
		$this->shortcodes = array();
		$this->table_html = '';
		
		//calls for just after wp admin core loads
		add_action('admin_init', array(&$this, 'admin_init'));
		
		//check for actions
		if(!empty($tableizer_action))
			if(method_exists($this, $tableizer_action))
				add_action('admin_init', array(&$this, $tableizer_action));
					
	}

	public function admin_init(){
		
		$this->load_scripts();
		$this->load_styles();
	}
	
	/**
	 * Build the admin menu.
	 * 
	 * The root menu and menu's for modules are created here.
	 */
	public function admin_menu(){
				
		$this->menu = "tableizer";
		add_menu_page( "Tableizer", "Tableizer", "administrator", "tableizer", array(&$this, 'get_page'));
	}	
	
	public function create_tables(){
		
		//security check
		if(!wp_verify_nonce($_POST['_wpnonce'],'create tables')){
			tableizer_error("Invalid nonce");
			return false;
		}
		
		$html = "<table>\n";
		$data = array();
		$_POST['data'] = trim($_POST['data']);
		
		//split into rows
		$rows = explode("\n", $_POST['data']);
		
		//build data
		foreach($rows as $row){
			$data[] = explode("\t", $row);
		}
		
		//build html table
		foreach($data as $row){
			$html .= "\t<tr>\n";
			foreach($row as $cell)
				$html .= "\t\t<td>".trim($cell)."</td>\n";
			$html .= "\t</tr>\n";
		}
		$html .= "</table>\n";
		
		$this->table_html = $html;
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
		$this->shortcodes['table html'] = $this->table_html;
		$this->set_shortcodes();

		print $this->html;
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
		
		wp_register_style('tableizer', TABLEIZER_URL . "/public_html/css/Tableizer.css");
		wp_enqueue_style('tableizer');
	}

	/**
	 * Sets values for the shortcodes in the view file.
	 * 
	 * Replaces the codes with values in @see FSNetworkRegister::$html . To add
	 * shortcodes to the view file use the syntax:
	 * <code> <!--[--identifying string--]--> </code>. In the construct of this
	 * class add the value to the array @see FSNetworkRegister::$shortcodes.
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
