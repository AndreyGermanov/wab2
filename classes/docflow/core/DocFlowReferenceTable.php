<?php
/**
 * Класс реализует форму списка справочника
 *
 * @author andrey
 */
class DocFlowReferenceTable extends DocFlowDocumentTable {    
	function construct($params) {
		parent::construct($params);
		$this->clientClass = "DocFlowReferenceTable";
		$this->parentClientClasses = "DocFlowDocumentTable~EntityDataTable~DataTable~Entity";		
	}
}
?>