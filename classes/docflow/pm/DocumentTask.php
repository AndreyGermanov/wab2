<?php
// Документ "Задача"
class DocumentTask extends Document {
	
	function construct($params) {
		parent::construct($params);
		$this->clientClass = "DocumentTask";
		$this->hierarchy = true;
		$this->parentClientClasses = "Document~Entity";
		$this->classListTitle = "Задачи";
		$this->classTitle = "Задача";		
		$this->template = "renderForm";
        $this->fieldList = "dateStart Дата начала~dateEnd Дата окончания~title Задача~project.title AS project Проект~manager.fullName AS manager Постановщик~taskCondition Состояние задачи~completed % Выполнения";
        $this->additionalFields = "name~isGroup~parent~registered~deleted";
        $this->condition = "@parent IS NOT EXISTS";
        $this->printFieldList = "dateStart Дата начала~dateEnd Дата окончания~title Задача~project.title AS project Проект~manager.fullName AS manager Постановщик~completed % Выполнения";
        $this->allFieldList = $this->fieldList;
        $this->width = "700";
        $this->height = "600";     
        $this->hierarchy = "true";
        $this->overrided = "width,height";
        $this->conditionFields = "title~Описание~string,firm~Организация~entity,department~Отдел~entity,manager~Постановщик~entity,project~Проект~entity,workObject~Объект~entity,dateStart~Дата начала~date,dateEnd~Дата окончания~date,parentTask~Главная задача~entity,completed~% Завершения~integer";        
        $this->sortOrder = "dateStart DESC";
	    $this->tabs_string = "main|Основное|".$this->skinPath."images/spacer.gif";
		$this->tabs_string .= ";advanced|Дополнительно|".$this->skinPath."images/spacer.gif";
	    if ($this->name!="") {
			$this->tabs_string .= ";taskChanges|Состояние|".$this->skinPath."images/spacer.gif";
		}
        $this->tabset_id = "WebItemTabset_".$this->module_id."_DocumentTask".$this->name;
        $this->handler = "scripts/handlers/docflow/pm/DocumentTask.js";
        $this->tabsetName = $this->tabset_id;
        $this->active_tab = "main";
        $this->adapterId = $this->adapter->getId();
        $this->icon = $this->skinPath."images/docflow/task.png";
        $this->createObjectList = array("DocumentContragentRequest" => "Обращение контрагента", "DocumentWorkReport" => "Отчет о работе", "ReferenceContacts" => "Контактное лицо", "DocumentTask" => "Задача");
        if ($this->name=="") {
        	$this->dateStart = time()."000";
        	$this->dateEnd = time()."000";
        }
	}
	
	function renderForm() {
		$blocks = getPrintBlocks(file_get_contents("templates/docflow/pm/DocumentTask.html"));
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
		$this->old_project = $this->project;
		$this->old_firm = $this->firm;
		$this->old_department = $this->department;
		$this->old_manager = $this->manager;
		$this->old_worker = $this->worker;
		$this->old_workObject = $this->workObject;
		$this->old_parentTask = $this->parentTask;
				
	}
	
	function checkData() {
		if (!$this->isGroup) {
			if ($this->manager=="") {
				$this->reportError("Укажите постановщика задачи","save");
				return 0;
			}				
			if ($this->dateStart=="") {
				$this->reportError("Укажите дату начала выполнения задачи","save");
				return 0;
			}						
			if ($this->completed>100) {
				$this->reportError("Процент выполнения не может быть больше 100","save");
				return 0;				
			}	
			if ($this->dateStart==$this->dateEnd) {
				$this->dateStart = getBeginOfDay($this->dateStart/1000)."000";
				$this->dateEnd = getEndOfDay($this->dateEnd/1000)."000";
			}
				
		}
		return parent::checkData();			
	}
	
	function afterSave($out=true) {
		parent::afterSave($out);
		
		if (is_object($this->project))
			$this->project = $this->project->getId();
		
		if (is_object($this->firm))
			$this->firm = $this->firm->getId();

		if (is_object($this->department))
			$this->department = $this->department->getId();

		if (is_object($this->manager))
			$this->manager = $this->manager->getId();

		if (is_object($this->worker))
			$this->worker = $this->worker->getId();
		
		if (is_object($this->workObject))
			$this->workObject = $this->workObject->getId();
		
		if (is_object($this->parentTask))
			$this->parentTask = $this->parentTask->getId();
		
		if (is_object($this->old_project))
			$this->old_project = $this->old_project->getId();
	
		if (is_object($this->old_firm))
			$this->old_firm = $this->old_firm->getId();

		if (is_object($this->old_department))
			$this->old_department = $this->old_department->getId();

		if (is_object($this->old_manager))
			$this->old_manager = $this->old_manager->getId();

		if (is_object($this->old_worker))
			$this->old_worker = $this->old_worker->getId();
		
		if (is_object($this->old_workObject))
			$this->old_workObject = $this->old_workObject->getId();

		if (is_object($this->old_parentTask))
			$this->old_parentTask = $this->old_parentTask->getId();
		
		if ($this->project!="") {			
			if ($this->old_project!="") {
				$this->removeLinks(array($this->old_project));
			}
			$this->setLinks(array($this->project));
		}
		
		if ($this->firm!="") {
			if ($this->old_firm!="")
				$this->removeLinks(array($this->old_firm));
			$this->setLinks(array($this->firm));
		}

		if ($this->department!="") {
			if ($this->old_department!="") {
				$this->removeLinks(array($this->old_department));
			}
			$this->setLinks(array($this->department));
		}

		if ($this->manager!="") {
			if ($this->old_manager!="") {
				$this->removeLinks(array($this->old_manager));
			}
			$this->setLinks(array($this->manager));
		}

		if ($this->worker!="") {
			if ($this->old_worker!="") {
				$this->removeLinks(array($this->old_worker));
			}
			$this->setLinks(array($this->worker));
		}
		
		if ($this->workObject!="") {
			if ($this->old_workObject!="") {
				$this->removeLinks(array($this->old_workObject));
			}
			$this->setLinks(array($this->workObject));
		}

		if ($this->parentTask!="") {
			if ($this->old_parentTask!="") {
				$this->removeLinks(array($this->old_parentTask));
			}
			$this->setLinks(array($this->parentTask));
		}		
	}
	
