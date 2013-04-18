<?php
/**
 * Класс, реализующий регистр. Он с одной стороны является записью регистра, а с
 * другой стороны это список записей регистра.
 *
 * Запись регистра в базовом варианте содержит дату и время регистрации (поле
 * regDate) и ссылку на документ, который создал эту запись (поле document).
 * 
 * Этот класс является абстрактным, поэтому других полей в нем нет. Реальные
 * регистры, которые будут наследоваться от него должны содержать дополнительные
 * поля, называемые измерениями регистра или реквизитами регистра. Если документ
 * проводит свои значения по регистру можно узнать значение определенного
 * реквизита на определенное время. Если у измерения числовое значение, можно
 * получить сумму значений этого показателя за определенный промежуток времени.
 * У каждого измерения два поля: тип измерения и значение измерения. Тип 
 * измерения может быть строкой или ссылкой на элемент справочника. Значение
 * измерения может быть любым. Обычно название поля значения измерения выглядит
 * как <имяПоляТипаИзмерения>Value
 *
 * Числовые значения измерений записи регистра могут быть как положительными,
 * так и отрицательными. Если значения отрицательны, то это считается расходом
 * по регистру. Сумма по регистру это фактически остаток на указанное время.
 *
 * При создании записи в регистре проверяется, есть ли уже запись с такими
 * же реквизитами и если есть, предыдущая запись удаляется. При этом
 * сравнении учитываются только поля, которые являются измерениями или реквизитами. Сочетание
 * значений измерений должно быть уникальным. Список полей объекта, являющихся
 * измерениями перечислен в переменной dimensions через запятую. Список реквизитов
 * перечислен в переменной recvs через запятую
 *
 * При удалении регистратора должны удаляться все записи в регистре,
 * относящиеся к этому регистратору. Для этой цели в классе есть
 * статический метод registratorDeleted(<идентификатор регистратора>).
 * Его должен вызывать документ-регистратор прежде чем удалиться. Этот
 * метод удаляет записи с этим регистратором во всех регистрах.
 *
 * Методы регистра:
 * 
 * getRecord(regDate,conditions) - получить запись регистра на указанную дату
 * getValue(regDate,fieldName,conditions) - получить значение в регистре на указанную дату
 * getFirstValue(regDate,fieldName,conditions) - получить первое значение поля в регистре
 * getLastValue(regDate,fieldName,conditions) - получить последнее значение поля в регистре
 * getValues(regDate,fieldNames,conditions) - получить список значений полей регистра на указанную дату
 * getFirstValues(fieldNames,conditions) - получить список первых значений полей регистра
 * getLastValues(fieldNames,conditions) - получить список последних значений полей регистра
 * getValueSumma(periodStart,periodEnd,fieldName,conditions) - получить сумму значений fieldName, попадающие в указанный период и соответствующие указанным условиям.
 * 																Условия должны включать в себя значения измерений регистра. Используются стандартные условия EQL-запросов. 
 * getValuesSummas(periodStart,periodEnd,fieldNames,conditions) - получить список сумм указанных полей
 * getRecords(periodStart,periodEnd,fields,conditions) - получить список записей регистра, соответствующий указанному периоду и указанным условиям, причем в качестве дат можно указывать
 *                                                       нули, в этом случае они не будут учитываться. Также указывается список полей, которые нужно получить. Если указано "*" производит полную
 *                                                       загрузку сущностей. Также можно указать count. В этом случае возвращается количество записей в регистре. Если указан список полей,
 *														 все равно создаются объекты и заполняются значениями этих полей.
 * removeRecords(conditions) - удаляет записи регистра, соответствующие указанным условиям в формате EQL
 * removeByRegistrator(document) - удаляет все записи регистра, принадлежащие указанному регистратору
 * remove() - удаляет текущую запись регистра
 * registratorDeleted(document) - статический метод, реагирующий на удаление документа. Удаляет все записи во всех регистрах, принадлежащие этому документу.
 * 
 * @author andrey
 */
class Registry extends WABEntity {
	
	public $persistedFields = array();
	
	public $printProfiles = array();
	public $userPrintProfiles = array();
	public $printProfile = array();
		
