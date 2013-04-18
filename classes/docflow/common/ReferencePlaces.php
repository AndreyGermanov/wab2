<?php
// Справочник мест хранения
class ReferencePlaces extends Reference {
	
	function construct($params) {
		parent::construct($params);
		$this->clientClass = "ReferencePlaces";
		$this->hierarchy = true;
		$this->parentClientClasses = "Reference~Entity";
		$this->classListTitle = "Места";
		$this->classTitle = "Места";		
		$this->template = "renderForm";
        $this->fieldList = "title Наименование";
        $this->additionalFields = "name~isGroup~parent~deleted";
        $this->condition = "@parent IS NOT EXISTS";
        $this->printFieldList = $this->fieldList;
        $this->allFieldList = $this->fieldList;
        $this->width = "700";
        $this->height = "600";     
        $this->overrided = "width,height";
        $this->conditionFields = "title~Наименование~string";        
        $this->sortOrder = "title ASC";
        $this->tabs_string = "main|Основное|".$this->skinPath."images/spacer.gif";
        $this->tabset_id = "WebItemTabset_".$this->module_id."_ReferencePlaces".$this->name;
        $this->tabsetName = $this->tabset_id;
        $this->active_tab = "main";
        $this->adapterId = $this->adapter->getId();
        $this->skinPath = $this->app->skinPath;
        $this->icon = $this->skinPath."images/Tree/objectgroup.png";
	}
	
	function renderForm() {
		$blocks = getPrintBlocks(file_get_contents("templates/docflow/common/ReferencePlaces.html"));
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
		
		if ($this->department=="") {
			$this->reportError("Укажите подразделение","save");
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
					$this->reportError("Элемент с таким наименованием уже существует","save");
					return 0;
				}
			}
		} 		
		return parent::checkData();			
	}
	
	function load() {
		parent::load();
		$this->old_department = $this->department;
	}	

	function createByOwnerObject() {
		$doc = $this->ownerObject;
		$class = get_class($doc);
		if ($class=="ReferenceDepartments") {
			$this->department = $doc;
		}
	}
	
	function afterSave($out=true) {
		parent::afterSave($out);
	
		if (is_object($this->department))
			$this->department = $this->department->getId();
		
		if (is_object($this->old_department))
			$this->old_department = $this->old_department->getId();
		
		if ($this->department!="") {
			if ($this->old_department!="") {
				$this->removeLinks(array($this->old_department));
			}
			$this->setLinks(array($this->department));
		}
	}
	
	function getArgs() {		        
		if ($this->window_id=="")
			$this->window_id = @$_GET["window_id"];
		
		$this->entityImages = json_encode(array());						
		return parent::getArgs();
	}
}
?>