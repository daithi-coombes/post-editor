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

	/**
	 * constructor
	 */
	public function __construct() {
		;
	}
	
	/**
	 * Builds a html table from data copied from excel spreadsheet. Takes data
	 * from $_POST.	
	 *
	 * @return string 
	 */
	public function build_table(){
		
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
					$data = trim($row[$x]);
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
		
		return $html;
	}
	
	public function get_page(){
		return "this is the page";
	}
}

?>