    function construct($params) {
        $old_params = $params;
        global $Objects;
        if (count($params)>2)
            $this->module_id = array_shift($params)."_".array_shift($params);
        else
            $this->module_id = "";
        if (count($params)>0)
            $this->name_set = true;
        $this->name = implode("_",$params);
        $this->old_name = $this->name;
        $app = $Objects->get($this->module_id);
        if (!$app->loaded)
            $app->load();
        $this->app = $app;
        
        $this->adapter = $app->getAdapter($this);
        $this->className = get_class($this);
        $this->defaultClassName = get_class($this);
        $this->childClass = $this->className;
        $this->additionalFields = "name";
        $this->parentEntity = get_class($this)."_".$this->module_id."_";
        $this->objectid = str_replace("_","",$this->getId());
        $this->itemsPerPage = "";
        $this->currentPage = "";
        $this->condition = "";
        $this->hierarchy = "false";
        $this->fieldList = "regDate Дата~document.number Документ";
        $this->sortOrder = "regDate DESC";
        $this->periodStart = 0;
        $this->periodEnd = 0;
        $this->dateStart = time()."000";
        $this->dateEnd = time()."000";
        $this->showHeader = true;
        $this->conditionsString = "";
        $params = $old_params;
        $this->persistedFields = array(
        		                 "document" => array("type" => "entity",
        		                 		              "params" => array("type" => "entity",        		                 		              		             
        		                 		              		             "additionalFields" => "name",
        		                 		              		             "show_float_div" => "true",
        		                 		              		             "classTitle" => "Регистратор",
        		                 		              		             "editorType" => "WABWindow",
        		                 		              		             "title" => "Регистратор",
                                                       		             "fieldList" => "title Наименование",
																	     "sortOrder" => "regDate ASC",
        		                 		              		             "width" => "100%",
        		                 		              		             "adapterId" => "DocFlowDataAdapter_".$this->module_id."_1",
        		                 		              		             "parentEntity" => "Document_".$this->module_id."_"
        		                                       )
        		                 ),
        		                 "regDate" => array("type" => "integer",
        		                 		             "params" => array("type" => "date",
        		                 		             	                "title" => "Дата регистрации",
        		                 		             					"show_time" => "false"
        		                                       )
        		                 )
        );
        parent::construct($params);
		$this->template = "renderForm";
        $this->clientClass = "Registry";
        $this->parentClientClasses = "Entity";   
        $this->defaultPrintProfile = "Основной";
        $this->defaultReport = "Основной";     
        $app = $Objects->get("Application");
        if (!$app->initiated)
        	$app->initModules();
        $this->gapp = $app;
        $this->skinPath = $app->skinPath;
        $this->icon = $this->skinPath."images/docflow/registry.png";
        $this->handler = "scripts/handlers/docflow/core/Registry.js";
    	$this->argums = "/var/WAB2/users/".$this->gapp->User."/arguments/".str_replace("_","",$this->getId());
    }


    function getPresentation() {
		if ($this->noPresent)
			return "";
		if (!$this->loaded)
			$this->load();
    	if ($this->regDate=="")
            $this->regDate= time()."000";
        return "Запись регистра ".$this->title." ".date("d.m.Y H:i:s",$this->regDate);
    }
    
    function getArgs() {
    	if ($this->currentReport=="")
    		$this->currentReport = $this->defaultReport;
    	 
    	if ($this->currentPrintProfile=="")
    		$this->currentPrintProfile = $this->defaultPrintProfile;
        $this->argums = "/var/WAB2/users/".$this->gapp->User."/arguments/".str_replace("_","",$this->getId());
    	$reports = array_keys($this->printProfiles);
    	$this->reportsList = implode("~",$reports)."|".implode("~",$reports);
    	
    	$profiles = array_keys($this->printProfiles[$this->currentReport]);
    	unset($profiles[array_search("settings",$profiles)]);
    	$this->profilesList = implode("~",$profiles)."|".implode("~",$profiles);
    	$result = parent::getArgs();
    	return $result;
    }
    
    function getHookProc($number) {
    	switch($number) {
    		case '3': return "getProfileData";
    		case '4': return "getProfilesList";
    		case '5': return "showControls";
    		case '6': return "setProfile";
    		case '7': return "setControls";
    		case '8': return "getSettingsDialog";
    		case '9': return "saveSettings";
    		case '10': return "removeProfile";
    	}
    	return parent::getHookProc($number);
    }
    
    function getSettingsDialog($arguments) {
    	echo @$this->printProfiles[$arguments["report"]]["settings"]["settingsClass"];
    }
    
    function showControls($arguments) {
    	$this->template = "templates/docflow/core/RegistryReportWindow.html";
    	$this->show();
    }
    
    function setControls($arguments) {
    	$this->template = "templates/docflow/core/RegistryReportWindow.html";
    }
    
    
    function setProfile($arguments) {
    	$fname = $this->argums;
    	file_put_contents($fname,serialize(array("periodStart" => $arguments["periodStart"], "periodEnd" => $arguments["periodEnd"], "currentReport" => $arguments["currentReport"], "printProfile" => objectToArray($arguments["printProfile"]))));    	     	
    }
    
