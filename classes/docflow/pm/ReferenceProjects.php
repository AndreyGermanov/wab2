<?php
// Справочник "Проекты"
class ReferenceProjects extends Reference {
	
	function construct($params) {
		parent::construct($params);
		$this->clientClass = "ReferenceProjects";
		$this->hierarchy = true;
		$this->parentClientClasses = "Reference~Entity";
		$this->classListTitle = "Проекты";
		$this->classTitle = "Проект";		
		$this->template = "renderForm";
        $this->fieldList = "title Наименование~firm.title AS firm Организация~department.title AS department Подразделение~projectCondition Состояние проекта~manager.fullName AS manager Менеджер";
        $this->additionalFields = "name~isGroup~parent~deleted";
        $this->condition = "@parent IS NOT EXISTS";
        $this->printFieldList = "title Наименование~firm.title AS firm Организация~department.title AS department Подразделение~manager.fullName AS manager Менеджер";
        $this->allFieldList = $this->fieldList;
        $this->width = "700";
        $this->height = "600";     
        $this->hierarchy = "true";
        $this->overrided = "width,height";
        $this->conditionFields = "title~Описание проекта~string,firm~Организация~entity,department~Подразделение~entity,manager~Менеджер~entity,dateStart~Дата начала~date,dateEnd~Дата окончания~date,isArchive~Архивный~boolean,description~Описание~text";        
        $this->sortOrder = "title ASC";
        $this->tabs_string = "main|Основное|".$this->skinPath."images/spacer.gif";
		if ($this->name!="") {
			$this->tabs_string .= ";projectChanges|Состояние|".$this->skinPath."images/spacer.gif";
		}
        $this->tabset_id = "WebItemTabset_".$this->module_id."_DocumentOrder".$this->name;
        $this->handler = "scripts/handlers/docflow/pm/ReferenceProjects.js";
        $this->tabsetName = $this->tabset_id;
        $this->active_tab = "main";
        $this->adapterId = $this->adapter->getId();
        $this->icon = $this->skinPath."images/docflow/project.png";
        $this->createObjectList = array("DocumentContragentRequest" => "Обращение контрагента", "DocumentContract" => "Договор", "DocumentInvoice" => "Счет на оплату", "DocumentWorkReport" => "Отчет о работе", "ReferenceContacts" => "Контактное лицо", "DocumentTask" => "Задача");
        $this->dateStart = time()."000";
        $this->dateEnd = time()."000";
	}
	
