<?php

class ModuleModelConfig extends ModelConfig {
	
	function construct($params) {
		parent::construct($params);
		$this->icon = $this->skinPath."images/Tree/module.png";
	}
	
	function getPresentation() {
		global $modules;
		if ($this->metadata_subfield=="")
			return "Параметры модуля ".$modules[$this->metadata_field]["title"];
		else
			return "Параметры модуля ".$modules[$this->metadata_subfield]["title"];
	
	}
	
}
?>