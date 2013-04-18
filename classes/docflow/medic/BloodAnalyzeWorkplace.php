<?php

class BloodAnalyzeWorkplace extends WABEntity {
	
	function construct($params) {
		parent::construct($params);
		
		$this->template = "renderForm";
		
		if (count($this->role)==0)
			$this->setRole();
		$this->tabs_string= "docs|Документы|".$this->skinPath."images/Tree/document.png";
		if (@$this->role["expert"]=="true")		
			$this->tabs_string.= ";report|Отчет|".$this->skinPath."images/Tree/report.png";
		
		$this->tabset_id = "WebItemTabset_".$this->module_id."_BloodAnalyzeWorkplace";
		$this->active_tab = "docs";
		$this->width = "680";
		$this->height = "400";
		$this->overrided = "width,height";		
	}
	
	function renderForm() {
		$blocks = getPrintBlocks(file_get_contents("templates/docflow/medic/BloodAnalyzeWorkplace.html"));
		$out = $blocks["header"];
		if (count($this->role)==0)
			$this->setRole();
		if (@$this->role["expert"]=="true")
			$out .= $blocks["tabset"];
		$out .= $blocks["documents"];
		if (@$this->role["expert"]=="true")
			$out .= $blocks["report"];
		$out .= $blocks["footer"];
		return $out;				
	}
}
?>
