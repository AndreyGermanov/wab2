<?php
// Справочник адресов Email
class ReferenceEmailAddresses extends Reference {
	
	function construct($params) {
		parent::construct($params);
		$this->clientClass = "ReferenceEmailAddresses";
		$this->hierarchy = true;
		$this->parentClientClasses = "Reference~Entity";
		$this->classListTitle = "Адреса E-Mail";
		$this->classTitle = "Адрес Email";		
		$this->template = "renderForm";
        $this->fieldList = "title Наименование~description Описание";
        $this->additionalFields = "name~isGroup~parent~deleted";
        $this->condition = "@parent IS NOT EXISTS";
        $this->printFieldList = $this->fieldList;
        $this->allFieldList = $this->fieldList;
        $this->width = "600";
        $this->height = "350";        
        $this->overrided = "width,height";
        $this->conditionFields = "title~Наименование~string,description~Описание~text";        
        $this->sortOrder = "title ASC";
        $this->handler = "scripts/handlers/docflow/common/ReferenceEmailAddresses.js";
        $this->tabs_string = "main|Основное|".$this->skinPath."images/spacer.gif";
        $this->tabset_id = "WebItemTabset_".$this->module_id."_ReferenceEmailAddresses".$this->name;
        $this->tabsetName = $this->tabset_id;
        $this->active_tab = "main";
        $this->adapterId = $this->adapter->getId();
        $this->icon = $this->skinPath."images/docflow/emailaddr.png";
    }
	
	function renderForm() {
		$blocks = getPrintBlocks(file_get_contents("templates/docflow/common/ReferenceEmailAddresses.html"));
		$common = getPrintBlocks(file_get_contents("templates/docflow/common/CommonFields.html"));
		$out = str_replace("{hiddenFields}",$common["hiddenFields"],$blocks["header"]);
		if ($this->name!="") {
			$out .= $common["tabs"];
			$out .= $common["profileEditTab"];
		}	
		$out.= $blocks["footer"];	
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
			$this->reportError("Укажите адрес E-Mail","save");
			return 0;
		}		
		if (!$this->isGroup) {
				
			if ($this->title != "") {
				if ($this->name!="")
					$query = "SELECT entities FROM fields WHERE @title='".$this->title."' AND @name!=".$this->name." AND @classname='".get_class($this)."'";
				else
					$query = "SELECT entities FROM fields WHERE @title='".$this->title."' AND @classname='".get_class($this)."'";				
				$result = PDODataAdapter::makeQuery($query, $this->adapter,$this->module_id);
				if (count($result)>0) {
					$this->reportError("Такой адрес E-mail уже есть в базе","save");
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
				
		$this->tabsetCode = '$object->module_id="'.$this->module_id.'";
							 $object->item="'.$this->getId().'";
							 $object->window_id="'.$this->window_id.'";
							 $object->tabs_string="'.$this->tabs_string.'";
							 $object->active_tab="'.$this->active_tab.'";';	
				
		$this->tabsetCode = cleanText($this->tabsetCode);		
		
		return parent::getArgs();
	}
}
?>