    function saveSettings($arguments) {
    	$object = $this;
    	if (file_exists("/var/WAB2/users/".$this->gapp->User."/settings/".$this->getId())) {
			eval(file_get_contents("/var/WAB2/users/".$this->gapp->User."/settings/".$this->getId()));
		}		
    	$this->userPrintProfiles[$arguments["report"]][$arguments["profile"]] = objectToArray($arguments["printProfile"]);
		$str .= '$object->userPrintProfiles = '.getArrayCode($this->userPrintProfiles).";\n";
		file_put_contents("/var/WAB2/users/".$this->gapp->User."/settings/".$this->getId(),$str);
    }

    function removeProfile($arguments) {
    	$object = $this;
    	if (file_exists("/var/WAB2/users/".$this->gapp->User."/settings/".$this->getId())) {
    		eval(file_get_contents("/var/WAB2/users/".$this->gapp->User."/settings/".$this->getId()));
    	}
    	unset($this->userPrintProfiles[$arguments["report"]][$arguments["profile"]]);
    	$str .= '$object->userPrintProfiles = '.getArrayCode($this->userPrintProfiles).";\n";
    	file_put_contents("/var/WAB2/users/".$this->gapp->User."/settings/".$this->getId(),$str);
    }
    
    function getProfileData($arguments) {
    	if (isset($arguments["profile"])) {
    		if (!$this->loaded)
    			$this->load();    		    		
    		if (count($this->printProfile)==0)
    			$this->printProfile = $this->printProfiles[$arguments["report"]][$arguments["profile"]];    		
    		echo json_encode($this->printProfile);    		
    	}    		
    }
    
    function getProfilesList($arguments) {
    	if (isset($arguments["report"])) {
    		if (!$this->loaded)
    			$this->load();    		
   			$profiles = array_keys($this->printProfiles[$arguments["report"]]);
   			unset($profiles[array_search("settings",$profiles)]);
   			echo implode("~",$profiles);
    	}    		
    }
    
    function load($em="",$dbEntity="",$force=false) {
    	parent::load($em,$dbEntity,$force);
    	foreach ($this->userPrintProfiles as $reportKey=>$reportValue) {
    		foreach ($reportValue as $profileKey=>$profileValue) {
    			$this->printProfiles[$reportKey][$profileKey] = $profileValue;
    		}    		
    	}
    }
    
    function renderForm() {
    	
    	global $Objects;
    	    	
    	if ($this->currentReport=="")
    		$this->currentReport = $this->defaultReport;
    	
    	if ($this->currentPrintProfile=="")
    		$this->currentPrintProfile = $this->defaultPrintProfile;
    	
    	$func = "renderForm".$this->currentReport;
    	
    	return $this->$func();
    }
    
