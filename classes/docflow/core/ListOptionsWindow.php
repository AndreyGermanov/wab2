<?php

/**
 * Класс реализует форму свойств списка документов, справочников и
 * других сущностей
 *
 * @author andrey
 */
class ListOptionsWindow extends WABEntity {
	
    function construct($params) {
        parent::construct($params);
        $this->template = "renderForm";             
        $this->handler = "scripts/handlers/docflow/core/ListOptionsWindow.js"; 
        $this->asAdminTemplate = true;
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
			$app->initModules();
        $this->skinPath = $app->skinPath;
        $this->icon = $this->skinPath."images/Tree/report.png";
        $this->tabset_id = "WebItemTabset_".$this->module_id."_".$this->name."ListOptions";
        $this->tabs_string = "main|Основное|".$app->skinPath."images/spacer.gif;";
        $this->tabs_string .= "fields|Поля|".$app->skinPath."images/spacer.gif;";
        $this->tabs_string .= "prnFields|Печатаемые поля|".$app->skinPath."images/spacer.gif;";
        $this->tabs_string .= "conditions|Условия отбора|".$app->skinPath."images/spacer.gif;";
        $this->tabs_string .= "tagsConditions|Доп. условия отбора|".$app->skinPath."images/spacer.gif";
        $this->active_tab = "main";
        $this->selectedFields = "";
        $this->allFields = "";
        $this->allPrintFields = "";
        $this->printFields = "";
        $this->conditions = "";
        $this->conditionFields = "";
        $this->showQRCode = "0";
        $this->sortField = "";
        $this->itemsPerPage = "";
        $this->width="650";
        $this->height = "450";
        $this->overrided = "width,height";
        $this->selected_fields_with_titles = "|";
        $this->loaded = false;
        $this->clientClass = "ListOptionsWindow";
        $this->parentClientClasses = "Entity";        
        $this->classTitle = "Окно настроек списка";
        $this->classListTitle = "Окно настроек списка";
    }
	
	function renderForm() {
		$blocks = getPrintBlocks(file_get_contents("templates/docflow/core/ListOptionsWindow.html"));
		$out = $blocks["header"];
		$this->setRole();
		if ($this->role["canEditProfile"]!=="false")
			$out .= $blocks["profile"];
		$out .= $blocks["mainFooter"];
		$this->getArgs();
		if (is_object($this->item)) {
			foreach ($this->conditionsArray as $key=>$value) {
				$args = array("{fieldName}" => $value["fieldName"],
							  "{fieldChecked}" => $value["fieldChecked"],
							  "{listType}" => $value["listType"],
							  "{listValue}" => $value["listValue"],
							  "{fieldType}" => $value["fieldType"],
							  "{fieldArgs}" => $value["fieldArgs"],
							  "{itemPrototype}" => $value["itemPrototype"],
							  "{fieldValue}" => $value["fieldValue"],
							  "{fieldTitle}" => $value["fieldTitle"]);
				$out .= strtr($blocks["row"],$args);			
			}
		}		
		$out .= $blocks["footer"];
		return $out;
	}
	
	function load() {
		global $Objects;
		if ($this->opener_object!="" and $this->opener_object != $this->getId()) {
			$this->opener_object = $Objects->get($this->opener_object);			
		}
		$this->item = $this->item2;
		if ($this->item!="") {
			$this->itemStr = $this->item;
			$this->item = $Objects->get($this->item);
		}
		$this->loaded = true;
	}
	
	function getId() {
		return get_class($this)."_".$this->module_id."_".$this->name;
	}

