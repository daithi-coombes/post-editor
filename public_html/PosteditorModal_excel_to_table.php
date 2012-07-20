	<form method="post" id="raw-data">

		<input type="hidden" name="posteditor_action" value="build_html_table"/>

		<textarea id="data" name="data"><!--[--textarea content--]--></textarea>
		<div class="modal-actions">
			<input type="image" src="<!--[--images dir--]-->/excel_to_table_btn.png"/>
			<!--<button name="posteditormodal_action" value="excel_to_table">
				<img src="<!--[--images dir--]--/"
			</button>-->
		</div>

	</form>

	<textarea id="results" name="results"><!--[--modal result--]--></textarea>

	<input type="button" value="Insert in to Editor" class="button-primary" onclick="posteditor_modal_ett.insert_to_editor()"/>
	<input type="button" value="Reset Form" class="button-primary" onclick="posteditor_modal_ett.reset_form()"/>
	<input type="button" value="Cancel Edit" class="button-primary" onclick="window.parent.tb_remove()"/>