    function renderFormОсновной() {
    	$tableBlocks = getPrintBlocks(file_get_contents("templates/docflow/core/printForms/RegistryReportTable.html"));
    	$out = "";
    	if ($this->showHeader)
    		$out .= $tableBlocks["headerText"];
    	$out .= $tableBlocks["header"];
    	$fields = $this->explodePersistedFields($this->persistedFields);
    	$out .= $tableBlocks["str"];
    	if (count($this->printProfile)==0)
    		$this->printProfile = $this->printProfiles[$this->currentReport][$this->currentPrintProfile];
    	
    	$this->printProfile = objectToArray($this->printProfile);
    	$this->printProfile = (array)$this->printProfile;
    	$printFields = (array)$this->printProfile["printFields"];
    	$printFieldNames = array_keys($printFields);
    	$prnFieldNames = $printFieldNames;
    	$groupFields = (array)$this->printProfile["groups"];
    	$groupKeys = array_keys($groupFields);
    	$sortArray = array();
    	$totals = array();
    	$fullTotals = array();
    	foreach ($groupFields as $key=>$value) {
    		if (array_search($key, $printFieldNames)===FALSE)
    			$printFieldNames[] = $key;
    		$fieldSettings = $fields[$key];
    		$sortArray[] = $key." ASC";
    	
    	}
    	$printFieldsStr = implode(",",$printFieldNames);
    	foreach ($printFields as $field => $settings) {
    		$settings = (array)$settings;
    		$fieldSettings = $fields[$field]["params"];
    		$arr = array();
    		$arr["{fieldName}"] = $fieldSettings["title"];
    		if (isset($settings["size"]))
    			$arr["{cellWidth}"] = "width=".$settings["size"];
    		$out .= strtr($tableBlocks["titleCell"],$arr);
    	}
    	$out .= $tableBlocks["endStr"];
    	$sortFields = (array)$this->printProfile["sortFields"];
    	foreach($sortFields as $key=>$value) {
    		$fieldSettings = $fields[$key];
    		if ($fieldSettings["type"]=="entity")
    			$key .= ".title";
    		if (array_search($key." ".$value,$sortArray)===FALSE)
    			$sortArray[] = $key." ".$value;
    	}
    	$sortStr = implode(",",$sortArray);
    	$condArray = array();
    	if ($this->conditions!="")
    		$condFields = $this->conditions;
    	else
    		$condFields = (array)$this->printProfile["conditions"];
    	foreach ($condFields as $key=>$value) {
    		$value = (array)$value;
    		if (isset($value["value1"])) {
    			$val = $value["value1"]." AND ".$value["value2"];
    		}
    		else if (is_array($value["value"])) {
    			$val = array();
    			foreach ($value["value"] as $value1) {
    				$val[] = "'".str_replace($this->module_id."_","",$value1)."'";
    			}
    			$val = "(".implode(",",$val).")";
    		} else if (isset($value["value"])) {
    			$val = "'".str_replace($this->module_id."_","",$value["value"])."'";
    		}
    		else {
    			$val = "";
    		}
    		$condArray[] = "@".str_replace(".",".@",$key).$value["type"].$val;
    	}
    	$condStr = implode(" AND ",$condArray);
    	if ($this->periodStart!=0)
    		$this->periodStart = getBeginOfDay($this->periodStart/1000)."000";
    	if ($this->periodEnd!=0)
    		$this->periodEnd = getEndOfDay($this->periodEnd/1000)."000";
    	$rows = $this->getRecords($this->periodStart, $this->periodEnd,$printFieldsStr,$condStr,$sortStr);
    	
    	$prevGroups = array();
    	$wasGroup = false;
    	$strings = array();
    	if (is_array($rows) and count($rows)>0) {
    		if (isset($this->printProfile["totals"]))
    			$this->printProfile["totals"] = (array)$this->printProfile["totals"];
    		if (isset($this->printProfile["totals"]["totals"])) {
    			$valueTotals = $this->printProfile["totals"]["totals"];
    			foreach($rows as $row) {
    				$index = "";
    				$indArray = array();
    				foreach ($groupFields as $key=>$value) {
    					$field = $key;
    					$fieldSettings = $fields[$field];
    					if ($fieldSettings["type"] == "entity") {
    						$obj = $row[$field];
    						if (is_object($obj)) {
    							$obj->loaded = false;
    							$obj->load();
    							$obj->noPresent = false;
    							$fieldValue = $obj->getPresentation(false);
    	
    						} else
    							$fieldValue = "";
    					}
    					else if ($fieldSettings["params"]["type"] == "date") {
    						if (isset($row[$field]))
    							$fieldValue = date("d.m.Y H:i:s",$row[$field]/1000);
    						else
    							$fieldValue = "";
    					}
    					else {
    						if (isset($row[$field]))
    							$fieldValue = $row[$field];
    						else
    							$fieldValue = "";
    					}
    					$indArray[] = $fieldValue;
    				}
    				$index = implode("_",$indArray);
    				foreach ($valueTotals as $totalValue) {
    					if (!isset($totals[$index][$totalValue]))
    						$totals[$index][$totalValue] = 0;
    					$field = $totalValue;
    					$fieldSettings = $fields[$field];
    					if ($fieldSettings["type"] == "entity") {
    						$obj = $row[$field];
    						if (is_object($obj)) {
    							$obj->loaded = false;
    							$obj->load();
    							$obj->noPresent = false;
    							$fieldValue = $obj->getPresentation(false);
    	
    						} else
    							$fieldValue = "";
    					}
    					else if ($fieldSettings["params"]["type"] == "date") {
    						if (isset($row[$field]))
    							$fieldValue = date("d.m.Y H:i:s",$row[$field]/1000);
    						else
    							$fieldValue = "";
    					}
    					else {
    						if (isset($row[$field]))
    							$fieldValue = $row[$field];
    						else
    							$fieldValue = "";
    					}
    					$totals[$index][$totalValue] += $fieldValue;
    					$ind = array();
    					for ($i=0;$i<count($indArray)-1;$i++) {
    						$ind[] = $indArray[$i];
    						if (!isset($totals[implode("_",$ind)][$totalValue]))
    							$totals[implode("_",$ind)][$totalValue] = 0;
    						$totals[implode("_",$ind)][$totalValue] += $fieldValue;
    					}
    					if (!isset($fullTotals[$totalValue]))
    						$fullTotals[$totalValue] = 0;
    					$fullTotals[$totalValue] += $fieldValue;
    				}
    			}
    		}
    		foreach($rows as $row) {
    			$out .= $tableBlocks["str"];
    			$fullGroup = array();
    			foreach ($groupKeys as $group) {
    				$fieldSettings = $fields[$group];
    				if ($fieldSettings["type"] == "entity") {
    					$obj = $row[$group];
    					if (is_object($obj)) {
    						$obj->loaded = false;
    						$obj->load();
    						$obj->noPresent = false;
    						$fieldValue = $obj->getPresentation(false);
    						$addon = "onmouseover='this.style.cursor=\"pointer\";this.style.backgroundColor=\"#FFFFCC\";' onmouseout='this.style.backgroundColor=\"#FFFFFF\";' onclick='getWindowManager().show_window(\"Window_".str_replace("_","",$obj->getId())."\",\"".$obj->getId()."\",null,\"".$this->getId()."\",\"".$this->getId()."\");'";
    							
    					} else {
    						$fieldValue = "";
    						$addon = "";
    					}
    				}
    				else if ($fieldSettings["params"]["type"] == "date") {
    					if (isset($row[$field]))
    						$fieldValue = date("d.m.Y H:i:s",$row[$field]/1000);
    					else
    						$fieldValue = "";
    				}
    				else
    				{
    					if (isset($row[$field]))
    						$fieldValue = $row[$field];
    					else
    						$fieldValue = "";
    				}
    				$fullGroup[] = @$fieldValue;
    				if (@$prevGroups[$group]!=@$row[$group]) {
    					$fieldSettings = $fields[$group];
    					if ($fieldSettings["type"] == "entity") {
    						$obj = $row[$group];
    						if (is_object($obj)) {
    							$obj->loaded = false;
    							$obj->load();
    							$obj->noPresent = false;
    							$fieldValue = $obj->getPresentation(false);
    							$addon = "onmouseover='this.prevStyle = this.style.backgroundColor;this.style.cursor=\"pointer\";this.style.backgroundColor=\"#FFFFCC\";' onmouseout='this.style.backgroundColor=this.prevStyle;' onclick='getWindowManager().show_window(\"Window_".str_replace("_","",$obj->getId())."\",\"".$obj->getId()."\",null,\"".$this->getId()."\",\"".$this->getId()."\");'";
    	
    						} else {
    							$fieldValue = "";
    							$addon = "";
    						}
    					}
    					else if ($fieldSettings["params"]["type"] == "date") {
    						if (isset($row[$field]))
    							$fieldValue = date("d.m.Y H:i:s",$row[$field]/1000);
    						else
    							$fieldValue = "";
    					}
    					else
    					{
    						if (isset($row[$field]))
    							$fieldValue = $row[$field];
    						else
    							$fieldValue = "";
    					}
    					$fontFace = "Arial";
    					$fontSize = "15";
    					$fontWeight = "bold";
    					$fontColor = "#000000";
    					$bgColor = "#CCCCCC";
    					$groupFields[$group] = (array)$groupFields[$group];
    					if (isset($groupFields[$group]["fontFace"]))
    						$fontFace = $groupFields[$group]["fontFace"];
    					if (isset($groupFields[$group]["fontSize"]))
    						$fontSize = $groupFields[$group]["fontSize"];
    					if (isset($groupFields[$group]["fontWeight"]))
    						$fontWeight = $groupFields[$group]["fontWeight"];
    					if (isset($groupFields[$group]["fontColor"]))
    						$fontColor = $groupFields[$group]["fontColor"];
    					if (isset($groupFields[$group]["bgColor"]))
    						$bgColor = $groupFields[$group]["bgColor"];
    	
    					$attrs = array("{addon}" => $addon, "{fieldValue}" => $fieldValue, "{fontFace}" => $fontFace, "{fontSize}" => $fontSize, "{fontWeight}" => $fontWeight, "{fontColor}" => $fontColor, "{bgColor}" => $bgColor, "{cellsCount}" => "1");
    					$addon = "";
    					$out .= strtr($tableBlocks["groupCell"],$attrs);
    					$indArray = array();
    					foreach ($groupFields as $key=>$value) {
    						$field = $key;
    						$fieldSettings = $fields[$field];
    						if ($fieldSettings["type"] == "entity") {
    							$obj = $row[$field];
    							if (is_object($obj)) {
    								$obj->loaded = false;
    								$obj->load();
    								$obj->noPresent = false;
    								$fieldValue = $obj->getPresentation(false);
    								$addon = "onmouseover='this.prevStyle = this.style.backgroundColor;this.style.cursor=\"pointer\";this.style.backgroundColor=\"#FFFFCC\";' onmouseout='this.style.backgroundColor=this.prevStyle;' onclick='getWindowManager().show_window(\"Window_".str_replace("_","",$obj->getId())."\",\"".$obj->getId()."\",null,\"".$this->getId()."\",\"".$this->getId()."\");'";
    							} else {
    								$fieldValue = "";
    								$addon = "";
    							}
    						}
    						else if ($fieldSettings["params"]["type"] == "date") {
    							if (isset($row[$field]))
    								$fieldValue = date("d.m.Y H:i:s",$row[$field]/1000);
    							else
    								$fieldValue = "";
    						}
    						else {
    							if (isset($row[$field]))
    								$fieldValue = $row[$field];
    							else
    								$fieldValue = "";
    						}
    						$indArray[] = $fieldValue;
    					}
    					$index = implode("_",$fullGroup);
    					for ($i=1;$i<count($prnFieldNames);$i++) {
    						$pkey = $prnFieldNames[$i];
    						if (isset($totals[$index][$pkey]))
    							$fieldValue = $totals[$index][$pkey];
    						else
    							$fieldValue = "";
    	
    						$fontFace = "Arial";
    						$fontSize = "15";
    						$fontWeight = "bold";
    						$fontColor = "#000000";
    						$bgColor = "#CCCCCC";
    						$groupFields[$group] = (array)$groupFields[$group];
    						if (isset($groupFields[$group]["fontFace"]))
    							$fontFace = $groupFields[$group]["fontFace"];
    						if (isset($groupFields[$group]["fontSize"]))
    							$fontSize = $groupFields[$group]["fontSize"];
    						if (isset($groupFields[$group]["fontWeight"]))
    							$fontWeight = $groupFields[$group]["fontWeight"];
    						if (isset($groupFields[$group]["fontColor"]))
    							$fontColor = $groupFields[$group]["fontColor"];
    						if (isset($groupFields[$group]["bgColor"]))
    							$bgColor = $groupFields[$group]["bgColor"];
    	
    						$attrs = array("{addon}" => $addon, "{fieldValue}" => $fieldValue, "{fontFace}" => $fontFace, "{fontSize}" => $fontSize, "{fontWeight}" => $fontWeight, "{fontColor}" => $fontColor, "{bgColor}" => $bgColor, "{cellsCount}" => "1");
    						$addon = "";
    						$out .= strtr($tableBlocks["groupCell"],$attrs);
    					}
    					$out .= $tableBlocks["endStr"];
    					$prevGroups[$group] = $row[$group];
    					$prevGroup = $group;
    				}
    			}
    			foreach ($printFields as $field=>$settings) {
    				$fieldSettings = $fields[$field];
    				if ($fieldSettings["type"] == "entity") {
    					$obj = $row[$field];
    					if (is_object($obj)) {
    						$obj->loaded = false;
    						$obj->load();
    						$obj->noPresent = false;
    						$fieldValue = $obj->getPresentation(false);
    						$addon = "onmouseover='this.style.cursor=\"pointer\";this.style.backgroundColor=\"#FFFFCC\";' onmouseout='this.style.backgroundColor=\"#FFFFFF\";' onclick='getWindowManager().show_window(\"Window_".str_replace("_","",$obj->getId())."\",\"".$obj->getId()."\",null,\"".$this->getId()."\",\"".$this->getId()."\");'";
    					} else {
    						$fieldValue = "";
    						$addon = "";
    					}
    				}
    				else if ($fieldSettings["params"]["type"] == "date") {
    					if (isset($row[$field]))
    						$fieldValue = date("d.m.Y H:i:s",$row[$field]/1000);
    					else
    						$fieldValue = "";
    				}
    				else {
    					if (isset($row[$field]))
    						$fieldValue = $row[$field];
    					else
    						$fieldValue = "";
    				}
    				$arr = array();
    				$arr["{fieldValue}"] = $fieldValue;
    				$arr["{addon}"] = $addon;
    				$addon = "";
    				$out .= strtr($tableBlocks["cell"],$arr);
    			}
    			$wasGroup = false;
    			$out .= $tableBlocks["endStr"];
    		}
    	}
    	if (count($fullTotals)>0) {
    		$totalProps = (array)$this->printProfile["totals"];
    		$out .= $tableBlocks["str"];
    		$titled = false;
    		foreach ($printFields as $key=>$value) {
    			$fontFace = "Arial";
    			$fontSize = "15";
    			$fontWeight = "bold";
    			$fontColor = "#000000";
    			$bgColor = "#CCCCCC";
    			 
    			if (isset($totalProps["fontFace"]))
    				$fontFace = $totalProps["fontFace"];
    			if (isset($totalProps["fontSize"]))
    				$fontSize = $totalProps["fontSize"];
    			if (isset($totalProps["fontWeight"]))
    				$fontWeight = $totalProps["fontWeight"];
    			if (isset($totalProps["fontColor"]))
    				$fontColor = $totalProps["fontColor"];
    			if (isset($totalProps["bgColor"]))
    				$bgColor = $totalProps["bgColor"];
    			if (isset($fullTotals[$key]))
    				$fieldValue = $fullTotals[$key];
    			else
    				$fieldValue = "";
    			if (!$titled) {
    				$fieldValue = "ИТОГИ:";
    				$titled = true;
    			}
    			$attrs = array("{fieldValue}" => $fieldValue, "{fontFace}" => $fontFace, "{fontSize}" => $fontSize, "{fontWeight}" => $fontWeight, "{fontColor}" => $fontColor, "{bgColor}" => $bgColor, "{cellsCount}" => "1");
    			$out .= strtr($tableBlocks["groupCell"],$attrs);
    		}
    		$out .= $tableBlocks["endStr"];
    	}
    	$out .= $tableBlocks["footer"];
    	unlink($this->argums);
    	return $out;    	 
    }

