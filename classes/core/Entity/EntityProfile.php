<?php
class EntityProfile extends WABEntity {
	
	public $ruleNames = array();
	public $eventNames = array();
	
	function construct($params) {
		parent::construct($params);
		$this->module_id = array_shift($params)."_".array_shift($params);
		$this->role = array_shift($params);
		$this->entityClass = $params[0];
		if (isset($params[1]) and @$params[1]!="")			
			$this->profile = implode("_",$params);
		else
			$this->profile = $this->entityClass;
		if (count($params)==1)
			$this->entityId = "";
		else
			$this->entityId = str_replace($this->entityClass."_","",$this->profile);
		$this->template = "renderForm";
		$this->handler = "scripts/handlers/core/EntityProfile.js";
		if ($this->module_id!="") {
			$this->tabset_id = "WebItemTabset_".$this->module_id."_".str_replace("_","",$this->getId())."Tabset";
			$this->tabsetName = $this->tabset_id;
			$this->fieldsTabset_id = "WebItemTabset_".$this->module_id."_".str_replace("_","",$this->getId())."FieldsTabset";
			$this->fieldsTabsetName = $this->fieldsTabset_id;
		}
		else {
			$this->tabset_id = "WebItemTabset_".str_replace("_","",$this->getId())."Tabset";
			$this->tabsetName = str_replace("_","",$this->getId())."Tabset";
			$this->fieldsTabset_id = "WebItemTabset_".str_replace("_","",$this->getId())."FieldsTabset";
			$this->fieldsTabsetName = str_replace("_","",$this->getId())."FieldsTabset";
		}		
		$this->tabs_string = "main|Основное|".$this->skinPath."images/spacer.gif;";
		$this->tabs_string.= "fields|Поля|".$this->skinPath."images/spacer.gif;";
		$this->tabs_string.= "events|События|".$this->skinPath."images/spacer.gif";
		$this->active_tab = "main";			
		$this->fieldsTabs_string = "fieldAccess|Права доступа|".$this->skinPath."images/spacer.gif;";
		$this->fieldsTabs_string.= "fieldDefaults|Значения по умолчанию|".$this->skinPath."images/spacer.gif";
		$this->fieldsActive_tab = "fieldAccess";	
		$this->ruleNames = array ("canRead","canEdit","canEditProfile","notifyUsers","canEditLinks","showTagsTab","showNotesTab","showFilesTab","showLinksTab");
		$this->eventNames = array ("*","ENTITY_OPENED","ENTITY_CLOSED","ENTITY_ADDED","ENTITY_CHANGED","ENTITY_DELETED","ENTITY_MARK_DELETED","ENTITY_MARK_UNDELETED");
		$this->width = "600";
		$this->height = "450";
		$this->overrided = "width,height"; 				
	}
	
	function getId() {
		return get_class($this)."_".$this->module_id."_".$this->role."_".$this->profile;
	}
	
	function getPresentation() {
		global $Objects;
		$obj = $Objects->get($this->entityClass."_".$this->module_id."_".$this->entityId);
		if ($this->entityId=="")
			$result = 'Профиль класса объектов "'.$obj->classTitle.'"';
		else
			$result = 'Профиль объекта "'.$obj->getPresentation().'"';
		$result .= ' для роли "'.@$GLOBALS["roles"][$this->role]["title"].' ('.$this->role.')"';
		return $result;
	}
	
	function getArgs() {
		$this->tabsetCode = '$object->module_id="'.$this->module_id.'";
		$object->item="'.$this->getId().'";
		$object->window_id="'.$this->window_id.'";
		$object->tabs_string="'.$this->tabs_string.'";
		$object->active_tab="'.$this->active_tab.'";';
			
		$this->tabsetCode = cleanText($this->tabsetCode);
		
		$this->fieldsTabsetCode = '$object->module_id="'.$this->module_id.'";
		$object->item="'.$this->getId().'";
		$object->window_id="'.$this->window_id.'";
		$object->tabs_string="'.$this->fieldsTabs_string.'";
		$object->active_tab="'.$this->fieldsActive_tab.'";';
			
		$this->fieldsTabsetCode = cleanText($this->fieldsTabsetCode);
		
		global $Objects;
		$entityId = "";
		$obj = $Objects->get($this->entityClass."_".$this->module_id."_".$this->entityId);
		$this->profileId = str_replace("__","_",$obj->profileClass."_".$this->module_id."_".$this->name."_".@$key);
		if ($entityId!="") {
			$this->profileTitle = @$obj->getPresentation();
		} else {
			$this->profileTitle = @$obj->classType." ".$obj->classListTitle;
		}
		
		return parent::getArgs();
	}
	
