<?php
// Справочник контрагентов
class ReferenceContragents extends Reference {
	
	function construct($params) {		
		parent::construct($params);
		$this->clientClass = "ReferenceContragents";
		$this->hierarchy = true;
		$this->parentClientClasses = "Reference~Entity";
		$this->classListTitle = "Контрагенты";
		$this->classTitle = "Контрагент";		
		$this->template = "renderForm";
        $this->fieldList = "title Наименование~phones Телефоны~defaultEmail.title AS email Email";
        $this->additionalFields = "name~isGroup~parent~deleted";
        $this->condition = "@parent IS NOT EXISTS";
        $this->printFieldList = $this->fieldList;
        $this->allFieldList = $this->fieldList;
        $this->width = "720";
        $this->height = "550";        
        $this->overrided = "width,height";
        $this->conditionFields = "title~Наименование~string,INN~ИНН~integer,KPP~КПП~integer,officialAddress~Юридический адрес~text,postalAddress~Почтовый адрес~text,phones~Телефоны~string,defaultEmail.title~E-mail~string,kind~Признак";        
        $this->sortOrder = "title ASC";
        $this->handler = "scripts/handlers/docflow/common/ReferenceContragents.js";
        $this->tabset_id = "WebItemTabset_".$this->module_id."_ReferenceContragents".$this->name;
        $this->tabsetName = $this->tabset_id;
		$this->tabs_string = "main|Основное|".$this->skinPath."images/spacer.gif;";
        $this->tabs_string.= "contacts|Адреса и телефоны|".$this->skinPath."images/spacer.gif";        
        $this->tabs_string.= ";simpleInfo|Информация|".$this->skinPath."images/spacer.gif";        
        if ($this->name!="") {
			$this->tabs_string.= ";banks|Расчетные счета|".$this->skinPath."images/spacer.gif";
			$this->tabs_string.= ";emails|Адреса Email|".$this->skinPath."images/spacer.gif";
        	$this->tabs_string.= ";quickSales|Заказы|".$this->skinPath."images/spacer.gif";        
        	$this->tabs_string.= ";discountReport|Дисконтная карта|".$this->skinPath."images/spacer.gif";        
        }				
        $this->active_tab = "main";
        $this->urDisplay = "";
        $this->fizDisplay = "none";
        $this->titleTitle = "Наименование";
        $this->itemsPerPage = "10";
        $this->adapterId = $this->adapter->getId();
		$this->icon = $this->skinPath."images/docflow/contragent.png";		
		global $Objects;
        $this->app = $Objects->get("Application");
        $this->skinPath = $this->app->skinPath;
        $this->receiveBarCodes = "1";
        $this->createObjectList = array("DocumentContragentRequest" => "Обращение контрагента", "DocumentOrder" => "Заказ", "DocumentContract" => "Договор", "DocumentInvoice" => "Счет на оплату", "ReferenceContacts" => "Контактное лицо", "ReferenceProjects" => "Проект", "DocumentTask" => "Задача");        
	}
	