    function getId() {
        if ($this->name=="")
            $this->name = $this->entityId;
        if ($this->module_id != "")
            return get_class($this)."_".$this->module_id."_".$this->name;
        else
            return get_class($this)."_".$this->name;
    }

    function afterSave($out=true) {
        global $Objects;
        $to_save = false;
        if ($this->name=="") {
            $this->name = $this->entityId;
            $to_save = true;
        }
        if ($to_save)
            $this->adapter->save();
    }

    function afterInit() {
        return true;
    }

    // Функция получает список записей регистра, соответствующих
    // указанным условиям. Возвращаются именно объекты, в которых 
    // заполнены поля, указанные в списке или количество записей, если
    // в качестве полей указано слово count.
    function getRecords($periodStart,$periodEnd,$fields="",$conditions="",$sort="") {
		
		// ФОРМИРУЕМ ТЕКСТ УСЛОВИЯ ЗАПРОСА
		// Условия для периода
		$period="";
		if ($periodStart!=0)
			$period = "@regDate>='".$periodStart."'";
		if ($periodEnd!=0)
			if ($period != "")
				$period .= " AND @regDate<='".$periodEnd."'";
			else
				$period = "@regDate<='".$periodEnd."'";
	
		// Остальные условия
		if ($conditions!="") { 
			if ($period!="")
				$conditions = $conditions." AND ".$period;
		}
		else
			$conditions = $period;
		
		if ($conditions!="")
			$conditions = "WHERE ".$conditions." AND @classname='".get_class($this)."'";
		else
			$conditions = "WHERE @classname='".get_class($this)."'";
		
		// ФОРМИРУЕМ СПИСОК ПОЛЕЙ, ВОЗВРАЩАЕМЫХ ЗАПРОСОМ
		if ($fields=="*" or $fields=="")
			$fields="entities";
		
		// ФОРМИРУЕМ ТЕКСТ СОРТИРОВКИ
		if ($sort=="")
			$sort = "ORDER BY regDate ASC";
		else
			$sort = "ORDER BY ".$sort;
		
		// ФОРМИРУЕМ ТЕКСТ ЗАПРОСА
		$query = "SELECT ".$fields." FROM fields ".$conditions." ".$sort;
		
		// ДЕЛАЕМ ЗАПРОС К БД
		$result = PDODataAdapter::makeQuery($query,$this->adapter,$this->module_id);
		// ВОЗВРАЩАЕМ РЕЗУЛЬТАТ
		return $result;
	}
	
	
	// Возвращает значение поля на указанную дату. Если в точности на указанную
	// дату значения нет, то возвращается предыдущее
	function getValue($regDate,$fieldName,$conditions) {
		$result = $this->getRecords(0,$regDate,$fieldName,$conditions,"regDate DESC");
		if (count($result)>0)
			return $result[0][$fieldName];
		else
			return 0;
	}
	
