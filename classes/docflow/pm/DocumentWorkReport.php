<?php
// Документ "Отчет о работе"
class DocumentWorkReport extends Document {
	
	function construct($params) {
		parent::construct($params);
		$this->clientClass = "DocumentWorkReport";
		$this->hierarchy = true;
		$this->parentClientClasses = "Document~Entity";
		$this->classListTitle = "Отчеты о работе";
		$this->classTitle = "Отчет о работе";
		$this->template = "renderForm";
        $this->fieldList = "dateStart Дата начала~dateEnd Дата окончания~employee.fullName AS employee Сотрудник~reportText Отчет";
        $this->additionalFields = "name~isGroup~parent~registered~deleted";
        $this->condition = "@parent IS NOT EXISTS";
        $this->printFieldList = $this->fieldList;
        $this->allFieldList = $this->fieldList;
        $this->width = "700";
        $this->height = "600";     
        $this->hierarchy = "true";
        $this->overrided = "width,height";
        $this->conditionFields = "dateStart~Дата начала~date,dateEnd~Дата окончания~date,employee~Сотрудник~entity,workObject~Объект~entity,firm~Организация~entity";        
        $this->sortOrder = "dateStart ASC";
        $this->tabs_string  = "main|Основное|".$this->skinPath."images/spacer.gif";
        $this->tabs_string .= ";additional|Дополнительно|".$this->skinPath."images/spacer.gif";
        $this->tabset_id = "WebItemTabset_".$this->module_id."_DocumentWorkReport".$this->name;
        $this->tabsetName = $this->tabset_id;
        $this->active_tab = "main";
        $this->adapterId = $this->adapter->getId();
        $this->icon = $this->skinPath."images/Tree/document.png";
        $this->dateStart = time()."000";
        $this->dateEnd = time()."000";
	}
	
	function renderForm() {
		$blocks = getPrintBlocks(file_get_contents("templates/docflow/pm/DocumentWorkReport.html"));
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
		$this->old_employee = $this->employee;
		$this->old_firm = $this->firm;
		$this->old_workObject = $this->workObject;
	}
			
	function checkData() {
		global $Objects;
		if (!$this->isGroup) {
			if ($this->dateStart>$this->dateEnd) {
				$this->reportError("Дата начала больше даты окончания !");
				return 0;
			}
			$currentDate = time()."000";
			if ($this->dateStart>$currentDate) {
				$this->reportError("Дата начала в будущем !");
				return 0;				
			}
			if ($this->dateEnd>$currentDate) {
				$this->reportError("Дата окончания в будущем !");
				return 0;				
			}
			if ($this->employee=="") {
				$this->reportError("Укажите сотрудника","save");
				return 0;
			}				
			if ($this->firm=="") {
				$this->reportError("Укажите организацию","save");
				return 0;
			}
			$reg = $Objects->get("RegistryWorkingTime_".$this->module_id."_".$this->name);
			if (is_object($this->employee))
				$employee = $this->employee->getId();
			else
				$employee = $this->employee;
			$records = $reg->getRecords(0,0,"dateStart,dateEnd","@resource='".str_replace($this->module_id."_","",$employee)."' AND @document!='".str_replace($this->module_id."_","",$this->getId())."'");
			foreach ($records as $value) {
				if (($this->dateStart>$value["dateStart"] and $this->dateStart<$value["dateEnd"]) or ($this->dateEnd>$value["dateStart"] and $this->dateEnd<$value["dateEnd"]) or
					($value["dateStart"]>$this->dateStart and $value["dateStart"]<$this->dateEnd) or ($value["dateEnd"]>$this->dateStart and $value["dateEnd"]<$this->dateEnd)) {
					$this->reportError("Сотрудник уже работал в указанный промежуток времени!");
					return 0;
				}					
			}
		}
		return parent::checkData();			
	}
	
	function afterSave($out=true) {
		parent::afterSave($out);
		
		if (is_object($this->employee))
			$this->employee = $this->employee->getId();
		
		if (is_object($this->firm))
			$this->firm = $this->firm->getId();
		
		if (is_object($this->workObject))
			$this->workObject = $this->workObject->getId();
		
		if (is_object($this->old_employee))
			$this->old_employee = $this->old_employee->getId();

		if (is_object($this->old_firm))
			$this->old_firm = $this->old_firm->getId();

		if (is_object($this->old_workObject))
			$this->old_workObject = $this->old_workObject->getId();

		if ($this->employee!="") {			
			if ($this->old_employee!="") {
				$this->removeLinks(array($this->old_employee));
			}
			$this->setLinks(array($this->employee));
		}
		
		if ($this->firm!="") {
			if ($this->old_firm!="")
				$this->removeLinks(array($this->old_firm));
			$this->setLinks(array($this->firm));
		}

		if ($this->workObject!="") {
			if ($this->old_workObject!="")
				$this->removeLinks(array($this->old_workObject));
			$this->setLinks(array($this->workObject));
		}		
	}
	
	function getArgs() {
		global $Objects;
		if (!is_object($this->employee) and $this->employee!="")
			$this->employee = $Objects->get($this->employee);
		
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
			
			if ($this->employee=="") {
				if ($this->manager!="") {
					if (!is_object($this->manager))
						$this->manager = $Objects->get($this->manager->getId());
					$this->employee = $this->manager;
				}
			}
		}	
				
		if ($this->window_id=="")
			$this->window_id = @$_GET["window_id"];
		
		$this->entityImages = json_encode(array());
				
		return parent::getArgs();
	}
			
	function createByOwnerObject() {
		$doc = $this->ownerObject;
		$class = get_class($doc);
		if ($class!="")
			$this->workObject = $doc;		
		if ($class=="ReferenceContragents") {
			$this->workObject = $doc;
		}
		if ($class=="DocumentContragentRequest") {
			$this->workObject = $doc;
			$this->title = $doc->title;
		}
		if ($class=="DocumentContract") {
			$this->workObject = $doc;
			$this->title = $doc->title;
		}
		if ($class=="DocumentOrder") {
			$this->workObject = $doc;
			$this->title = $doc->title;
		}
		if ($class=="DocumentInvoice") {
			$this->workObject = $doc;
			$this->firm = $doc->firm;
			$this->title = $doc->title;
		}		
		if ($class=="ReferenceFiles") {
			$this->workObject = $doc;
			$this->title = $doc->title;
		}		
		if ($class=="ReferenceNotes") {
			$this->workObject = $doc;
			$this->title = $doc->title;
		}		
		if ($class=="ReferenceProjects") {
			$this->workObject = $doc;
		}		
	}	
	
	function register() {
		parent::register();
		global $Objects;
		$record = $Objects->get("RegistryWorkingTime_".$this->module_id."_");
		$record->document = $this;
		$record->regDate = $this->docDate;
		$record->firm = $this->firm;
		$record->resource = $this->employee;
		$record->workObject = $this->workObject;
		$record->dateStart = $this->dateStart;
		$record->dateEnd = $this->dateEnd;
		$record->reportText = $this->reportText;
		$record->period = ($this->dateEnd-$this->dateStart)/1000;
		$record->save(true);
		$Objects->remove("RegistryWorkingTime_".$this->module_id."_");
		$Objects->remove("Registry_".$this->module_id."_");
	}	
}
?>