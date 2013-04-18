<?php
// Справочник банков
class ReferenceBanks extends Reference {
	
	function construct($params) {
		parent::construct($params);
		$this->clientClass = "ReferenceBanks";
		$this->hierarchy = true;
		$this->parentClientClasses = "Reference~Entity";
		$this->classListTitle = "Банки";
		$this->classTitle = "Банк";		
		$this->template = "renderForm";
        $this->fieldList = "title Наименование~BIK БИК~KS Корр.счет";
        $this->additionalFields = "name~isGroup~parent~deleted";
        $this->condition = "@parent IS NOT EXISTS";
        $this->printFieldList = $this->fieldList;
        $this->allFieldList = $this->fieldList;
        $this->width = "600";
        $this->height = "400";
        $this->icon = $this->skinPath."images/docflow/bank.png";   
        $this->conditionFields = "title~Наименование~string,BIK~БИК~integer,KS~Корр.счет~string";        
        $this->sortOrder = "BIK ASC";
        $this->tabs_string = "main|Основное|".$this->skinPath."images/spacer.gif";
        $this->tabset_id = "WebItemTabset_".$this->module_id."_ReferenceFiles".$this->name;
        $this->tabsetName = $this->tabset_id;
        $this->active_tab = "main";
	}
	
	function renderForm() {
		$blocks = getPrintBlocks(file_get_contents("templates/docflow/common/ReferenceBanks.html"));
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
		$this->loaded = false;
		$this->load();
		if ($this->classTitle != "")
			return $this->classTitle.":".$this->title;
		else
			return $this->title;
	}	
	
	function checkData() {
		if ($this->title=="") {
			$this->reportError("Укажите название банка","save");
			return 0;
		}
		
		if (!$this->isGroup) {
			if (!preg_match('/^[1-9][0-9]{19}$/',$this->KS)) {
				$this->reportError("Корр. счет указан не верно","save");
				return 0;
			}
			
			if (!preg_match('/^0[1-9][0-9]{7}$/',$this->BIK)) {
				$this->reportError("БИК указан не верно","save");
				return 0;				
			}
			
			if ($this->name!="")
				$query = "SELECT entities FROM fields WHERE @BIK='".$this->BIK."' AND @name!=".$this->name." AND @classname='".get_class($this)."'";
			else
				$query = "SELECT entities FROM fields WHERE @BIK='".$this->BIK."' AND @classname='".get_class($this)."'";
			$result = PDODataAdapter::makeQuery($query, $this->adapter,$this->module_id);
			if (count($result)>0) {
				$this->reportError("Банк с таким БИК уже есть в базе!","save");
				return 0;
			}
		} 
		
		return parent::checkData();			
	}
}
?>