	// Возвращает самое первое значение указанного поля
	function getFirstValue($fieldName,$conditions,$dateStart=0,$dateEnd=0) {
		$result = $this->getRecords($dateStart,$dateEnd,$fieldName,$conditions,"regDate ASC");
		if (count($result)>0)
			return $result[0][$fieldName];
		else
			return 0;
	}
	
	// Возвращает самое последнее значение указанного поля
	function getLastValue($fieldName,$conditions,$dateStart=0,$dateEnd=0) {
		$result = $this->getRecords($dateStart,$dateEnd,$fieldName,$conditions,"regDate DESC");
		if (count($result)>0)
			return $result[0][$fieldName];
		else
			return 0;
	}

	// Возвращает список значений полей. Если в точности на указанную
	// дату значения нет, то возвращается предыдущее
	function getValues($regDate,$fieldNames,$conditions) {
		$result = $this->getRecords(0,$regDate,$fieldNames,$conditions,"regDate DESC");
		if (count($result)>0)
			return $result[0];
		else
			return 0;
	}

	// Возвращает список самых первых значений полей регистра
	function getFirstValues($fieldNames,$conditions,$dateStart=0,$dateEnd=0) {
		$result = $this->getRecords($dateStart,$dateEnd,$fieldNames,$conditions,"regDate ASC");
		if (count($result)>0)
			return $result[0];
		else
			return 0;
	}