	function getArgs() {

		global $Objects;
				
		if (!is_object($this->firm) and $this->firm!="")
			$this->firm = $Objects->get($this->firm);		
		
		if ($this->name=="") {
			if ($this->firm=="") {
				if ($this->manager!="") {
					if (!is_object($this->manager))
						$this->manager = $Objects->get($this->manager->getId());
					$this->firm = $this->manager->firm;
				}
			}
		}		
				
		if ($this->window_id=="")
			$this->window_id = @$_GET["window_id"];
		
		$this->entityImages = json_encode(array());

		$this->changesfieldList = "docDate Дата~taskCondition.title AS taskCondition Состояние задачи~title Комментарий";
		$this->changesItemsPerPage = 10;
		$this->changesSortOrder = "docDate DESC";
		$this->changesSortField = $this->changesSortOrder;
		$this->changesConditionFields = "number~Номер~integer|docDate~Дата~date|taskDocument~Задача~entity|taskCondition~Состояние~entity";
		$this->changesCondition = "@parent IS NOT EXISTS";
		
		$this->taskChangesTableId = "DocFlowDocumentTable_".$this->module_id."_".$this->name."taskChanges";
		
		$this->taskChangesTableFieldAccess = json_encode(array("taskDocument" => "read"));
		$this->taskChangesTableFieldDefaults = json_encode(array("taskDocument" => $this->getId()));
		
		$this->taskChangesCode = '$object->additionalCondition="@taskDocument.@name='.$this->name.'";
		$object->window_id="'.$this->window_id.'";
		$object->parent_object_id="'.$this->getId().'";
		$object->className="DocumentChangeTaskCondition";
		$object->defaultClassName="DocumentChangeTaskCondition";
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
		
		$this->taskChangesCode = cleanText($this->taskChangesCode);
		
		return parent::getArgs();
	}
			
	function createByOwnerObject() {
		$doc = $this->ownerObject;
		$class = get_class($doc);		
		if ($class!="")
			$this->workObject = $doc;
		if ($class=="ReferenceProjects") {
			$this->project = $doc;
			$this->firm = $doc->firm;
			$this->department = $doc->department;
		}
		if ($class=="DocumentTask") {
			$this->parentTask = $doc;
			$this->project = $doc->project;
			$this->firm = $doc->firm;
			$this->department = $doc->department;
			$this->workObject = $doc->workObject;
		}
		if ($class=="DocumentContragentRequest") {
			$this->title = $doc->title;
		}
		if ($class=="DocumentOrder") {
			$this->title = $doc->title;
		}		
		if ($class=="DocumentContract") {
			$this->title = $doc->title;
		}
		if ($class=="DocumentInvoice") {
			$this->firm = $doc->firm;
			$this->title = $doc->title;
			$this->department = $doc->department;
		}		
	}
	
	function __get($key) {
		if ($key=="taskCondition") {
			global $Objects;
			$reg = $Objects->get("RegistryTaskConditions_".$this->module_id."_1");
			$record = $reg->getValue(time()."000","taskCondition","@taskDocument='".get_class($this)."_".$this->name."'");
			if (is_object($record)) {
				if (!$record->loaded)
					$record->load();
				return $record->title;
			}
		}
		return parent::__get($key);
	}	
	
	function getPrintForms() {
		return array();
	}	

	function register() {
		parent::register();
		global $Objects;
		if ($this->worker!="" and is_object($this->worker)) {
			$record = $Objects->get("RegistryTaskWorkers_".$this->module_id."_");
			$record->document = $this;
			$record->regDate = $this->docDate;
			$record->firm = $this->firm;
			$record->workObject = $this->workObject;
			$record->worker = $this->worker;
			$record->task = $this;
			$record->dateStart = $this->dateStart;
			$record->dateEnd = $this->dateEnd;		
			$record->period = ($this->dateEnd-$this->dateStart)/1000;
			if ($record->period<0)
				$record->period = 0;
			$record->save(true);
			$Objects->remove("RegistryTaskWorkers_".$this->module_id."_");
			$Objects->remove("Registry_".$this->module_id."_");
		}
	}	
}
?>
