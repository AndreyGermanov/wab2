<?php
// Документ "Розничная продажа"
class DocumentQuickSale extends Document {
	
	function construct($params) {
		parent::construct($params);
		$this->clientClass = "DocumentQuickSale";
		$this->hierarchy = true;
		$this->parentClientClasses = "Document~Entity";
		$this->classListTitle = "Розничные продажи";
		$this->classTitle = "Розничная продажа";		
		$this->template = "renderForm";
        $this->fieldList = "docDate Дата~number Номер~contragent.title AS contragent Контрагент~orderSumma Сумма~discountSumma Скидка~manager.fullName AS manager Менеджер";
        $this->additionalFields = "name~isGroup~parent~registered~deleted";
        $this->condition = "@parent IS NOT EXISTS";
        $this->printFieldList = $this->fieldList;
        $this->allFieldList = $this->fieldList;
        $this->width = "800";
        $this->height = "600";     
        $this->hierarchy = "true";
        $this->overrided = "width,height";
        $this->conditionFields = "contragent~Контрагент~entity,contragent.title~Наименование контрагента~string,orderSumma~Сумма~decimal,discountSumma~Скидка~decimal,prihodSumma~Получено от клиента~decimal,documentTable~Таблица~string";        
        $this->sortOrder = "docDate DESC";
        $this->tabs_string  = "main|Основное|".$this->skinPath."images/spacer.gif";
        $this->tabset_id = "WebItemTabset_".$this->module_id."_DocumentInvoice".$this->name;
        $this->handler = "scripts/handlers/docflow/rozn/DocumentQuickSale.js";
        $this->tabsetName = $this->tabset_id;
        $this->active_tab = "main";
        $this->adapterId = $this->adapter->getId();
        $this->icon = $this->skinPath."images/Tree/document.png";
        $this->createObjectList = array("DocumentContragentRequest" => "Обращение контрагента", "DocumentWorkReport" => "Отчет о работе", "ReferenceContacts" => "Контактное лицо", "DocumentTask" => "Задача");
        $this->helpGuideId = "rozn_6.2";
	}
	
	function renderForm() {
		$blocks = getPrintBlocks(file_get_contents("templates/docflow/rozn/DocumentQuickSale.html"));
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
		if ($this->loaded)
			return 0;
		parent::load();		
		$this->loaded = true;
		$this->old_contragent = $this->contragent;
	}
			
	function checkData() {
		if (!$this->isGroup) {
			if ($this->contragent=="") {
				$this->reportError("Укажите контрагента","save");
				return 0;
			}				
			if ($this->prihodSumma-($this->orderSumma-$this->discountSumma)<0) {
				$this->reportError("Сумма принятая от клиента меньше чем ВСЕГО К ОПЛАТЕ");
				return 0;
			}
			if ($this->documentTable=="") {
				$this->reportError("Таблица документа не заполнена");
				return 0;
			} else {
				$arr = explode("|",$this->documentTable);
				foreach ($arr as $value) {
					$strings = explode("~",$value);
					foreach ($strings as $str) {
						$str = trim($str);
						if ($str=="") {
							$this->reportError("Таблица документа заполнена не верно. Есть незаполненные ячейки");
							return 0;
						}
					}
				}
			}
			if ($this->registered) {
				$this->smsSent = 1;
				$this->emailSent = 1;
			}
			
		}
				
		return parent::checkData();			
	}
	
	function afterSave($out=true) {
		parent::afterSave($out);
		
		if (is_object($this->contragent))
			$this->contragent = $this->contragent->getId();
				
		if (is_object($this->old_contragent))
			$this->old_contragent = $this->old_contragent->getId();
	
		
		if ($this->contragent!="") {			
			if ($this->old_contragent!="") {
				$this->removeLinks(array($this->old_contragent));
			}
			$this->setLinks(array($this->contragent));
		}		
	}
	
	function getArgs() {

		global $Objects;
			
		$this->allDiscountSumma = $this->getContragentDiscount($this->docDate-1);
		if ($this->name=="") {
			$this->discountSumma = $this->allDiscountSumma;
		}
		
		$this->documentTableId = "DocumentQuickSaleTable_".$this->module_id."_".$this->name;
		$this->documentTableCode = '$object->documentQuickSale="'.$this->getId().'";$object->parent_object_id="'.$this->getId().'";';		
		$this->orderSummaStr = $this->orderSumma;
		$this->discountSummaStr = $this->discountSumma;
		$this->totalSummaStr = $this->orderSumma-$this->discountSumma;
		if ($this->totalSummaStr<0)
			$this->totalSummaStr = 0;
		$this->backSummaStr = $this->prihodSumma-$this->totalSumma;
		if ($this->backSummaStr<0)
			$this->backSummaStr=0;
		if (!is_object($this->contragent) and $this->contragent!="")
			$this->contragent = $Objects->get($this->contragent);
								
		if ($this->window_id=="")
			$this->window_id = @$_GET["window_id"];
		
		$this->entityImages = json_encode(array());
				
		return parent::getArgs();
	}
			