	function renderForm() {
		$blocks = getPrintBlocks(file_get_contents("templates/docflow/pm/ReferenceProjects.html"));
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
	
	function load() {
		parent::load();
		$this->old_firm = $this->firm;
		$this->old_department = $this->deparment;
		$this->old_manager = $this->manager;
		$this->old_workObject = $this->workObject;
	}
			
	function checkData() {
		if ($this->title=="") {
			$this->reportError("Укажите наименование","save");
			return 0;
		}

		if (!$this->isGroup) {
			if ($this->dateStart!="" and $this->dateEnd!="") {
				if ($this->dateStart>$this->dateEnd) {
					$this->reportError("Дата начала больше даты окончания","save");
					return 0;
				}
			}
		}		
		return parent::checkData();			
	}
	
	function afterSave($out=true) {
		parent::afterSave($out);
		
		if (is_object($this->firm))
			$this->firm = $this->firm->getId();
		
		if (is_object($this->department))
			$this->department = $this->department->getId();

		if (is_object($this->manager))
			$this->manager = $this->manager->getId();

		if (is_object($this->workObject))
			$this->workObject = $this->workObject->getId();
		
		if (is_object($this->old_firm))
			$this->old_firm = $this->old_firm->getId();
	
		if (is_object($this->old_department))
			$this->old_department = $this->old_department->getId();

		if (is_object($this->old_manager))
			$this->old_manager = $this->old_manager->getId();

		if (is_object($this->old_workObject))
			$this->old_workObject = $this->old_workObject->getId();
		
		if ($this->firm!="") {			
			if ($this->old_firm!="") {
				$this->removeLinks(array($this->old_firm));
			}
			$this->setLinks(array($this->firm));
		}
		
		if ($this->department!="") {
			if ($this->old_department!="")
				$this->removeLinks(array($this->old_department));
			$this->setLinks(array($this->department));
		}

		if ($this->manager!="") {
			if ($this->old_manager!="")
				$this->removeLinks(array($this->old_manager));
			$this->setLinks(array($this->manager));
		}

		if ($this->workObject!="") {
			if ($this->old_workObject!="")
				$this->removeLinks(array($this->old_workObject));
			$this->setLinks(array($this->workObject));
		}		
	}
	
	function getArgs() {		        
		if ($this->window_id=="")
			$this->window_id = @$_GET["window_id"];		
		
		$this->entityImages = json_encode(array());
		
		$this->changesfieldList = "docDate Дата~projectCondition.title AS projectCondition Состояние проекта~title Комментарий";
		$this->changesItemsPerPage = 10;
		$this->changesSortOrder = "docDate DESC";
		$this->changesSortField = $this->changesSortOrder;
		$this->changesConditionFields = "number~Номер~integer|docDate~Дата~date|projectReference~Проект~entity|projectCondition~Состояние~entity";
		$this->changesCondition = "@parent IS NOT EXISTS";
		
		$this->projectChangesTableId = "DocFlowDocumentTable_".$this->module_id."_".$this->name."projectChanges";
		
		$this->projectChangesTableFieldAccess = json_encode(array("projectReference" => "read"));
		$this->projectChangesTableFieldDefaults = json_encode(array("projectReference" => $this->getId()));
		
		$this->projectChangesCode = '$object->additionalCondition="@projectReference.@name='.$this->name.'";
									$object->window_id="'.$this->window_id.'";
									$object->parent_object_id="'.$this->getId().'";
									$object->className="DocumentChangeProjectCondition";
									$object->defaultClassName="DocumentChangeProjectCondition";
									$object->fieldList="'.$this->changesfieldList.'";
									$object->itemsPerPage='.$this->changesItemsPerPage.';
									$object->currentPage=1;
									$object->hierarchy=0;
									$object->additionalFields="name~deleted~registered";
									$object->condition="'.$this->changesCondition.'";
									$object->conditionFields="'.$this->changesConditionFields.'";
									$object->adapterId="'.$this->adapter->getId().'";
									$object->autoload="false";
									$object->sortOrder="'.$this->changesSortOrder.'";';
		
		$this->projectChangesCode = cleanText($this->projectChangesCode);
				
		return parent::getArgs();
	}
	
	function __get($key) {
		if ($key=="projectCondition") {
			global $Objects;
			$reg = $Objects->get("RegistryProjectConditions_".$this->module_id."_1");
			$record = $reg->getValue(time()."000","projectCondition","@projectReference='".get_class($this)."_".$this->name."'");
			if (is_object($record)) {
				if (!$record->loaded)
					$record->load();
				return $record->title;
			}
		}
		return parent::__get($key);
	}
	
	
	function createByOwnerObject() {
		$doc = $this->ownerObject;
		$class = get_class($doc);	
		if ($class!="")
			$this->workObject = $doc;	
		if ($class=="ReferenceFirms") {
			$this->firm = $doc;
		}
		if ($class=="ReferenceDepartments") {
			$this->department = $doc;
		}		
		if ($class=="DocumentContract") {
			$this->contragent = $doc->contragent;
			$this->title = $doc->title;
		}
		if ($class=="DocumentContragentRequest") {
			$this->contragent = $doc->contragent;
			$this->contact = $doc->contact;
			$this->requestText = $doc->requestText; 
			$this->title = $doc->title;
		}
	}
}
?>