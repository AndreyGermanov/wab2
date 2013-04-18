<?php
class ReferenceFilesTable extends DocFlowDocumentTable {
	
	function construct($params) {
		parent::construct($params);
		$this->template = "templates/docflow/common/ReferenceFilesTable.html";
	}
}