<?php
/*
 * Класс, реализующий таблицу тэгов
 * 
 */
class TagsTable extends DataTable { 
	   
    function construct($params) {
        parent::construct($params);
        global $Objects,$tags;
        $this->entityObject = "";
        $this->handler = "scripts/handlers/docflow/core/TagsTable.js";
        $this->template = "templates/docflow/core/TagsTable.html";
        $this->res = "";
        $this->clientClass = "TagsTable";
        $this->parentClientClasses = "DataTable~Entity";        
        $this->tagGroup = "";
    }

    function getArgs() {
        $result = parent::getArgs();
        global $Objects,$tags,$fields,$tagGroups;
        
        $result["{tagGroupsString}"] = " ~".implode("~",array_keys($tagGroups))."| ~".implode("~",array_keys($tagGroups));
        if ($this->entityObject!="")
        	$this->entityObject = $Objects->get($this->entityObject);
        if (is_object($this->entityObject) and method_exists($this->entityObject,"getId")) {
        	$this->entityObject->loaded=false;
        	$this->entityObject->load();
        } else if ($this->tagGroup=="")
        	return 0;
        if ($this->tagGroup=="")        
        	$tbl = explode("~",$this->entityObject->tags);
        else
        	$tbl = $tagGroups[$this->tagGroup];
        
        $id = $this->getId();
        $str = "";//$id."tbl=new Array;";
        $str .= $id."tbl.columns = new Array;\n";
        
        $str .= $id."tbl.columns[0] = new Array;\n";
        $str .= $id."tbl.columns[0]['name'] = 'name';\n";
        $str .= $id."tbl.columns[0]['title'] = 'Имя';\n";
        $str .= $id."tbl.columns[0]['class'] = 'cell';\n";
        $str .= $id."tbl.columns[0]['properties'] = '';\n";
        $str .= $id."tbl.columns[0]['control'] = 'string';\n";
        $str .= $id."tbl.columns[0]['control_properties'] = 'deactivated=true,input_class=input1,selectClass=SelectTagFloatMenu,showOnKeyPress=true,hideSelectButton=true';\n";
        $str .= $id."tbl.columns[0]['must_set'] = true;\n";
        $str .= $id."tbl.columns[0]['unique'] = false;\n";
        $str .= $id."tbl.columns[0]['readonly'] = false;\n";

        $str .= $id."tbl.columns[1] = new Array;\n";
        $str .= $id."tbl.columns[1]['name'] = 'value';\n";
        $str .= $id."tbl.columns[1]['title'] = 'Значение';\n";
        $str .= $id."tbl.columns[1]['class'] = 'cell';\n";
        $str .= $id."tbl.columns[1]['properties'] = 'width=60%';\n";
        $str .= $id."tbl.columns[1]['control'] = 'text';\n";
        $str .= $id."tbl.columns[1]['control_properties'] = 'deactivated=true,input_class=input1,selectClass=SelectEntityFloatMenu,selectOptions=,showOnKeyPress=true,hideSelectButton=true';\n";
        $str .= $id."tbl.columns[1]['must_set'] = true;\n";
        $str .= $id."tbl.columns[1]['unique'] = false;\n";
        $str .= $id."tbl.columns[1]['readonly'] = false;\n";        
        
        $str .= $id."tbl.rows = new Array;\n";
        $str .= $id."tbl.rows[0] = new Array;\n";
        $str .= $id."tbl.rows[0]['class'] = '';\n";
        $str .= $id."tbl.rows[0]['properties'] = '';\n";
        $str .= $id."tbl.rows[0]['cells'] = new Array;\n";
        $str .= $id."tbl.rows[0]['cells'][0] = new Array;\n";
        $str .= $id."tbl.rows[0]['cells'][0]['properties'] = '';\n";
        $str .= $id."tbl.rows[0]['cells'][0]['class'] = 'header';\n";
        $str .= $id."tbl.rows[0]['cells'][0]['value'] = 'Имя';\n";
        $str .= $id."tbl.rows[0]['cells'][0]['control'] = 'header';\n";
        $str .= $id."tbl.rows[0]['cells'][0]['control_properties'] = 'input_class=input1,deactivated=false';\n";

        $str .= $id."tbl.rows[0]['cells'][1] = new Array;\n";
        $str .= $id."tbl.rows[0]['cells'][1]['properties'] = '';\n";
        $str .= $id."tbl.rows[0]['cells'][1]['value'] = 'Значение';\n";
        $str .= $id."tbl.rows[0]['cells'][1]['control'] = 'header';\n";
        $str .= $id."tbl.rows[0]['cells'][1]['control_properties'] = 'input_class=input1,deactivated=false';\n";
        $str .= $id."tbl.rows[0]['cells'][1]['class'] = 'header';\n";
                

        $str .= $id."tbl.emptyrow = new Array;\n";
        $str .= $id."tbl.emptyrow['class'] = '';\n";
        $str .= $id."tbl.emptyrow['properties'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'] = new Array;\n";
        $str .= $id."tbl.emptyrow['cells'][0] = new Array;\n";
        $str .= $id."tbl.emptyrow['cells'][0]['properties'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][0]['control'] = 'string';\n";
        $str .= $id."tbl.emptyrow['cells'][0]['value'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][1] = new Array;\n";
        $str .= $id."tbl.emptyrow['cells'][1]['properties'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][1]['control'] = 'text';\n";
        $str .= $id."tbl.emptyrow['cells'][1]['value'] = '';\n";
        $c = 1;
        foreach ($tbl as $row) {        	  
        	if (@$row[0]=="/" or trim($row)=="")
        		continue; 
	        $str .= $id."tbl.rows[$c] = new Array;\n";
	        $str .= $id."tbl.rows[$c]['cells'] = new Array;\n";
	        $str .= $id."tbl.rows[$c]['cells'][0] = new Array;\n";
	        $str .= $id."tbl.rows[$c]['cells'][0]['value'] = '".str_replace("\n\r","xoxoxo",$row)."';".$id."tbl.rows[$c]['cells'][0]['value'] = ".$id."tbl.rows[$c]['cells'][0]['value'].replace(/xoxoxo/g,'\\r\\n');\n";
	        $str .= $id."tbl.rows[$c]['cells'][0]['control_properties'] = 'deactivated=true,input_class=input1,selectClass=SelectTagFloatMenu,selectOptions=entityClasszozo".get_class($this->entityObject)."~resultFieldszozoname~resultObjectzozo".$this->getId()."_".$c.",showOnKeyPress=true,hideSelectButton=true';\n";
	        $str .= $id."tbl.rows[$c]['cells'][1] = new Array;\n";
	        $ctl_properties  = 'deactivated=true,input_class=input1';
	        if (isset($tags[$row])) {
	        	if (isset($fields[$tags[$row]["field"]])) {
	        		$field = $fields[$tags[$row]["field"]];
	        		if (isset($field["type"])) {
		        		$str .= $id."tbl.rows[$c]['cells'][1]['control'] = '".$field["type"]."';\n";
		        		if ($field["type"]!="list" and $field["type"]!="listedit" and $field["type"]!="boolean")
		        			$ctl_properties .= ',selectClass=SelectEntityFloatMenu,selectOptions=entityClasszozo'.@get_class(@$this->entityObject).'xyzxyzsearchFieldzozo'.$row.'xyzxyzdisplayFieldzozo'.$row.'xyzxyzresultFieldszozo'.$row.'|valuexyzxyzresultObjectzozo'.$this->getId()."_".$c.',showOnKeyPress=true,hideSelectButton=true';
	        			
		        	}
	        	}
	        	$ctl_properties .= ",".str_replace("~",",",$this->getClientInputControlStr(@$field["params"]));
	        	
	        } else
	        	$ctl_properties .= ',selectClass=SelectEntityFloatMenu,selectOptions=entityClasszozo'.@get_class(@$this->entityObject).'xyzxyzsearchFieldzozo'.$row.',showOnKeyPress=true,hideSelectButton=true';
	        if ($this->tagGroup=="")
	        	$value = @$this->entityObject->fields[$row];
	        else
	        	$value = @$tags[$row]["value"];
	        $str .= $id."tbl.rows[$c]['cells'][1]['value'] = '".str_replace("\r\n","xoxoxo",$value)."';".$id."tbl.rows[$c]['cells'][1]['value'] = ".$id."tbl.rows[$c]['cells'][1]['value'].replace(/xoxoxo/g,'\\r\\n');\n";
	        $str .= $id."tbl.rows[$c]['cells'][1]['control_properties'] = '".$ctl_properties."';\n";
	        $c++;
        }
        $result["{data}"] = $str;
        return $result;
    } 
    
    function getTagControlProperties($tag) {
    	global $tags,$fields;
    	$row = $tag;
    	$result = array();
    	if (isset($tags[$row])) {
    		if (isset($fields[$tags[$row]["field"]])) {
    			$field = $fields[$tags[$row]["field"]];
    			if (isset($field["type"])) {
    				$result["type"] = $field["type"];    		
    			}
    			$result["properties"] = 'deactivated=true,input_class=input1,'.str_replace("~",",",$this->getClientInputControlStr(@$field["params"]));
    			$result["value"] = @$tags[$row]["value"];    		
    		}
    	} else {
    		$result["type"] = "text";
    		$result["properties"] = "";
    		$result["value"] = ""; 
    	}
    	echo json_encode($result);
    }
    
    function getTagControlPropertiesHook($arguments) {
    	return $this->getTagControlProperties($arguments["tag"]);
    }
    
    function getDataHook($arguments) {
    	$this->setArguments($arguments);
    	$result = $this->getArgs();
    	echo $result["{data}"];
    }
    
    function getHookProc($number) {
    	switch ($number) {
    		case '3': return "getTagControlPropertiesHook";
    		case '4': return "getDataHook";
    	}
    	return parent::getHookProc($number);    	 
    }        
}
?>