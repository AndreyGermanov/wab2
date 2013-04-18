<?php
// Справочник контактных лиц
class ReferenceContacts extends Reference {
	
	function construct($params) {		
		parent::construct($params);
		$this->clientClass = "ReferenceContacts";
		$this->hierarchy = true;
		$this->parentClientClasses = "Reference~Entity";
		$this->classListTitle = "Контактные лица";
		$this->classTitle = "Контактное лицо";		
		$this->template = "renderForm";
        $this->fieldList = "title ФИО~description Описание~phones Телефоны~defaultEmail.title AS email Email";
        $this->additionalFields = "name~isGroup~parent~deleted";
        $this->condition = "@parent IS NOT EXISTS";
        $this->printFieldList = $this->fieldList;
        $this->allFieldList = $this->fieldList;
        $this->width = "650";
        $this->height = "450";        
        $this->overrided = "width,height";
        $this->conditionFields = "title~ФИО~string,description~Описание~string,postalAddress~Почтовый адрес~text,phones~Телефоны~string,defaultEmail.title~E-mail~string,birthDate~Дата рождения~date,appointment~Должность~string";        
        $this->sortOrder = "title ASC";
        $this->handler = "scripts/handlers/docflow/common/ReferenceContacts.js";
        $this->tabset_id = "WebItemTabset_".$this->module_id."_".get_class($this).$this->name;
        $this->tabsetName = $this->tabset_id;
		$this->tabs_string = "main|Основное|".$this->skinPath."images/spacer.gif;";
        $this->tabs_string.= "contacts|Адреса и телефоны|".$this->skinPath."images/spacer.gif";        
		if ($this->name!="") {
			$this->tabs_string.= ";emails|Адреса Email|".$this->skinPath."images/spacer.gif";
		}				
        $this->active_tab = "main";
        $this->urDisplay = "";
        $this->fizDisplay = "none";
        $this->titleTitle = "Наименование";
        $this->type = "1";
        $this->itemsPerPage = "10";
        $this->adapterId = $this->adapter->getId();
		$this->icon = $this->skinPath."images/docflow/contragent.png";		
		global $Objects;
        $this->app = $Objects->get("Application");
        $this->skinPath = $this->app->skinPath;
        $this->createObjectList = array("DocumentTask" => "Задача");
	}
	
	function renderForm() {
		$blocks = getPrintBlocks(file_get_contents("templates/docflow/common/ReferenceContacts.html"));
		$common = getPrintBlocks(file_get_contents("templates/docflow/common/CommonFields.html"));
		$out = str_replace("{hiddenFields}",$common["hiddenFields"],$blocks["header"]);
		if ($this->name!="") {
			$out .= $blocks["notNew"];
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
			$this->reportError("Укажите наименование контактного лица","save");
			return 0;
		}		
		return parent::checkData();			
	}
	
	function getArgs() {		   

		if ($this->window_id=="")
			$this->window_id = @$_GET["window_id"];
		
		$this->entityImages = json_encode(array());
		
		$this->emailfieldList = "title Адрес Email~description Описание";
		$this->emailitemsPerPage = 10;
		$this->emailsortOrder = "title ASC";
		$this->emailsortField = $this->emailsortOrder;
		$this->emailconditon = "@parent IS NOT EXISTS";
		
		$this->emailEntityImages = json_encode(array());
		if (is_object($this->defaultEmail)) {			
			$this->emailEntityImages = json_encode(array($this->defaultEmail->getId() => $this->skinPath."images/Buttons/RegisteredDocumentEntityImage.png"));
		}			
		$this->emailsCode = '$object->topLinkObject="'.$this->getId().'";
									$object->window_id="'.$this->window_id.'";
									$object->parent_object_id="'.$this->getId().'";
									$object->className="ReferenceEmailAddresses";
									$object->defaultClassName="ReferenceEmailAddresses";
									$object->fieldList="'.$this->emailfieldList.'";
									$object->itemsPerPage='.$this->emailitemsPerPage.';
									$object->currentPage=1;
									$object->hierarchy=0;
									$object->additionalFields="name~deleted";
									$object->adapterId="'.$this->adapter->getId().'";
									$object->autoload="false";
									$object->sortOrder="'.$this->emailSortOrder.'";';
		
		$this->emailsCode = cleanText($this->emailsCode);
		$this->emailsTableId = "DocFlowReferenceTable_".$this->module_id."_".$this->name."emails";
		
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