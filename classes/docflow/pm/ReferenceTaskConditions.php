<?php
// Справочник состояний задачи
class ReferenceTaskConditions extends Reference {
	
	function construct($params) {
		parent::construct($params);
		$this->clientClass = "ReferenceTaskConditions";
		$this->hierarchy = true;
		$this->parentClientClasses = "Reference~Entity";
		$this->classListTitle = "Состояния задачи";
		$this->classTitle = "Состояние задачи";		
		$this->template = "renderForm";
        $this->fieldList = "orderNumber № п.п.~title Наименование";
        $this->additionalFields = "name~isGroup~parent~deleted";
        $this->condition = "@parent IS NOT EXISTS";
        $this->printFieldList = $this->fieldList;
        $this->allFieldList = $this->fieldList;
        $this->width = "600";
        $this->height = "400";
        $this->hierarchy = "false";
        //$this->icon = $this->skinPath."images/docflow/bank.png";   
        $this->conditionFields = "orderNumber~№ п.п.~string,title~Наименование~string";        
        $this->sortOrder = "orderNumber ASC";
        $this->tabs_string = "main|Основное|".$this->skinPath."images/spacer.gif";
        $this->tabset_id = "WebItemTabset_".$this->module_id."_ReferenceTaskConditions".$this->name;
        $this->tabsetName = $this->tabset_id;
        $this->active_tab = "main";
	}
	
	function renderForm() {
		$blocks = getPrintBlocks(file_get_contents("templates/docflow/crm/ReferenceOrderConditions.html"));
		$common = getPrintBlocks(file_get_contents("templates/docflow/common/CommonFields.html"));
		$out = str_replace("{hiddenFields}",$common["hiddenFields"],$blocks["header"]);
		if ($this->name!="") {
			$out .= $common["tabs"];
			$out .= $common["profileEditTab"];
		}
		$out .= $blocks["footer"];		
		return $out;
	}
	
	function getPresentation() {
		if ($this->noPresent)
			return "";
		if (!$this->loaded)
			$this->load();
		if ($this->classTitle != "")
			return $this->classTitle.":".$this->title;
		else
			return $this->title;
	}	
	
	function checkData() {
		if ($this->title=="") {
			$this->reportError("Укажите наименование","save");
			return 0;
		}
		
		if (!$this->isGroup) {			
			if ($this->orderNumber="") {
				$this->reportError("Укажите порядковый номер","save");
				return 0;
			}
				
			if ($this->name!="")
				$query = "SELECT entities FROM fields WHERE @orderNumber='".$this->sortOrder."' AND @name!=".$this->name." AND @classname='".get_class($this)."'";
			else
				$query = "SELECT entities FROM fields WHERE @orderNumber='".$this->sortOrder."' AND @classname='".get_class($this)."'";
			$result = PDODataAdapter::makeQuery($query, $this->adapter,$this->module_id);
			if (count($result)>0) {
				$this->reportError("Состояние с таким порядковым номером уже есть в базе!","save");
				return 0;
			}
		} 
		
		return parent::checkData();			
	}
	
	function load() {
		parent::load();		
		if ($this->orderNumber=="") {
			$this->orderNumber = $this->adapter->getMaxValue("fields","orderNumber","@classname='".get_class($this)."'");
		}		
	}
}
?>