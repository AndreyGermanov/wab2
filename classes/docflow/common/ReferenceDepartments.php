<?php
// Справочник подразделений
class ReferenceDepartments extends Reference {
	
	function construct($params) {
		parent::construct($params);
		$this->clientClass = "ReferenceDepartments";
		$this->hierarchy = true;
		$this->parentClientClasses = "Reference~Entity";
		$this->classListTitle = "Подразделения";
		$this->classTitle = "Подразделение";		
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
        if ($this->name!="") {
        	$this->tabs_string .= ";places|Места|".$this->skinPath."images/spacer.gif";        	 
        }
        $this->tabset_id = "WebItemTabset_".$this->module_id."_ReferenceDepartments".$this->name;
        $this->tabsetName = $this->tabset_id;
        $this->active_tab = "main";
        $this->adapterId = $this->adapter->getId();
        $this->skinPath = $this->app->skinPath;
        $this->icon = $this->skinPath."images/docflow/department.png";
        $this->handler = "scripts/handlers/docflow/common/ReferenceDepartments.js";
        $this->createObjectList = array("ReferencePlaces" => "Место");
	}
	
	function renderForm() {
		$blocks = getPrintBlocks(file_get_contents("templates/docflow/common/ReferenceDepartments.html"));
		$common = getPrintBlocks(file_get_contents("templates/docflow/common/CommonFields.html"));
		$out = str_replace("{hiddenFields}",$common["hiddenFields"],$blocks["header"]);
		if ($this->name!="") {
			$out .= $blocks["places"];
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
					$this->reportError("Элемент с таким наименованием уже существует","save");
					return 0;
				}
			}
		} 		
		return parent::checkData();			
	}
	
	function getArgs() {

		$this->bsfieldList = "title Место";
		$this->bsitemsPerPage = 10;
		$this->bssortOrder = "title ASC";
		$this->bssortField = $this->bssortOrder;
		$this->bsConditionFields = "title~Наименование~string";
		$this->bscondition = "@parent IS NOT EXISTS";
		
		$this->placesTableId = "DocFlowReferenceTable_".$this->module_id."_".$this->name."places";
		
		$this->placesTableFieldAccess = json_encode(array("department" => "read"));
		$this->placesTableFieldDefaults = json_encode(array("department" => $this->getId()));
		
		$this->placesCode = '$object->additionalCondition="@department.@name='.$this->name.'";
		$object->window_id="'.$this->window_id.'";
		$object->parent_object_id="'.$this->getId().'";
		$object->className="ReferencePlaces";
		$object->defaultClassName="ReferencePlaces";
		$object->fieldList="'.$this->bsfieldList.'";
		$object->itemsPerPage='.$this->bsitemsPerPage.';
		$object->currentPage=1;
		$object->hierarchy=0;
		$object->additionalFields="name~deleted";
		$object->condition="'.$this->bscondition.'";
		$object->conditionFields="'.$this->bsConditionFields.'";
		$object->adapterId="'.$this->adapter->getId().'";
		$object->autoload="false";
		$object->sortOrder="'.$this->bssortOrder.'";';
		
		$this->placesCode = cleanText($this->placesCode);
				
		if ($this->window_id=="")
			$this->window_id = @$_GET["window_id"];
		
		$this->entityImages = json_encode(array());
		
		return parent::getArgs();
	}
}
?>