	function renderForm_1() {
		$blocks = getPrintBlocks(file_get_contents("templates/core/EntityProfile.html"));
		$out = $blocks["mainHeader"];
		$ruleNames = $this->ruleNames;
		$args = array();
		foreach ($ruleNames as $ruleName) {
			$args["{mainRuleName}"] = $ruleName;
			$ruleValue = @$GLOBALS["roles"][$this->role][$this->profile][$ruleName];			
			if (@$ruleValue[0]=="[") {
				$args["{mainRuleCodeName}"] = strtr($ruleValue,array("[" => "","]" =>""));
				$args["{mainRuleValue}"] = "code";	
				$args["{codeDisplayStyle"] = "";			
			} else {
				$args["{mainRuleValue}"] = $ruleValue;
				$args["{mainRuleCodeName}"] = "";
				$args["{codeDisplayStyle}"] = "none";			
			}
			$args["{mainRuleHeader}"] = @$GLOBALS["fields"][$GLOBALS["profileItems"][$ruleName]]["params"]["title"];
			$args["{mainRuleProperties}"] = getMetadataFieldParamsString(@$GLOBALS["profileItems"][$ruleName]);
			$args["{mainRuleType}"] = @$GLOBALS["fields"][$GLOBALS["profileItems"][$ruleName]]["params"]["type"];
			$out .= strtr($blocks["mainRulesRow"],$args);
		}
		$out .= $blocks["mainFooter"];
		return $out;
	}
	
	function renderForm_2() {
		$blocks = getPrintBlocks(file_get_contents("templates/core/EntityProfile.html"));
		$out = $blocks["fieldsHeader"].$blocks["fieldAccessHeader"];
		global $Objects;
		$obj = $Objects->get($this->entityClass."_".$this->module_id."_".$this->entityId);
		if (isset($GLOBALS["models"][get_class($obj)])) {
			$model = $obj->explodePersistedFields($obj->persistedFields);
			foreach ($model as $key=>$value) {
				if ($key=="name" or $key=="metaTitle" or $key=="file" or $key=="collection" or @$value["params"]["hide"]=="true")
					continue;
				$args["{fieldName}"] = $key;
				$args["{fieldTitle}"] = @$value["params"]["title"];
				$fieldValue = @$GLOBALS["roles"][$this->role][$this->profile]["fieldAccess"][$key];
				if ($fieldValue=="")
					$ruleValue="empty";
				if (@$fieldValue[0]=="[") {
					$args["{fieldCodeName}"] = strtr($ruleValue,array("[" => "","]" =>""));
					$args["{fieldValue}"] = "code";
					$args["{fieldCodeDisplayStyle"] = "";
				} else {
					$args["{fieldValue}"] = $fieldValue;
					$args["{fieldCodeName}"] = "";
					$args["{fieldCodeDisplayStyle}"] = "none";
				}
				$args["{fieldProperties}"] = getMetadataFieldParamsString(@$GLOBALS["profileItems"]["fieldAccessRules"]);
				$args["{fieldType}"] = @$GLOBALS["fields"]["fieldAccessRules"]["params"]["type"];
				$out .= strtr($blocks["fieldAccessRow"],$args);				
			}
			
			$args["{fieldName}"] = "*";
			$args["{fieldTitle}"] = "По умолчанию";
			$fieldValue = @$GLOBALS["roles"][$this->role][$this->profile]["fieldAccess"]["*"];
			if ($fieldValue=="")
				$ruleValue="empty";
			if (@$fieldValue[0]=="[") {
				$args["{fieldCodeName}"] = strtr($ruleValue,array("[" => "","]" =>""));
				$args["{fieldValue}"] = "code";
				$args["{fieldCodeDisplayStyle"] = "";
			} else {
				$args["{fieldValue}"] = $fieldValue;
				$args["{fieldCodeName}"] = "";
				$args["{fieldCodeDisplayStyle}"] = "none";
			}
			$args["{fieldProperties}"] = getMetadataFieldParamsString(@$GLOBALS["profileItems"]["fieldAccessRules"]);
			$args["{fieldType}"] = @$GLOBALS["fields"]["fieldAccessRules"]["params"]["type"];
			$out .= strtr($blocks["fieldAccessRow"],$args);		
			$out .= $blocks["fieldAccessFooter"];
			$out .= $blocks["fieldDefaultsHeader"];
			foreach ($model as $key=>$value) {
				if ($key=="name" or $key=="metaTitle" or $key=="file" or $key=="collection" or @$value["params"]["hide"]=="true")
					continue;
				$args["{fieldName}"] = $key;
				$args["{fieldTitle}"] = @$value["params"]["title"];
				$fieldValue = @$GLOBALS["roles"][$this->role][$this->profile]["fieldDefaults"][$key];
				if ($fieldValue=="")
					$ruleValue="empty";
				if (@$fieldValue[0]=="[") {
					$args["{fieldCodeName}"] = strtr($fieldValue,array("[" => "","]" =>""));
					$args["{fieldValue}"] = "";
					$args["{fieldTypeValue}"] = "code";
					$args["{fieldCodeDisplayStyle}"] = "";
					$args["{fieldValueDisplayStyle}"] = "none";
				} else {
					$args["{fieldValue}"] = $fieldValue;
					$args["{fieldCodeName}"] = "";
					$args["{fieldTypeValue}"] = "value";
					$args["{fieldCodeDisplayStyle}"] = "none";
					$args["{fieldValueDisplayStyle}"] = "";
				}
				if ($fieldValue=="")
					$args["{fieldTypeValue}"] = "empty";
				$args["{fieldProperties}"] = json_encode(@$value["params"]);
				$args["{fieldType}"] = @$value["params"]["type"];
				$args["{fieldTypeProperties}"] = getMetadataFieldParamsString(@$GLOBALS["profileItems"]["fieldDefaultsRules"]);
				$args["{fieldTypeType}"] = @$GLOBALS["fields"]["fieldDefaultsRules"]["params"]["type"];
				$out .= strtr($blocks["fieldDefaultsRow"],$args);			
			}
			$out .= $blocks["fieldDefaultsFooter"];				
		}			
		return $out;		
	}
	
