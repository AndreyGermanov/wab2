<?php
/**
 * Класс, реализующий регистр движений по дисконтным картам реферреров
 *  
 * Реквизиты регистра:
 * 
 * dateStart - Начало периода работы
 * dateEnd - Конец периода работы
 * contragent - контрагент
 * discountCardNumber - номер карты
 * summa - сумма (с плюсом - приход, с минусом - расход)
 * 
 * @author andrey
 */
class RegistryDiscountCards extends Registry {
    function construct($params) {
		parent::construct($params);		
        $this->classTitle = "Дисконтные карты";
        $this->classListTitle = "Дисконтные карты";
        $this->recvs = "contragent,discountCardNumber,summa";
        $this->clientClass = "RegistryDiscountCards";
        $this->parentClientClasses = "Registry~Entity";   
        $this->helpGuideId = "rozn_7.2";  
        $this->defaultReport = "Основной";        
        $this->printProfiles["Основной"] = array (
        		"settings" => array (
        			"settingsClass" => "RegistrySettingsDialog"
        		),
        		"Основной" => array (
	       			"printFields" => array (
	   					"regDate" => array (
							"size" => "10%"
	   					),
	   					"document" => array (
							"size" => "23%"
	   					),
	   					"contragent" => array (
							"size" => "10%",        							
	   					),
	   					"discountCardNumber" => array (
							"size" => "10%"
	   					),
	   					"summa" => array (
							"size" => "10%"
	   					)
	       			),
	        		"totals" => array (
	        			"bgColor" => "#DDDDDD",
	        			"fontFace" => "Arial",
	        			"fontSize" => "15",
	    				"fontWeight" => "bold",
						"fontColor" => "#000000",
	        			"totals" => array("summa")
	   				)        				
      			)        		
        );

        $this->printProfiles["Остатки"] = array (
        		"settings" => array (
        				"settingsClass" => ""
        		),
        		"Основной" => array (
        				"printFields" => array (
        						"regDate" => array (
        								"size" => "10%"
        						),
        						"document" => array (
        								"size" => "23%"
        						),
        						"prihod" => array (
        								"size" => "10%"
        						),
        						"rashod" => array (
        								"size" => "10%"
        						),        						
        						"ostatok" => array (
        								"size" => "10%"
        						)
        				),
	        			"sortFields" => array (
	        				"regDate" => "ASC"
	        			),
        				"groups" => array (
        						"contragent" => array (
        								"bgColor" => "#DDDDDD",
        								"fontFace" => "Arial",
        								"fontSize" => "15",
        								"fontWeight" => "bold",
        								"fontColor" => "#000000"
        						)
        				),
        				"totals" => array (
        						"bgColor" => "#DDDDDD",
        						"fontFace" => "Arial",
        						"fontSize" => "15",
        						"fontWeight" => "bold",
        						"fontColor" => "#000000",
        						"totals" => array("prihod","rashod","ostatok")
        				)
        		)
        );
        
    }
    
    function renderFormОстатки() {
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
    		if ($field=="prihod")
    			$title = "Приход";
    		elseif ($field=="rashod")
    			$title = "Расход";
    		elseif ($field=="ostatok")
    			$title = "Остаток";
    		else {
    			$fieldSettings = @$fields[$field]["params"];
    			$title = @$fieldSettings["title"];
    		}
    		$arr = array();
    		$arr["{fieldName}"] = $title;
    		if (isset($settings["size"]))
    			$arr["{cellWidth}"] = "width=".$settings["size"];
    		$out .= strtr($tableBlocks["titleCell"],$arr);
    	}
    	$out .= $tableBlocks["endStr"];
    	$sortFields = @(array)$this->printProfile["sortFields"];
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
    		$condFields = @(array)$this->printProfile["conditions"];
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
    	 
    	$rows = $this->getRecords($this->periodStart, $this->periodEnd,$printFieldsStr.",summa",$condStr,$sortStr);
    	 
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
    					$fieldSettings = @$fields[$field];
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
    						if ($field=="prihod" and $row["summa"]>0)
    							$fieldValue = $row["summa"];
    						elseif ($field=="rashod" and $row["summa"]<0)
    							$fieldValue = $row["summa"]-$row["summa"]*2;
    						elseif ($field=="ostatok")
    							$fieldValue = ""; 
    						elseif (isset($row[$field]))
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
    						if ($pkey=="ostatok")
    							$fieldValue = $totals[$index]["prihod"]-$totals[$index]["rashod"];    						    						
    						elseif (isset($totals[$index][$pkey]))
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
    				$fieldSettings = @$fields[$field];
    				if (@$fieldSettings["type"] == "entity") {
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
    				else if (@$fieldSettings["params"]["type"] == "date") {
    					if (isset($row[$field]))
    						$fieldValue = date("d.m.Y H:i:s",$row[$field]/1000);
    					else
    						$fieldValue = "";
    				}
    				else {    					
   						if ($field=="prihod" and $row["summa"]>0)
   							$fieldValue = $row["summa"];
   						elseif ($field=="rashod" and $row["summa"]<0)
   							$fieldValue = $row["summa"]-$row["summa"]*2;
   						elseif ($field=="ostatok")
   							$fieldValue = ""; 
    					elseif (isset($row[$field]))
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
    			if ($key=="ostatok")
    				$fieldValue = $fullTotals["prihod"]-$fullTotals["rashod"];
    			elseif (isset($fullTotals[$key]))
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
    	@unlink($this->argums);
    	return $out;
    }
    
}
?>