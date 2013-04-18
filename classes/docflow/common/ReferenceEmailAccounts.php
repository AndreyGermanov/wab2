<?php
// Справочник учетных записей Email
class ReferenceEmailAccounts extends Reference {
	
	function construct($params) {
		parent::construct($params);
		$this->clientClass = "ReferenceEmailAccounts";
		$this->hierarchy = true;
		$this->parentClientClasses = "Reference~Entity";
		$this->classListTitle = "Учетные записи Email";
		$this->classTitle = "Учетная запись Email";		
		$this->template = "renderForm";
        $this->fieldList = "email Email~title Описание";
        $this->additionalFields = "name~isGroup~parent~deleted";
        $this->condition = "@parent IS NOT EXISTS";
        $this->printFieldList = $this->fieldList;
        $this->allFieldList = $this->fieldList;
        $this->width = "700";
        $this->height = "600";     
        $this->overrided = "width,height";
        $this->conditionFields = "email~EMail~string|title~Наименование~string";        
        $this->sortOrder = "email ASC";
        $this->tabs_string = "main|Основное|".$this->skinPath."images/spacer.gif";
        $this->tabset_id = "WebItemTabset_".$this->module_id."_ReferenceDepartments".$this->name;
        $this->tabsetName = $this->tabset_id;
        $this->active_tab = "main";
        $this->adapterId = $this->adapter->getId();
        $this->skinPath = $this->app->skinPath;
        $this->icon = $this->skinPath."images/Window/mail.gif";
	}
	
	function renderForm() {
		$blocks = getPrintBlocks(file_get_contents("templates/docflow/common/ReferenceEmailAccounts.html"));
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
			return $this->classTitle.":".$this->email;
		else
			return $this->email;
	}	
		
	function checkData() {
		
		if ($this->email=="") {
			$this->reportError("Укажите email","save");
			return 0;
		}
		
		if ($this->title=="") {
			$this->reportError("Укажите описание","save");
			return 0;
		}
		
		if ($this->host=="") {
			$this->reportError("Укажите хост","save");
			return 0;
		}

		if ($this->port=="") {
			$this->reportError("Укажите порт","save");
			return 0;
		}

		if ($this->username=="") {
			$this->reportError("Укажите имя пользователя","save");
			return 0;
		}
		
		if (!$this->isGroup) {
			if ($this->email != "") {				
				if ($this->email!="")
					$query = "SELECT entities FROM fields WHERE @email='".$this->title."' AND @name!=".$this->name." AND @classname='".get_class($this)."'";
				else
					$query = "SELECT entities FROM fields WHERE @email='".$this->title."' AND @classname='".get_class($this)."'";				
				$result = PDODataAdapter::makeQuery($query, $this->adapter,$this->module_id);
				if (count($result)>0) {
					$this->reportError("Заметка с таким email уже существует","save");
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