	function renderForm_events() {
		$blocks = getPrintBlocks(file_get_contents("templates/core/EntityProfile.html"));
		$out = $blocks["eventsHeader"];
		$ruleNames = $this->eventNames;
		$args = array();
		foreach ($ruleNames as $ruleName) {
			$args["{eventName}"] = $ruleName;
			$ruleValue = @$GLOBALS["roles"][$this->role][$this->profile]["events"][$ruleName]["infoPanel"];
			$args["{eventValuePanel}"] = $ruleValue;
			$ruleValue = @$GLOBALS["roles"][$this->role][$this->profile]["events"][$ruleName]["mail"];
			$args["{eventValueMail}"] = $ruleValue;
			$args["{eventHeader}"] = @$GLOBALS["events"][$ruleName]["title"];
			$args["{eventProperties}"] = getMetadataFieldParamsString(@$GLOBALS["profileItems"]["eventRuleField"]);
			$args["{eventType}"] = @$GLOBALS["fields"][$GLOBALS["profileItems"]["eventRuleField"]]["params"]["type"];
			$out .= strtr($blocks["eventsRow"],$args);
		}
		$out .= $blocks["eventsFooter"];
		return $out;
	}	
	
	function renderForm() {
		$blocks = getPrintBlocks(file_get_contents("templates/core/EntityProfile.html"));
		$out = $blocks["header"];
		$methods = get_class_methods(get_class($this));
		natsort($methods);
		foreach ($methods as $method) {
			if (stripos($method,"renderForm_")!==FALSE)
				$out .= $this->$method();
		}		
		$out .= $blocks["footer"];
		return $out;
	}
	
	function load() {
		
	}
	
	function getHookProc($number) {
		switch ($number) {
			case "3": return "saveProfile";
		}
		return parent::getHookProc($number);
	}
	
