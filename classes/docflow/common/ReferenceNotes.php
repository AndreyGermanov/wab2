<?php
// Справочник заметок
class ReferenceNotes extends Reference {
	
	function construct($params) {
		parent::construct($params);
		$this->clientClass = "ReferenceNotes";
		$this->hierarchy = true;
		$this->parentClientClasses = "Reference~Entity";
		$this->classListTitle = "Заметки";
		$this->classTitle = "Заметка";		
		$this->template = "renderForm";
        $this->fieldList = "date Дата~title Описание~user Создатель";
        $this->additionalFields = "name~isGroup~parent~deleted";
        $this->condition = "@parent IS NOT EXISTS";
        $this->printFieldList = $this->fieldList;
        $this->allFieldList = $this->fieldList;
        $this->width = "700";
        $this->height = "600";     
        $this->overrided = "width,height";
        $this->conditionFields = "date~Дата~date,title~Описание~string,user~Создатель~string";        
        $this->sortOrder = "date DESC";
        $this->handler = "scripts/handlers/docflow/common/ReferenceNotes.js";
        $this->tabs_string = "main|Основное|".$this->skinPath."images/spacer.gif";
        $this->tabset_id = "WebItemTabset_".$this->module_id."_ReferenceNotes".$this->name;
        $this->tabsetName = $this->tabset_id;
        $this->active_tab = "main";
        $this->date = time()."000";
        $this->adapterId = $this->adapter->getId();
        $this->skinPath = $this->app->skinPath;
        $this->icon = $this->skinPath."images/docflow/note.png";
        $this->createObjectList = array("DocumentWorkReport" => "Отчет о работе", "ReferenceProjects" => "Проект", "DocumentTask" => "Задача");
	}
	
	function renderForm() {
		$blocks = getPrintBlocks(file_get_contents("templates/docflow/common/ReferenceNotes.html"));
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
			if ($this->title != "") {				
				if ($this->title!="")
					$query = "SELECT entities FROM fields WHERE @title='".$this->title."' AND @name!=".$this->name." AND @classname='".get_class($this)."'";
				else
					$query = "SELECT entities FROM fields WHERE @title='".$this->title."' AND @classname='".get_class($this)."'";				
				$result = PDODataAdapter::makeQuery($query, $this->adapter,$this->module_id);
				if (count($result)>0) {
					$this->reportError("Заметка с таким наименованием уже существует","save");
					return 0;
				}
			}
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