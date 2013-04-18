<?php
// Справочник организаций
class ReferenceFirms extends Reference {
	
	function construct($params) {		
		parent::construct($params);
		$this->clientClass = "ReferenceFirms";
		$this->hierarchy = true;
		$this->parentClientClasses = "Reference~Entity";
		$this->classListTitle = "Организации";
		$this->classTitle = "Организация";		
		$this->template = "renderForm";
        $this->fieldList = "title Наименование~phones Телефоны~defaultEmail.email AS email Email";
        $this->additionalFields = "name~isGroup~parent~deleted";
        $this->condition = "@parent IS NOT EXISTS";
        $this->printFieldList = $this->fieldList;
        $this->allFieldList = $this->fieldList;
        $this->width = "700";
        $this->height = "500";        
        $this->overrided = "width,height";
        $this->conditionFields = "title~Наименование~string,INN~ИНН~integer,KPP~КПП~integer,officialAddress~Юридический адрес~text,postalAddress~Почтовый адрес~text,phones~Телефоны~string,defaultEmail.title~E-mail~string";        
        $this->sortOrder = "title ASC";
        $this->handler = "scripts/handlers/docflow/common/ReferenceFirms.js";
        $this->tabset_id = "WebItemTabset_".$this->module_id."_ReferenceFirms".$this->name;
        $this->tabsetName = $this->tabset_id;
		$this->tabs_string = "main|Основное|".$this->skinPath."images/spacer.gif;";
        $this->tabs_string.= "contacts|Адреса и телефоны|".$this->skinPath."images/spacer.gif";        
        $this->tabs_string.= ";codes|Коды|".$this->skinPath."images/spacer.gif";        
        $this->tabs_string.= ";fonds|Фонды|".$this->skinPath."images/spacer.gif";        
        $this->tabs_string.= ";faces|Лица|".$this->skinPath."images/spacer.gif";        
        if ($this->name!="") {
			$this->tabs_string.= ";banks|Расчетные счета|".$this->skinPath."images/spacer.gif";
			$this->tabs_string.= ";emails|Адреса Email|".$this->skinPath."images/spacer.gif";
		}				
        $this->active_tab = "main";
        $this->urDisplay = "";
        $this->fizDisplay = "none";
        $this->titleTitle = "Наименование";
        $this->type = "1";
        $this->itemsPerPage = "10";
        $this->adapterId = $this->adapter->getId();
		$this->bsfieldList = "RS Номер счета~BIK БИК~KS К.с.~bank.title AS bank Банк";
		$this->bsitemsPerPage = 10;
		$this->bssortOrder = "title ASC";
		$this->bssortField = $this->bssortOrder;
		$this->bsConditionFields = "RS~№ счета!!!!~string|BIK~БИК~integer|KS~Корр.счет~string|bank.title~Банк~string";
		$this->bscondition = "@parent IS NOT EXISTS";      
		$this->icon = $this->skinPath."images/docflow/firm.png";		
		global $Objects;
        $this->app = $Objects->get("Application");
        $this->skinPath = $this->app->skinPath;
	}
	
	function renderForm() {
		$blocks = getPrintBlocks(file_get_contents("templates/docflow/common/ReferenceFirms.html"));
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
			$this->reportError("Укажите наименование","save");
			return 0;
		}		
		if (!$this->isGroup) {
			if ($this->INN!="" and !preg_match('/^[1-9][0-9]{9}$/',$this->INN)) {
				$this->reportError("ИНН указан не верно","save");
				return 0;
			}
			
			if ($this->KPP!="" and !preg_match('/^[1-9][0-9]{8}$/',$this->KPP)) {
				$this->reportError("КПП указан не верно","save");
				return 0;				
			}

			if ($this->OKPO!="" and !preg_match('/^[1-9][0-9]{7}$/',$this->OKPO)) {
				$this->reportError("Код ОКПО указан не верно","save");
				return 0;
			}
				
			if ($this->INN != "") {
				if ($this->name!="")
					$query = "SELECT entities FROM fields WHERE @INN=".$this->INN." AND @name!=".$this->name." AND @classname='".get_class($this)."'";
				else
					$query = "SELECT entities FROM fields WHERE @INN=".$this->INN." AND @classname='".get_class($this)."'";				
				$result = PDODataAdapter::makeQuery($query, $this->adapter,$this->module_id);
				if (count($result)>0) {
					$this->reportError("Организация с таким ИНН уже есть в базе!","save");
					return 0;
				}
			}
		} 		
		return parent::checkData();			
	}
	
	function getArgs() {		   
		if ($this->type=="1") {
			$this->urDisplay = "";
			$this->fizDisplay = "none";
			$this->titleTitle = "Наименование";
		} else {
			$this->urDisplay = "none";
			$this->fizDisplay = "";
			$this->titleTitle = "ФИО";				
		}

		if ($this->window_id=="")
			$this->window_id = @$_GET["window_id"];
		
		$this->entityImages = json_encode(array());
		if (is_object($this->defaultBankAccount)) {			
			$this->entityImages = json_encode(array($this->defaultBankAccount->getId() => $this->skinPath."images/Buttons/RegisteredDocumentEntityImage.png"));
		}			
		
		$this->bankAccountsCode = '$object->additionalCondition="@contragent.@name='.$this->name.'";
								   $object->window_id="'.$this->window_id.'";
								   $object->parent_object_id="'.$this->getId().'";
								   $object->className="ReferenceBankAccounts";
								   $object->defaultClassName="ReferenceBankAccounts";
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

		$this->emailfieldList = "email Адрес Email~title Описание";
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
									$object->className="ReferenceEmailAccounts";
									$object->defaultClassName="ReferenceEmailAccoubts";
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
				
		$this->bankAccountsCode = cleanText($this->bankAccountsCode);
		$this->tabsetCode = cleanText($this->tabsetCode);		
		$this->bankAccountsTableId = "DocFlowReferenceTable_".$this->module_id."_".$this->name."bankAccounts";
		
		$this->accountsTableFieldAccess = json_encode(array("contragent" => "read"));
		$this->accountsTableFieldDefaults = json_encode(array("contragent" => $this->getId()));
		
		return parent::getArgs();
	}		
}
?>