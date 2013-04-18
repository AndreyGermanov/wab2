<?php
/**
 * Класс управляет панелью страниц DataTable
 *
 * @author andrey
 */
class DataTablePagePanel extends PagePanel {	
	function construct($params) {
		parent::construct($params);
		$this->clientClass = "DataTablePagePanel";
		$this->parentClientClasses = "PagePanel~Entity";		
	}    
}
?>