    function getArgs() {
        global $Objects;

        $this->condition = str_replace("xoxoxo",'"',$this->condition);        
        $sort_array = explode(" ",trim($this->sortField));
        if (count($sort_array)>1) {
			$this->sortDirection = array_pop($sort_array);
			$this->sortField = implode(" ",$sort_array);
		}
		if (!is_object($this->item))
			$this->item = $Objects->get($this->item);
		if (is_object($this->item)) {
			$tags = $this->item->getClassTagNames();
	//		$fieldList = explode(",",$this->fieldList);
			$allFieldList = explode(",",$this->allFieldList);
	//		$printFieldList = explode(",",$this->printFieldList);
			foreach ($tags as $tag) {
			//	$fieldList[] = $tag." ".$tag;
				$allFieldList[] = $tag." ".$tag;
				//$printFieldList[] = $tag." ".$tag;
			}
	//		$this->fieldList = implode(",",$fieldList);
			$this->allFieldList = implode("~",$allFieldList);
		//	$this->printFieldList = implode(",",$printFieldList);
		}
		
		$this->tagsConditionsTableId = "TagsConditionsTable_".$this->module_id."_".@$this->item->name;
		$names = array();$titles=array();$names2=array();
        $sel_fields_array = explode(",",$this->fieldList);
        foreach ($sel_fields_array as $field) {
			$parts = explode(" ",$field);
			if (count($parts)==2) {
				$titles[] = array_pop($parts);
				$names[] = implode(" ",$parts);
				$names2[] = implode(" ",$parts);
			} else {
				if (@$parts[1]=="AS") {
					$names2[] =  $parts[0];
					$names[] = array_shift($parts)." ".array_shift($parts)." ".array_shift($parts);
					$titles[] = implode(" ",$parts);
				} else {
					$names2[] = $parts[0];
					$names[] = array_shift($parts);
					$titles[] = implode(" ",$parts);
				}
			}
		}
		$this->selected_fields_with_titles = implode("~",$names2)."|".implode("~",$titles);
		$this->selected_fields_with_titles_2 = str_replace("~",",",implode("~",$names)."|".implode("~",$titles));
		$sel_names = $names;
        $names = array();$titles=array();
        $not_sel_fields_array = explode("~",$this->allFieldList);
        foreach ($not_sel_fields_array as $field) {
			$name = ""; $title = "";
			$parts = explode(" ",$field);
			if (count($parts)==2) {
				$title = array_pop($parts);
				$name = implode(" ",$parts);
			} else {
				if (@$parts[1]=="AS") {
					$name = array_shift($parts)." ".array_shift($parts)." ".array_shift($parts);
					$title = implode(" ",$parts);
				} else {
					$name = array_shift($parts);
					$title = implode(" ",$parts);
				}
			}
			if (in_array($name,$sel_names)===FALSE) {
				$names[] = $name;
				$titles[] = $title;
			}
		}
		$this->not_selected_fields_with_titles = implode(",",$names)."|".implode(",",$titles);
        $names = array();$titles=array();
        $sel_fields_array = explode("~",$this->printFieldList);
        foreach ($sel_fields_array as $field) {
			$parts = explode(" ",$field);
			if (count($parts)==2) {
				$titles[] = array_pop($parts);
				$names[] = implode(" ",$parts);
			} else {
				if (@$parts[1]=="AS") {
					$names[] = array_shift($parts)." ".array_shift($parts)." ".array_shift($parts);
					$titles[] = implode(" ",$parts);
				} else {
					$names[] = array_shift($parts);
					$titles[] = implode(" ",$parts);
				}
			}
		}
		$this->print_fields_with_titles = str_replace("~",",",implode("~",$names)."|".implode("~",$titles));
		$sel_names = $names;
        $names = array();$titles=array();
        $not_sel_fields_array = explode("~",$this->allFieldList);
        foreach ($not_sel_fields_array as $field) {
			$name = ""; $title = "";
			$parts = explode(" ",$field);
			if (count($parts)==2) {
				$title = array_pop($parts);
				$name = implode(" ",$parts);
			} else {
				if (@$parts[1]=="AS") {
					$name = array_shift($parts)." ".array_shift($parts)." ".array_shift($parts);
					$title = implode(" ",$parts);
				} else {
					$name = array_shift($parts);
					$title = implode(" ",$parts);
				}
			}
			if (in_array($name,$sel_names)===FALSE) {
				$names[] = $name;
				$titles[] = $title;
			}
		}
		$this->not_printed_fields_with_titles = implode(",",$names)."|".implode(",",$titles);
		if ($this->item!="") {
			$persistedArray = $this->item->getPersistedArray();
		}
		else {
			$persistedArray = array();
		}		
		$condArray = explode(",",$this->conditionFields);
		$conditionsArray = array();
		$count = 0;
		$regs = PDODataAdapter::getConditionRegs();
		$matches = array();
		$cond_results = array();
		$count = 0;
		$condition = str_replace('"',"'",$this->condition);
		foreach ($regs as $reg) {
			while (preg_match($reg,$condition,$matches)) {
				$cond_results[str_replace("@","",$matches[1])]["name"] = str_replace("@","",$matches[1]);
				$cond_results[str_replace("@","",$matches[1])]["operation"] = strtr($matches[2],array("=" => "eq", "!=" => "neq", "<" => "le", ">" => "ge", "<=" => "leeq", ">=" => "geeq", " LIKE " => "has", " NOT LIKE " => "notHas", " IN " => "inList", " NOT IN " => "notInList"));
				if ($matches[2]!=" IN " and $matches[2]!=" NOT IN ")
					$cond_results[str_replace("@","",$matches[1])]["value"] = strtr($matches[3],array("%" => "", "'" => ""));
				else {
					$value = substr($matches[3],1,strlen($matches[3])-1);
					$values_array = array();
					$matches2=array();
					while(preg_match("/\'(.*)\'/U",$value,$matches2)) {
						$values_array[] = strtr($matches2[1],array("%" => "", "'" => ""));
						$value = preg_replace("/\'(.*)\'/U","",$value,1);
					}
					$cond_results[str_replace("@","",$matches[1])]["value"] = implode("~",$values_array);
				}
				$condition = preg_replace($reg,"",$condition,1);
			}
		}
		global $fields,$models;
		foreach ($condArray as $value) {
			if ($value=="")
				continue;
			$val = explode("~",$value);
			$name = $val[0];$title=$val[1];
			array_shift($val);array_shift($val);
			$type = @implode("~",$val);
			$conditionsArray[$count]["listValue"] = @$cond_results[$name]["operation"];
			$conditionsArray[$count]["fieldName"] = $name;
			$conditionsArray[$count]["fieldTitle"] = $title;		
			if (isset($fields[@$models[get_class($this->item)][$name]])) {
				$f = $this->explodeField($fields[@$models[get_class($this->item)][$name]]);
				if ($type=="")
					$type = $f["params"]["type"];				
				$conditionsArray[$count]["fieldArgs"] = $this->getClientInputControlStr($f["params"]);
				$conditionsArray[$count]["fieldType"] = "";
			} else
				$conditionsArray[$count]["fieldArgs"] = "type=".$type;
			if ($conditionsArray[$count]["listValue"]=="inList" or $conditionsArray[$count]["listValue"]=="notInList") {
				$conditionsArray[$count]["fieldType"] = "array";
				$conditionsArray[$count]["itemPrototype"] = $conditionsArray[$count]["fieldArgs"];
			} else {
				$conditionsArray[$count]["fieldType"] = $type;
				$conditionsArray[$count]["itemPrototype"] = "";
			}
			if ($type=="decimal" or $type=="integer")
				$conditionsArray[$count]["listType"] = "list,eq~neq~le~ge~leeq~reeq~inList~notInList|равно~не равно~меньше~больше~меньше или равно~больше или равно~в списке~не в списке";
			else if ($type == "string" or $type=="text")
				$conditionsArray[$count]["listType"] = "list,eq~neq~has~notHas~inList~notInList|равно~не равно~содержит~не содержит~в списке~не в списке";
			else
				$conditionsArray[$count]["listType"] = "list,eq~neq~inList~notInList|равно~не равно~в списке~не в списке";
			$conditionsArray[$count]["fieldValue"] = @$cond_results[$name]["value"];
			if ($conditionsArray[$count]["fieldValue"]!="")
				$conditionsArray[$count]["fieldChecked"] = "1";
			else
				$conditionsArray[$count]["fieldChecked"] = "0";
			$count++;
		}
		$this->conditionsArray = $conditionsArray;
        $result = parent::getArgs();       
        return $result;
    }
    
    function getPresentation() {
		return "Свойства списка";
	}
}
?>