	function createByOwnerObject() {
		$doc = $this->ownerObject;
		$class = get_class($doc);		
		if ($class=="ReferenceContragents") {
			$this->contragent = $doc;
		}
		if ($class=="DocumentContragentRequest") {
			$this->contragent = $doc->contragent;
		}
		if ($class=="DocumentContract") {
			$this->contragent = $doc->contragent;
		}
		if ($class=="DocumentOrder") {
			$this->contragent = $doc->contragent;
		}
	}
	
	function getContragentDiscount($date=0) {
		global $Objects;
		$summa = 0;
		if ($this->contragent!="") {
			if (!is_object($this->contragent)) {
				$this->contragent = $Objects->get($this->contragent);
			}
			$this->contragent->loaded = false;
			$this->contragent->load();
			if ($date==0)
				$date = $this->docDate-1;
			$reg = $Objects->get("RegistryDiscountCards_".$this->module_id."_reg");
			$movements = $reg->getRecords(0,$date,"summa","@discountCardNumber='".$this->contragent->discountCardNumber."'");			
			if (count($movements)==0) {
				if ($this->contragent->referrerNumber!="") {
					if ($this->name=="")
						$query = "SELECT entities FROM fields WHERE @contragent='".str_replace($this->module_id."_","",$this->contragent->getId())."' AND @registered=1 AND @docDate<=".$date." AND @classname='DocumentQuickSale'";
					else
						$query = "SELECT entities FROM fields WHERE @name!=".$this->name." AND @contragent='".str_replace($this->module_id."_","",$this->contragent->getId())."' AND @registered=1 AND @docDate<=".$date." AND @classname='DocumentQuickSale'";
					$result = PDODataAdapter::makeQuery($query, $this->adapter,$this->module_id);
					
					if (count($result)==0) {
						$settings = $Objects->get("BasicCompanyInfo_".$this->module_id."_1");
						$settings->load();
						$summa = $settings->referralDiscount;
					}
				} 
			} else {
				foreach ($movements as $value) {
					$summa += $value["summa"];
				}
			}
		}
		if ($summa<0)
			$summa=0;
		return $summa;				
	}
	
	function getPrintForms() {
		return array("print" => "Приходный кассовый ордер");
	}	
	
	function printDocument($printForm = "") {
		parent::printDocument($printForm);
		if (!$this->loaded)
			$this->load();
		$tpl = "templates/docflow/rozn/printForms/pko.html";
		$blocks = getPrintBlocks(file_get_contents($tpl));
		global $Objects;
		$settings = $Objects->get("BasicCompanyInfo_".$this->module_id."_1");
		$settings->load();
		$arr = array();
		$arr["{firmName}"] = $settings->firmName;
		$arr["{OKPO}"] = $settings->firmOKPO;
		$arr["{buhgalterName}"] = $settings->buhgalterName;
		$arr["{kassirName}"] = $settings->kassirName;
		$arr["{kassaPrihodOsnovanie}"] = $settings->kassaPrihodOsnovanie;
		$arr["{summa}"] = $this->orderSumma-$this->discountSumma;
		$arr["{summaStr}"] = num2str(floatval($arr["{summa}"]));		
		$arr["{summaStr}"] = mb_strtoupper(mb_substr($arr["{summaStr}"],0,1,"UTF8")).mb_substr($arr["{summaStr}"],1,strlen($arr["{summaStr}"])-1,"UTF8");
		
		if (is_object($this->contragent))
			$this->contragent->load();
		$arr["{contragentName}"] = $this->contragent->title;
		$summ = explode(".",$arr["{summa}"]);
		$arr["{summaRub}"] = $summ[0];
		if ($summ[1]=="")
			$summ[1] = "00";
		if (strlen($summ[1])=="1")
			$summ[1] = "0".$summ[1];
		$arr["{summaKop}"] = $summ[1];
		$arr["{summa1}"] = $arr["{summaRub}"]." руб. ".$arr["{summaKop}"]." коп.";
		$arr["{summa}"] = $arr["{summaRub}"].",".$arr["{summaKop}"];
		$arr["{docDate}"] = date("d.m.Y",substr($this->docDate,0,strlen($this->docDate)-3));
								
		$result = parent::printDocument($printForm);		
		$result .= strtr($blocks["header"],$arr);
		
		return $result;
	}	
	
