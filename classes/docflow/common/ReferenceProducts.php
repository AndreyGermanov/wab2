<?php
// Справочник номенклатуры
class ReferenceProducts extends Reference {
	
	function construct($params) {		
		parent::construct($params);
		$this->clientClass = "ReferenceProducts";
		$this->hierarchy = true;
		$this->parentClientClasses = "Reference~Entity";
		$this->classListTitle = "Номенклатура";
		$this->classTitle = "Номенклатура";		
		$this->template = "renderForm";
        $this->fieldList = "title Наименование~code Код~cost Цена~dimension.title AS dimension Ед. изм.";
        $this->additionalFields = "name~isGroup~parent~deleted";
        $this->condition = "@parent IS NOT EXISTS";
        $this->printFieldList = $this->fieldList;
        $this->allFieldList = $this->fieldList;
        $this->width = "700";
        $this->height = "500";        
        $this->overrided = "width,height";
        $this->conditionFields = "title~Наименование~string,code~Код~string,kind~Признак~entity,cost~Цена~decimal,NDS~НДС~integer,dimension~Единица измерения~entity,description~Описание~string";        
        $this->sortOrder = "title ASC";
        $this->handler = "scripts/handlers/docflow/common/ReferenceProducts.js";
        $this->tabset_id = "WebItemTabset_".$this->module_id."_ReferenceProducts".$this->name;
        $this->tabsetName = $this->tabset_id;
		$this->tabs_string = "main|Основное|".$this->skinPath."images/spacer.gif;";
        $this->tabs_string.= "descriptionTab|Описание|".$this->skinPath."images/spacer.gif";        
        $this->active_tab = "main";
        $this->type = "1";
        $this->itemsPerPage = "10";
        $this->adapterId = $this->adapter->getId();
		$this->icon = $this->skinPath."images/Tree/objectgroup.png";		
		global $Objects;
        $this->app = $Objects->get("Application");
        $this->skinPath = $this->app->skinPath;
//        $this->createObjectList = array("DocumentContragentRequest" => "Обращение контрагента", "DocumentOrder" => "Заказ", "DocumentContract" => "Договор", "DocumentInvoice" => "Счет на оплату", "ReferenceContacts" => "Контактное лицо", "ReferenceProjects" => "Проект", "DocumentTask" => "Задача");        
	}
	
	function renderForm() {
		$blocks = getPrintBlocks(file_get_contents("templates/docflow/common/ReferenceProducts.html"));
		$common = getPrintBlocks(file_get_contents("templates/docflow/common/CommonFields.html"));
		$out = str_replace("{hiddenFields}",$common["hiddenFields"],$blocks["header"]);
		if ($this->name!="") {
			$out .= $common["tabs"];
			$out .= $common["profileEditTab"];
		}
		$out .= $blocks["footer"];
		return $out;
	}
	
	function getPresentation($full=true) {
		if ($this->noPresent)
			return "";
		$this->loaded = false;
		$this->load();
		if (!$full)
			return $this->title;
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
				
			if ($this->code != "") {
				if ($this->name!="")
					$query = "SELECT entities FROM fields WHERE @code='".$this->code."' AND @name!=".$this->name." AND @classname='".get_class($this)."'";
				else
					$query = "SELECT entities FROM fields WHERE @code='".$this->code."' AND @classname='".get_class($this)."'";				
				$result = PDODataAdapter::makeQuery($query, $this->adapter,$this->module_id);
				if (count($result)>0) {
					$this->reportError("Позиция с таким кодом уже есть в базе!","save");
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
		if (is_object($this->defaultBankAccount)) {			
			$this->entityImages = json_encode(array($this->defaultBankAccount->getId() => $this->skinPath."images/Buttons/RegisteredDocumentEntityImage.png"));
		}			
		
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