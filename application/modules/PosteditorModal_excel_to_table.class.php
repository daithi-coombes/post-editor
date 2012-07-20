<?php
/**
 * @package post-editor
 */
/**
 * Description of PosteditorModal_excel_to_table
 *
 * @author daithi
 * @package post-editor
 */
class PosteditorModal_excel_to_table {

	/** @var string The current action (method to be called). 
	 * Defaults to false */
	private $action;
	/** @var string Holds the html from the view file for parsing */
	private $html;
	/** @var string The formated html table result */
	private $modal_result;
	/** @var array An array of shortcode=>value pairs for the view file */
	private $shortcodes;
	/** @var string The pasted excel content */
	private $textarea_content;
	
	/**
	 * constructor
	 */
	public function __construct() {
		
		//set default params
		(@$_REQUEST['posteditor_action'])	//the current action
			? $action = $_REQUEST['posteditor_action']
			: $action = false;
		$this->shortcodes = array();
		$this->modal_result = "";
		(@$_REQUEST['data'])	//posted data
			? $this->textarea_content = $_REQUEST['data']
			: $this->textarea_content = "";
		
		//wp hooks
		add_action('wp_head', array(&$this, 'admin_head'));
		
		//look for actions
		if($action)
			if(method_exists($this, $action))
				$this->$action();
	}
	
	/**
	 * Adds global javascript vars to the admin head.
	 * 
	 * @deprecated
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
	 * Builds a html table from data copied from excel spreadsheet. Takes data
	 * from $_POST.	
	 *
	 * Stores result in $this->modal_result
	 */
	public function build_html_table(){
		
		$html = "<table>\n";
		$data = array();
		@$_POST['data'] = trim($_POST['data']);
		$cols = 0;

		//split into rows
		$rows = explode("\n", $_POST['data']);

		//build data
		foreach ($rows as $row) {
			$cells = explode("\t", $row);
			$data[] = $cells;
			
			//work out num of cols
			if(count($cells)>$cols) $cols = count($cells);
		}
		
		//build html table
		foreach ($data as $row) {
			
			//vars
			$cells = array();
			$html .= "\t<tr>\n";
			$use_colspan = false;
			
			//if only first cell filled add colspan to create just one &lt;td>
			if(!empty($row[0])){
				for($x=1; $x<$cols; $x++){
					$data = trim(@$row[$x]);
					if(empty($data)) $use_colspan = true;
					else
						break;
				}
			}
			if($use_colspan) $cells[] = "\t\t<td colspan=\"{$cols}\">{$row[0]}</td>\n";
			
			//else build cells normally
			else
				foreach ($row as $key=> $cell){
					if(empty($cell)) $cell = "&nbsp;";
					$cells[] = "\t\t<td>" . trim($cell) . " {$use_colspan}</td>\n";
				}
			
			//add to html
			$html .= implode("", $cells) . "\t</tr>\n";
		}
		$html .= "</table>\n";
		
		$this->modal_result = $html;
	}
	
	/**
	 * Returns the html for the view file.
	 *
	 * @return string 
	 */
	public function get_page(){
		
		$this->html = file_get_contents( POSTEDITOR_DIR . "/public_html/PosteditorModal_excel_to_table.php");
		$this->shortcodes['images dir'] = POSTEDITOR_URL . "/public_html/images";
		$this->shortcodes['modal result'] = $this->modal_result;
		$this->shortcodes['textarea content'] = $this->textarea_content;
		$this->set_shortcodes();
		
		return $this->html;
	}
	
	/**
	 * Loads javascript files
	 * 
	 * @return void 
	 */
	public function load_scripts() {
		wp_register_script('posteditormodal_excel_to_table-tinymce', POSTEDITOR_URL . "/application/includes/tinymce/jscripts/tiny_mce/jquery.tinymce.js");
		wp_register_script('posteditormodal_excel_to_table', POSTEDITOR_URL . "/public_html/js/PosteditorModal_excel_to_table.js", array(
			'jquery',
			'posteditormodal_excel_to_table-tinymce'
		));
		
		wp_enqueue_script('posteditormodal_excel_to_table');
	}

	/**
	 * Loads css files
	 * 
	 * @return void 
	 */
	public function load_styles() {
		wp_register_style("posteditormodal_excel_to_table", POSTEDITOR_URL . "/public_html/css/PosteditorModal_excel_to_table.css");
		wp_enqueue_style("posteditormodal_excel_to_table");
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