	function saveProfile_1($arguments) {
		foreach ($this->ruleNames as $ruleName) {
			if (isset($arguments[$ruleName])) {
				$value = $arguments[$ruleName];
				if ($value=="empty" or $value=="")
					unset($GLOBALS["roles"][$this->role][$this->profile][$ruleName]);
				else if ($value=="code")
					$GLOBALS["roles"][$this->role][$this->profile][$ruleName] = "[".@$arguments[$ruleName."Code"]."]";
				else 
					$GLOBALS["roles"][$this->role][$this->profile][$ruleName] = @$arguments[$ruleName];
			}
		}
	}
	
	function saveProfile_2($arguments) {
		foreach ($arguments as $fieldRule=>$value) {
			if (stripos($fieldRule,"fieldAccess_")!==FALSE && stripos($fieldRule,"Code")===FALSE) {
				$fieldName = str_replace("fieldAccess_","",$fieldRule);
				if ($value=="empty")
					unset($GLOBALS["roles"][$this->role][$this->profile]["fieldAccess"][$fieldName]);
				else if ($value=="code")
					$GLOBALS["roles"][$this->role][$this->profile]["fieldAccess"][$fieldName] = "[".@$arguments[$fieldRule."Code"]."]";
				else
					$GLOBALS["roles"][$this->role][$this->profile]["fieldAccess"][$fieldName] = $value;
			}					
			if (stripos($fieldRule,"fieldDefaults_")!==FALSE && stripos($fieldRule,"Code")===FALSE && stripos($fieldRule,"_name")===FALSE) {
				$fieldName = str_replace("fieldDefaults_","",$fieldRule);
				if ($value=="empty")
					unset($GLOBALS["roles"][$this->role][$this->profile]["fieldDefaults"][$fieldName]);
				else if ($value=="code" && @$arguments[$fieldRule."Code"]!="")
					$GLOBALS["roles"][$this->role][$this->profile]["fieldDefaults"][$fieldName] = "[".@$arguments[$fieldRule."Code"]."]";
				else if (@$arguments[$fieldRule."_name"] != "")
					$GLOBALS["roles"][$this->role][$this->profile]["fieldDefaults"][$fieldName] = str_replace($this->module_id."_","",@$arguments[$fieldRule."_name"]);
				else
					unset($GLOBALS["roles"][$this->role][$this->profile]["fieldDefaults"][$fieldName]);
			}					
		}
	}	

	function saveProfile_events($arguments) {
		foreach ($this->eventNames as $ruleName) {
			if (isset($arguments["eventInfoPanel_".$ruleName])) {
				$value = $arguments["eventInfoPanel_".$ruleName];
				$value2 = $arguments["eventMail_".$ruleName];
				if ($value=="empty")
					unset($GLOBALS["roles"][$this->role][$this->profile]["events"][$ruleName]["infoPanel"]);
				else
					$GLOBALS["roles"][$this->role][$this->profile]["events"][$ruleName]["infoPanel"] = $value;
				if ($value2=="empty")
					unset($GLOBALS["roles"][$this->role][$this->profile]["events"][$ruleName]["mail"]);
				else
					$GLOBALS["roles"][$this->role][$this->profile]["events"][$ruleName]["mail"] = $value2;						
				if ($value=="empty" and $value2=="empty")
					unset($GLOBALS["roles"][$this->role][$this->profile]["events"][$ruleName]);						
			}
		}
	}
	
	function saveProfile($arguments) {
		global $Objects;
		$this->setRole();
		if ($this->role["canEditProfile"]=="false")
			return 0;
		$arguments = (array)$arguments;
		$methods = get_class_methods(get_class($this));
		natsort($methods);
		foreach ($methods as $method) {
			if (stripos($method,"checkData_")!==FALSE) {
				$result = $this->$method($arguments);
				if (!$result)
					return 0;
			}				
		}
		foreach ($methods as $method) {
			if (stripos($method,"saveProfile_")!==FALSE) {
				$this->$method($arguments);
			}
		}
		$app=$Objects->get("Application");
		if (!$app->initiated)
			$app->initModules();
		$app->raiseRemoteEvent("PROFILE_CHANGED","object_id=".$this->getId());
		$this->file = $GLOBALS["roles"][$this->role]["file"];
		$str = "<?php\n".getMetadataString(getMetadataInFile($this->file))."\n?>";
		file_put_contents($this->file,$str);		
	}
}