	function renderForm() {
		$blocks = getPrintBlocks(file_get_contents("templates/docflow/common/ReferenceContragents.html"));
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
	
	function getPresentation($full=true) {
		if ($this->noPresent)
			return "";
		$this->loaded = false;
		$this->load();
		if (!$full) {
			if ($this->discountCardNumber!="")
				return $this->title." (".$this->discountCardNumber.")";
			else
				return $this->title;
		}		 
		if ($this->classTitle != "")
			return $this->classTitle.":".$this->title;
		else
			return $this->title;
	}	
		
	function checkData() {
		
		$this->familyName = trim($this->familyName);
		$this->firstName = trim($this->firstName);
		$this->secondName = trim($this->secondName);
		$this->discountCardNumber = trim($this->discountCardNumber);
		$this->referrerNumber = trim($this->referrerNumber);
		
		if ($this->familyName!="") {
			if ($this->firstName=="") {
				$this->reportError("Укажите имя!","save");
				return 0;
			}
			if ($this->secondName=="") {
				$this->reportError("Укажите отчество!","save");
				return 0;
			}
			if ($this->discountCardNumber=="") {
				$this->reportError("Укажите номер дисконтной карты!","save");
				return 0;				
			} else {
				if ($this->name!="")
					$query = "SELECT entities FROM fields WHERE @discountCardNumber='".$this->discountCardNumber."' AND @name!=".$this->name." AND @classname='".get_class($this)."'";
				else
					$query = "SELECT entities FROM fields WHERE @discountCardNumber='".$this->discountCardNumber."' AND @classname='".get_class($this)."'";
				$result = PDODataAdapter::makeQuery($query, $this->adapter,$this->module_id);
				if (count($result)>0) {
					$this->reportError("Дисконтная карта с указанным номером уже выдавалась другому клиенту!","save");
					return 0;
				}
			}
			$this->title = $this->familyName." ".$this->firstName." ".$this->secondName;
			if ($this->name!="")
				$query = "SELECT entities FROM fields WHERE @title='".$this->title."' AND @name!=".$this->name." AND @classname='".get_class($this)."'";
			else
				$query = "SELECT entities FROM fields WHERE @title='".$this->title."' AND @classname='".get_class($this)."'";
			$result = PDODataAdapter::makeQuery($query, $this->adapter,$this->module_id);
			if (count($result)>0) {
				$this->reportError("Клиент с указанной комбинацией фамилии имени и отчества уже есть в базе!","save");
				return 0;
			}
				
			if ($this->referrerNumber!="") {
				if ($this->referrerNumber == $this->discountCardNumber) {
					$this->reportError("Номер дисконтной карты не должен совпадать с номером реферрера");
					return 0;
				}
				if ($this->name!="")
					$query = "SELECT entities FROM fields WHERE @referrerNumber='".$this->referrerNumber."' AND @name!=".$this->name." AND @classname='".get_class($this)."'";
				else
					$query = "SELECT entities FROM fields WHERE @referrerNumber='".$this->referrerNumber."' AND @classname='".get_class($this)."'";
				$query = "SELECT entities FROM fields WHERE @discountCardNumber='".$this->referrerNumber."' AND @classname='".get_class($this)."'";
				$result = PDODataAdapter::makeQuery($query, $this->adapter,$this->module_id);
				if (count($result)==0) {
					$this->reportError("Номер реферрера указан неверно! Карта с таким номером никогда не выдавалась!","save");
					return 0;
				}				
			}			
		}
		
		if ($this->title=="") {
			$this->reportError("Укажите наименование клиента","save");
			return 0;
		}		
		if (!$this->isGroup and $this->familyName=="") {
			if ($this->INN!="" and !preg_match('/^[1-9][0-9]{9}$/',$this->INN)) {
				$this->reportError("ИНН указан не верно","save");
				return 0;
			}
			
			if ($this->KPP!="" and !preg_match('/^[1-9][0-9]{8}$/',$this->KPP)) {
				$this->reportError("КПП указан не верно","save");
				return 0;				
			}
				
			if ($this->INN != "") {
				if ($this->name!="")
					$query = "SELECT entities FROM fields WHERE @INN=".$this->INN." AND @name!=".$this->name." AND @classname='".get_class($this)."'";
				else
					$query = "SELECT entities FROM fields WHERE @INN=".$this->INN." AND @classname='".get_class($this)."'";				
				$result = PDODataAdapter::makeQuery($query, $this->adapter,$this->module_id);
				if (count($result)>0) {
					$this->reportError("Контрагент с таким ИНН уже есть в базе!","save");
					return 0;
				}
			}
		} 		
		return parent::checkData();			
	}
	
	function getArgs() {		   
		if ($this->type=="1" || $this->type=="") {
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

		$fname = "/var/WAB2/users/".$this->app->User."/arguments/".str_replace("_","",$this->getId());
		file_put_contents($fname,serialize(array("defaultReport" => "Остатки", "conditions" => array("contragent" => array("type" => "=", "value" => $this->getId())))));
		$this->reportArguments = $fname;
		$this->bsfieldList = "RS Номер счета~BIK БИК~KS К.с.~bank.title AS bank Банк";
		$this->bsitemsPerPage = 10;
		$this->bssortOrder = "title ASC";
		$this->bssortField = $this->bssortOrder;
		$this->bsConditionFields = "RS~№ счета!!!!~string|BIK~БИК~integer|KS~Корр.счет~string|bank.title~Банк~string";
		$this->bscondition = "@parent IS NOT EXISTS";

		$this->bankAccountsTableId = "DocFlowReferenceTable_".$this->module_id."_".$this->name."bankAccounts";
		
		$this->accountsTableFieldAccess = json_encode(array("contragent" => "read"));
		$this->accountsTableFieldDefaults = json_encode(array("contragent" => $this->getId()));
		
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

		$this->bankAccountsCode = cleanText($this->bankAccountsCode);

		$this->qsfieldList = "docDate Дата~number Номер~orderSumma Сумма~discountSumma Скидка";
		$this->qsitemsPerPage = 10;
		$this->qssortOrder = "date DESC";
		$this->qssortField = $this->qssortOrder;
		$this->qsConditionFields = "contragent~Контрагент~entity|contragent.title~Наименование контрагента~string|orderSumma~Сумма~decimal|discountSumma~Списанная скидка~decimal|prihodSumma~Получено от клиента~decimal|documentTable~Наименования~string";
		$this->qscondition = "@parent IS NOT EXISTS";
		
		$this->quickSaleTableId = "DocFlowDocumentTable_".$this->module_id."_".$this->name."quickSale";
		
		$this->quickSaleTableFieldAccess = json_encode(array("contragent" => "read"));
		$this->quickSaleTableFieldDefaults = json_encode(array("contragent" => $this->getId()));
		
		$this->quickSaleTableCode = '$object->additionalCondition="@contragent.@name='.$this->name.'";
		$object->window_id="'.$this->window_id.'";
		$object->parent_object_id="'.$this->getId().'";
		$object->className="DocumentQuickSale";
		$object->defaultClassName="DocumentQuickSale";
		$object->fieldList="'.$this->qsfieldList.'";
		$object->itemsPerPage='.$this->qsitemsPerPage.';
		$object->currentPage=1;
		$object->hierarchy=0;
		$object->additionalFields="name~deleted~registered";
		$object->condition="'.$this->qscondition.'";
		$object->conditionFields="'.$this->qsConditionFields.'";
		$object->adapterId="'.$this->adapter->getId().'";
		$object->autoload="false";
		$object->sortOrder="'.$this->qssortOrder.'";';
		
		$this->quickSaleTableCode = cleanText($this->quickSaleTableCode);
				
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
						
		$this->discountCardReadOnly = "false";
		$this->referrerNumberReadOnly = "false";
		
		if ($this->discountCardNumber!="") {
			$query = "SELECT entities FROM fields WHERE @discountCardNumber='".$this->discountCardNumber."' AND @classname='RegistryDiscountCards'";
			$result = PDODataAdapter::makeQuery($query, $this->adapter,$this->module_id);				
			if (count($result)>0)
				$this->discountCardReadOnly = "true";
			$query = "SELECT entities FROM fields WHERE @referrerNumber='".$this->discountCardNumber."' AND @classname='ReferenceContragents'";
			$result = PDODataAdapter::makeQuery($query, $this->adapter,$this->module_id);				
			if (count($result)>0)
				$this->discountCardReadOnly = "true";
		}
		if ($this->referrerNumber!="") {
			$query = "SELECT entities FROM fields WHERE @registered=1 AND @contragent='".str_replace($this->module_id."_","",$this->getId())."' AND @classname='DocumentQuickSale'";
			$result = PDODataAdapter::makeQuery($query, $this->adapter,$this->module_id);
			$arr = array();
			if (count($result)>0) {
				foreach ($result as $record) {
					$arr[] = "'".str_replace($this->module_id."_","",$record->getId())."'";
				}
			}
			if (count($arr)>0) {
				$query = "SELECT entities FROM fields WHERE @discountCardNumber='".$this->referrerNumber."' AND @summa>0 AND @document IN (".implode(",",$arr).") AND @classname='RegistryDiscountCards'";
				$result = PDODataAdapter::makeQuery($query, $this->adapter,$this->module_id);
				if (count($result)>0) {
					$this->referrerNumberReadOnly = "true";
				}
			}				
		}
		return parent::getArgs();
	}			
}
?>