	function getPresentation($full=true) {
		global $Objects;
		if ($this->noPresent)
			return "";
		$this->loaded = false;
		$this->load();
		if ($this->number=="")
			return "";
		$present = "";
		if (is_object($this->contragent)) {
			$this->contragent->noPresent = false;
			$present = " ".$this->contragent->getPresentation(false);
		}
		else {
			$this->contragent = $Objects->get($this->contragent);
			if (is_object($this->contragent)) {
				$this->contragent->noPresent = false;
				$present = " ".$this->contragent->getPresentation(false);
			}				
		}
		if (!$full)
			return $this->classTitle." № ".$this->number." от ".date("d.m.Y H:i:s",substr($this->docDate,0,-3)).$present;
		if ($this->docDate=="")
			$this->docDate= time()."000";
		if ($this->classTitle=="")
			return $this->title." № ".$this->number." от ".date("d.m.Y H:i:s",substr($this->docDate,0,-3)).$present;
		else
			return $this->classTitle." № ".$this->number." от ".date("d.m.Y H:i:s",substr($this->docDate,0,-3)).$present;
	}	
	
	function register() {
		parent::register();
		global $Objects;
		if ($this->contragent!="") {
			if (!is_object($this->contragent)) {
				$this->contragent = $Objects->get($this->contragent);
			}
		}
		$this->contragent->load();
		$reg = $Objects->get("RegistryDiscountCards_".$this->module_id."_reg");
		$summa = $reg->getValueSumma(0,$this->docDate,"summa","@discountCardNumber='".$this->contragent->discountCardNumber."'");

		if ($summa>0) {
			//if ($this->discountSumma>$summa)
				$this->discountSumma = $summa;
			$record = $Objects->get("RegistryDiscountCards_".$this->module_id."_");		
			$record->document = $this;
			$record->regDate = $this->docDate;
			$record->contragent = $this->contragent;
			$record->discountCardNumber = $this->contragent->discountCardNumber;
			$record->summa = $this->discountSumma-$this->discountSumma*2;
			$record->save(true);
		}
		$Objects->remove("RegistryDiscountCards_".$this->module_id."_");
		$settings = $Objects->get("BasicCompanyInfo_".$this->module_id."_1");
		$settings->load();
		$arr = explode(",",$settings->referrerDiscounts);
		$client = $this->contragent;
		foreach ($arr as $discount) {
			$client->loaded = false;
			$client->load();
			if ($client->referrerNumber!="") {
				$query = "SELECT entities FROM fields WHERE @discountCardNumber='".$client->referrerNumber."' AND @classname='ReferenceContragents'";
				$result = PDODataAdapter::makeQuery($query, $this->adapter,$this->module_id);
				$this->setSettings();
				if (count($result)>0) {
					$client = current($result);
					$client->loaded = false;
					$client->load();
					
					$discountSumma = round($this->orderSumma/100*$discount,2);
					if ($discountSumma>0) {
						$client->load();
						$record = $Objects->get("RegistryDiscountCards_".$this->module_id."_");
						$record->document = $this;
						$record->regDate = $this->docDate;
						$record->contragent = $client;
						$record->discountCardNumber = $client->discountCardNumber;
						$record->summa = $discountSumma;
						$record->save(true);
						$Objects->remove("RegistryDiscountCards_".$this->module_id."_");
						if ($client->email!="") {
							$email = $client->email;
							$headers = "From: ".$settings->emailFrom."\n";
								
							$headers.= "MIME-Version: 1.0\n";
							$headers.= "Content-type: text/html; charset=utf-8\n";
							$to = $email;
							$subject = $settings->emailSubject;
							$message = str_replace("{сумма}",$discountSumma,$settings->emailTemplate);
							mail($to,$subject,$sender.$message,$headers);
							//$this->emailSent="1";
						}
						if ($client->mobileNumber!="") {
							$message = str_replace("{сумма}",$discountSumma,$settings->smsTemplate);
							$phone = $client->mobileNumber;
							//$message = mb_convert_encoding($message, "UTF-8");
							$arr = array("{phone}" => $phone, "{message}" => $message);
							$strings = array();							
							
							$strings = @file($this->settings["sendSMSScriptPath"]."tosend");
							$strings[] = $phone.";".$message;
							$result_strings = array();
							foreach ($strings as $string)
							    $result_strings[] = str_replace("\n","",$string);
							$strings = $result_strings;
							file_put_contents($this->settings["sendSMSScriptPath"]."tosend", implode("\n",$strings));
							//$this->smsSent="1";
						}
					}						
				} else {
					break;
				}					
			} else {
				break;
			}
		}
	}	
	
	function getHookProc($number) {
		switch($number) {
			case '10': return "getProductInfo";
		}
		return parent::getHookProc($number);
	}
	
	function getProductInfo($arguments) {
		global $Objects;
		$obj = $Objects->get(@$arguments["id"]);
		if (is_object($obj)) {
			$obj->load();
			if (is_object($obj->dimension)) {
				$obj->dimension->load();
				$dim = $obj->dimension->title;
			}
			else {
				$dim = "";
			}
			echo json_encode(array("title" => $obj->title, "code" => $obj->code, "dimension" => $dim, "cost" => $obj->cost, "NDS" => $obj->NDS));
		} else
			echo json_encode(array());
	}	
}
?>
