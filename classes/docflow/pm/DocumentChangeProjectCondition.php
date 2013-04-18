<?php
// Документ "Изменение состояния проекта"
class DocumentChangeProjectCondition extends Document {
	
	function construct($params) {
		parent::construct($params);
		$this->clientClass = "DocumentChangeProjectCondition";
		$this->hierarchy = true;
		$this->parentClientClasses = "Document~Entity";
		$this->classListTitle = "Изменения состояния проектов";
		$this->classTitle = "Изменение состояния проекта";		
		$this->template = "renderForm";
        $this->fieldList = "docDate Дата~number Номер~projectReference.title AS title Проект~projectCondition.title AS projectCondition Состояние проекта~title Комментарий";
        $this->additionalFields = "name~isGroup~parent~registered~deleted";
        $this->condition = "@parent IS NOT EXISTS";
        $this->printFieldList = $this->fieldList;
        $this->allFieldList = $this->fieldList;
        $this->width = "700";
        $this->height = "600";     
        $this->hierarchy = "true";
        $this->overrided = "width,height";
        $this->conditionFields = "title~Комментарий~string,projectReference~Проект~entity,projectCondition~Состояние проекта~entity";        
        $this->sortOrder = "docDate ASC";
        $this->tabs_string = "main|Основное|".$this->skinPath."images/spacer.gif";
        $this->tabset_id = "WebItemTabset_".$this->module_id."_DocumentChangeProjectCondition".$this->name;
        $this->tabsetName = $this->tabset_id;
        $this->active_tab = "main";
        $this->adapterId = $this->adapter->getId();
        $this->icon = $this->skinPath."images/Tree/document.png";
	}
	
	function renderForm() {
		$blocks = getPrintBlocks(file_get_contents("templates/docflow/pm/DocumentChangeProjectCondition.html"));
		$common = getPrintBlocks(file_get_contents("templates/docflow/common/CommonFields.html"));
		$out = str_replace("{hiddenFields}",$common["hiddenFields"],$blocks["header"]);
		if ($this->name!="") {
			$out .= $common["tabs"];
			$out .= $common["profileEditTab"];
		}
		$out .= $blocks["footer"];		
		return $out;
	}
	
	function load() {
		parent::load();
		$this->old_projectReference = $this->projectReference;
		$this->old_projectCondition = $this->projectCondition;
	}
			
	function checkData() {
		if (!$this->isGroup) {
			if ($this->projectReference=="") {
				$this->reportError("Укажите проект","save");
				return 0;
			}				
			if ($this->projectCondition=="") {
				$this->reportError("Укажите состояние проекта","save");
				return 0;
			}				
			if ($this->manager=="") {
				$this->reportError("Укажите ответственного менеджера","save");
				return 0;
			}				
			global $Objects;
			if (!is_object($this->projectReference))
				$doc = $Objects->get($this->projectReference);
			else
				$doc = $this->projectReference;
			if (!$doc->loaded)
				$doc->load();
			if ($doc->dateStart!="")
				if ($doc->docDate>$this->docDate) {
					$this->reportError("Дата изменения состояния проекта не может быть меньше даты начала проекта","save");
					return 0;				
				}
		}
		return parent::checkData();			
	}
	
	function afterSave($out=true) {
		parent::afterSave($out);
		if ($this->projectReference!="") {
			$this->removeLinks($this->old_projectReference);
			$this->setLinks(array($this->projectReference));
		}
	}
	
	function getArgs() {		        
		if ($this->window_id=="")
			$this->window_id = @$_GET["window_id"];
		
		$this->entityImages = json_encode(array());
						
		return parent::getArgs();
	}
	
	function register() {
		parent::register();
		global $Objects;
		$record = $Objects->get("RegistryProjectConditions_".$this->module_id."_");
		$record->document = $this;
		$record->regDate = $this->docDate;
		$record->projectReference = $this->projectReference;
		$record->projectCondition = $this->projectCondition;	
		$record->save(true);
		$Objects->remove("RegistryProjectConditions_".$this->module_id."_");
	}	
}
?>