	// Возвращает список самых первых значений полей регистра
	function getLastValues($fieldNames,$conditions,$dateStart=0,$dateEnd=0) {
		$result = $this->getRecords($dateStart,$dateEnd,$fieldNames,$conditions,"regDate DESC");
		if (count($result)>0)
			return $result[0];
		else
			return 0;
	}

	// Возвращает запись на указанную дату, соответствующую указанным условиям
	function getRecord($regDate,$conditions) {
		$result = $this->getRecords(0,$regDate,"entities",$conditions,"regDate DESC");
		if (count($result)>0) {
			reset($result);
			return current($result);			
		}
		else
			return 0;
	}
	
	// Возвращает сумму значений измерений указанного регистра за указанный период
	function getValueSumma($periodStart,$periodEnd,$fieldName,$conditions) {
		$result = $this->getRecords($periodStart,$periodEnd,$fieldName,$conditions);
		if (count($result)>0) {
			$summa = 0;			
			foreach ($result as $key=>$value)
					$summa += $value[$fieldName];
			return $summa;
		} else
			return 0;
	}
	
	// Возвращает список сумм значений измерений указанного регистра за указанный период
	function getValuesSummas($periodStart,$periodEnd,$fieldNames,$conditions) {
		$result = $this->getRecords($periodStart,$periodEnd,$fieldName,$conditions);
		if (count($result)>0) {
			$summas = array();
			foreach ($result as $key=>$value) {
				if (!isset($summas[$key]))
					$summas[$key] = 0;
				$summas[$key] += $value;
			}
			return $summas;
		} else
			return 0;
	}
	
