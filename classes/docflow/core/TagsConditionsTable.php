<?php
/*
 * Класс, реализующий таблицу отбора сущностей по тэгам
 * 
 */
class TagsConditionsTable extends DataTable { 
	   
    function construct($params) {
        parent::construct($params);
        global $Objects;
        $this->entityObject = "";
        $this->tagsCondition = "";
        $this->handler = "scripts/handlers/docflow/core/TagsConditionsTable.js";
        $this->res = "";
        $this->clientClass = "TagsConditionsTable";
        $this->parentClientClasses = "DataTable~Entity";        
    }

    function getArgs() {
        $result = parent::getArgs();
        global $Objects;
        if ($this->entityObject!="")
        	$this->entityObject = $Objects->get($this->entityObject);
        if (is_object($this->entityObject) and method_exists($this->entityObject,"getId")) {
        	$this->entityObject->loaded=false;
        	$this->entityObject->load();
        } else
        	return 0;
		$tagNames="";
        $tagNames = implode("~",$this->entityObject->getClassTagNames());
                       
        $id = $this->getId();
        $str = "";
        $str .= $id."tbl.columns = new Array;\n";
        
        $str .= $id."tbl.columns[0] = new Array;\n";
        $str .= $id."tbl.columns[0]['name'] = 'tagName';\n";
        $str .= $id."tbl.columns[0]['title'] = 'Имя поля';\n";
        $str .= $id."tbl.columns[0]['class'] = 'cell';\n";
        $str .= $id."tbl.columns[0]['properties'] = 'width=40%';\n";
        $str .= $id."tbl.columns[0]['control'] = 'list,~".$tagNames."|~".$tagNames."';\n";
        $str .= $id."tbl.columns[0]['control_properties'] = 'deactivated=true,input_class=input1';\n";
        $str .= $id."tbl.columns[0]['must_set'] = true;\n";
        $str .= $id."tbl.columns[0]['unique'] = true;\n";
        $str .= $id."tbl.columns[0]['readonly'] = false;\n";

        $str .= $id."tbl.columns[1] = new Array;\n";
        $str .= $id."tbl.columns[1]['name'] = 'tagCondition';\n";
        $str .= $id."tbl.columns[1]['title'] = 'Условие';\n";
        $str .= $id."tbl.columns[1]['class'] = 'cell';\n";
        $str .= $id."tbl.columns[1]['properties'] = '';\n";
        $str .= $id."tbl.columns[1]['control'] = 'list,eq~neq~has~notHas~inList~notInList|равно~не равно~содержит~не содержит~в списке~не в списке';\n";
        $str .= $id."tbl.columns[1]['control_properties'] = 'deactivated=true,input_class=input1';\n";
        $str .= $id."tbl.columns[1]['must_set'] = true;\n";
        $str .= $id."tbl.columns[1]['unique'] = false;\n";
        $str .= $id."tbl.columns[1]['readonly'] = false;\n";        

        $str .= $id."tbl.columns[2] = new Array;\n";
        $str .= $id."tbl.columns[2]['name'] = 'tagValue';\n";
        $str .= $id."tbl.columns[2]['title'] = 'Значение';\n";
        $str .= $id."tbl.columns[2]['class'] = 'cell';\n";
        $str .= $id."tbl.columns[2]['properties'] = 'width=40%';\n";
        $str .= $id."tbl.columns[2]['control'] = 'string';\n";
        $str .= $id."tbl.columns[2]['control_properties'] = 'deactivated=true,input_class=input1,selectClass=SelectEntityFloatMenu,selectOptions=,showOnKeyPress=true,hideSelectButton=true';\n";
        $str .= $id."tbl.columns[2]['must_set'] = false;\n";
        $str .= $id."tbl.columns[2]['unique'] = false;\n";
        $str .= $id."tbl.columns[2]['readonly'] = false;\n";
        
        $str .= $id."tbl.rows = new Array;\n";
        $str .= $id."tbl.rows[0] = new Array;\n";
        $str .= $id."tbl.rows[0]['class'] = '';\n";
        $str .= $id."tbl.rows[0]['properties'] = '';\n";
        $str .= $id."tbl.rows[0]['cells'] = new Array;\n";
        $str .= $id."tbl.rows[0]['cells'][0] = new Array;\n";
        $str .= $id."tbl.rows[0]['cells'][0]['properties'] = '';\n";
        $str .= $id."tbl.rows[0]['cells'][0]['class'] = 'header';\n";
        $str .= $id."tbl.rows[0]['cells'][0]['value'] = 'Имя поля';\n";
        $str .= $id."tbl.rows[0]['cells'][0]['control'] = 'header';\n";
        $str .= $id."tbl.rows[0]['cells'][0]['control_properties'] = 'input_class=input1,deactivated=false';\n";

        $str .= $id."tbl.rows[0]['cells'][1] = new Array;\n";
        $str .= $id."tbl.rows[0]['cells'][1]['properties'] = '';\n";
        $str .= $id."tbl.rows[0]['cells'][1]['value'] = 'Условие';\n";
        $str .= $id."tbl.rows[0]['cells'][1]['control'] = 'header';\n";
        $str .= $id."tbl.rows[0]['cells'][1]['control_properties'] = 'input_class=input1,deactivated=false';\n";
        $str .= $id."tbl.rows[0]['cells'][1]['class'] = 'header';\n";

        $str .= $id."tbl.rows[0]['cells'][2] = new Array;\n";
        $str .= $id."tbl.rows[0]['cells'][2]['properties'] = '';\n";
        $str .= $id."tbl.rows[0]['cells'][2]['value'] = 'Значение';\n";
        $str .= $id."tbl.rows[0]['cells'][2]['control'] = 'header';\n";
        $str .= $id."tbl.rows[0]['cells'][2]['control_properties'] = 'input_class=input1,deactivated=false';\n";
        $str .= $id."tbl.rows[0]['cells'][2]['class'] = 'header';\n";
        

        $str .= $id."tbl.emptyrow = new Array;\n";
        $str .= $id."tbl.emptyrow['class'] = '';\n";
        $str .= $id."tbl.emptyrow['properties'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'] = new Array;\n";
        $str .= $id."tbl.emptyrow['cells'][0] = new Array;\n";
        $str .= $id."tbl.emptyrow['cells'][0]['properties'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][0]['value'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][1] = new Array;\n";
        $str .= $id."tbl.emptyrow['cells'][1]['properties'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][1]['value'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][2] = new Array;\n";
        $str .= $id."tbl.emptyrow['cells'][2]['properties'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][2]['value'] = '';\n";
        $c = 1;
        
        $regs = PDODataAdapter::getConditionRegs();
        $matches = array();
        $cond_results = array();
        $count = 0;        
        $condition = str_replace('"',"'",str_replace("xoxoxo","'",$this->tagsCondition));
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
        
        $c = 1;
        foreach ($cond_results as $row) {
        	$str .= $id."tbl.rows[$c] = new Array;\n";
        	$str .= $id."tbl.rows[$c]['cells'] = new Array;\n";
        	$str .= $id."tbl.rows[$c]['cells'][0] = new Array;\n";
        	$str .= $id."tbl.rows[$c]['cells'][0]['value'] = '".str_replace("\n\r","xoxoxo",$row["name"])."';".$id."tbl.rows[$c]['cells'][0]['value'] = ".$id."tbl.rows[$c]['cells'][0]['value'].replace(/xoxoxo/g,'\\r\\n');\n";
        	$str .= $id."tbl.rows[$c]['cells'][0]['control_properties'] = 'deactivated=true,input_class=input1';\n";
        	$str .= $id."tbl.rows[$c]['cells'][1] = new Array;\n";
        	$str .= $id."tbl.rows[$c]['cells'][1]['value'] = '".str_replace("\r\n","xoxoxo",$row["operation"])."';".$id."tbl.rows[$c]['cells'][1]['value'] = ".$id."tbl.rows[$c]['cells'][1]['value'].replace(/xoxoxo/g,'\\r\\n');\n";
        	$str .= $id."tbl.rows[$c]['cells'][1]['control_properties'] = 'deactivated=true,input_class=input1';\n";
        	$str .= $id."tbl.rows[$c]['cells'][2] = new Array;\n";
        	$str .= $id."tbl.rows[$c]['cells'][2]['value'] = '".str_replace("\r\n","xoxoxo",$row["value"])."';".$id."tbl.rows[$c]['cells'][1]['value'] = ".$id."tbl.rows[$c]['cells'][1]['value'].replace(/xoxoxo/g,'\\r\\n');\n";
        	$str .= $id."tbl.rows[$c]['cells'][2]['control_properties'] = 'deactivated=true,input_class=input1';\n";
        	$c++;
        }        
        $result["{data}"] = $str;
        return $result;
    } 
}
?>