<?php
// Справочник информационных карточек
class ReferenceInfoCard extends Reference {
	
	function construct($params) {
		parent::construct($params);
		$this->clientClass = "ReferenceInfoCard";
		$this->hierarchy = true;
		$this->parentClientClasses = "Reference~Entity";
		$this->classListTitle = "Информационные карточки";
		$this->classTitle = "Информационная карточка";		
		$this->template = "renderForm";
        $this->fieldList = "title Описание";
        $this->additionalFields = "name~isGroup~parent~deleted";
        $this->condition = "@parent IS NOT EXISTS";
        $this->printFieldList = $this->fieldList;
        $this->allFieldList = $this->fieldList;
        $this->width = "700";
        $this->height = "600";     
        $this->overrided = "width,height";
        $this->conditionFields = "title~Наименование~string";        
        $this->sortOrder = "title ASC";
        $this->handler = "scripts/handlers/docflow/common/ReferenceNotes.js";
        $this->tabs_string = "main|Основное|".$this->skinPath."images/spacer.gif";
        $this->tabset_id = "WebItemTabset_".$this->module_id."_ReferenceInfoCard".$this->name;
        $this->tabsetName = $this->tabset_id;
        $this->active_tab = "main";
        $this->date = time()."000";
        $this->adapterId = $this->adapter->getId();
        $this->skinPath = $this->app->skinPath;
        $this->icon = $this->skinPath."images/docflow/note.png";
	}
	
	function renderForm() {
		$blocks = getPrintBlocks(file_get_contents("templates/docflow/common/ReferenceInfoCard.html"));
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
		global $Objects;
		if ($this->noPresent)
			return "";
		if (!$this->loaded)
			$this->load();
		if ($this->objectId!="") {
			$obj = $Objects->get($this->objectId);
			if ($obj->presentation!="")
				return $obj->presentation;
		}
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
		return parent::checkData();			
	}
	
	function getArgs() {		        
		if ($this->window_id=="")
			$this->window_id = @$_GET["window_id"];
		
		$this->entityImages = json_encode(array());
						
		return parent::getArgs();
	}
}
?>