	// Удаляет записи регистра, соответствующие указанным условиям
	function removeRecords($conditions) {
		if ($conditions!="")
			$conditions .= "WHERE ".$conditions." AND @classname='".get_class($this)."'";
		else
			$conditions = "WHERE @classname='".get_class($this)."'";
		$query = "SELECT entities FROM fields ".$conditions;
		$res = PDODataAdapter::makeQuery($query,$this->adapter,$this->module_id);
		$arr = array();
		foreach ($res as $key=>$value) 
			$arr[] = $value->name;
		$arr_str = implode(",",$arr);
		@$this->adapter->dbh->exec("DELETE from dbEntity WHERE id IN (".$arr_str.")");
		@$this->adapter->dbh->exec("DELETE from fields WHERE entityId IN (".$arr_str.")");
	}
	
	// Удаляет все записи регистра, сгенерированные указанным документом
	function removeByRegistrator($document) {
		$query = "SELECT entities FROM fields WHERE @document='".get_class($document)."_".$document->name."' AND @classname='".get_class($this)."'";
		$res = PDODataAdapter::makeQuery($query,$this->adapter,$this->module_id);
		$arr = array();
		foreach ($res as $key=>$value) 
			$arr[] = $value->name;
		$arr_str = implode(",",$arr);
		@$this->adapter->dbh->exec("DELETE from dbEntity WHERE id IN (".$arr_str.")");
		@$this->adapter->dbh->exec("DELETE from fields WHERE entityId IN (".$arr_str.")");
	}
	
	// Удаляет все записи в регистрах, созданные для указанного документа
	function registratorDeleted($document) {
		$query = "SELECT entities FROM fields WHERE @document='".get_class($document)."_".$document->name."' AND @classname LIKE '%Registry%'";
		$res = PDODataAdapter::makeQuery($query,$this->adapter,$this->module_id);
		$arr = array();
		foreach ($res as $key=>$value) 
			$arr[] = $value->name;
		$arr_str = implode(",",$arr);
		@$this->adapter->dbh->exec("DELETE from dbEntity WHERE id IN (".$arr_str.")");
		@$this->adapter->dbh->exec("DELETE from fields WHERE entityId IN (".$arr_str.")");
	}
	
	// Проверяет данные перед записью
	function checkData() {
		global $Objects;
		// Если уже есть запись на указанное время с таким набором измерений,
		// удаляем ее
		$conds = array();
		if ($this->dimensions!="") {
			$dims = explode(",",$this->dimensions);
			foreach ($dims as $value) {
				if (is_object(@$this->fields[$value]))
					$conds[] = "@".$value.".name='".@$this->fields[$value]->name."'";
				else
					$conds[] = "@".$value."='".@$this->fields[$value]."'";
			}
		}
		if ($this->recvs!="") {
			$dims = explode(",",$this->recvs);
			foreach ($dims as $value) {
				if (is_object(@$this->fields[$value]))
					$conds[] = "@".$value.".name='".@$this->fields[$value]->name."'";
				else
					$conds[] = "@".$value."='".@$this->fields[$value]."'";
			}
		}
		if (count($conds)>0)
			$conds[] = "@name!='".$this->name."'";
		
		$result = $this->getRecord($this->regDate,implode("AND",$conds));
		if ($result!=0)
			$result->remove();
		return parent::checkData();
	}
}
?>