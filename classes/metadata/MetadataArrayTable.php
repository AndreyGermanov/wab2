<?php
/*
 * Класс, реализующий таблицу параметров поля метаданных
 *
 * Он представляет из себя таблицу со следующими колонками:
 *
 * "Имя","Значение"
 *
 * Таблица формируется методом getArgs(), используется поле fieldName, на основании 
 * которого формируются значения таблицы. 
 *
 * Данные возвращаются клиенту в виде объекта, с помощью метода getSingleValue().
 *
 * (C) 2012 ООО "ЛВА". Все права защищены
 *
 * @andrey 11.07.2012 09:45:00
 *
 */

class MetadataArrayTable extends DataTable {
    
    function construct($params) {
        parent::construct($params);
        global $Objects;
        $this->usersList = "";
        $this->clientClass = "MetadataArrayTable";
        $this->parentClientClasses = "DataTable~Entity";        
    }

    function getArgs() {
        $result = parent::getArgs();
        global $Objects;
        $str = "";
        switch ($this->metadataArray) {
        	case "models":
        		if ($this->arrayField=="all") {
        			$selectClass = "MetadataTree";
        			$selectOptions = "metadataArray=fields~forSelect=1~groupSelect=0~itemSelect=1~loaded=true";
        		}
        		if ($this->arrayField=="groups") {
        			$selectClass = "MetadataTree";
        			$selectOptions = "metadataArray=fields~forSelect=1~groupSelect=1~itemSelect=0~loaded=true~showItems=0";
        		}
        		break;
        	case "groups":
        		if ($this->arrayField=="fields") {
        			$selectClass = "MetadataTree";
        			$selectOptions = "metadataArray=fields~forSelect=1~groupSelect=0~itemSelect=1~loaded=true";
        		}
        		if ($this->arrayField=="groups") {
        			$selectClass = "MetadataTree";
        			$selectOptions = "metadataArray=fields~forSelect=1~groupSelect=1~itemSelect=0~loaded=true~showItems=0";
        		}
        		break;
       		case "tags":
       			if ($this->arrayField=="fields") {
       				$selectClass = "MetadataTree";
       				$selectOptions = "metadataArray=tags~forSelect=1~groupSelect=0~itemSelect=1~loaded=true";
       			}
       			break;        		
        	case "modelGroups":
        		if ($this->arrayField=="fields") {
        			$selectClass = "MetadataTree";
        			$selectOptions = "metadataArray=models~forSelect=1~groupSelect=0~itemSelect=1~loaded=true";
        		}
        		if ($this->arrayField=="groups") {
        			$selectClass = "MetadataTree";
        			$selectOptions = "metadataArray=models~forSelect=1~groupSelect=1~itemSelect=0~loaded=true~showItems=0";
        		}
        		break;
       		case "codeGroups":
       			if ($this->arrayField=="fields") {
       				$selectClass = "MetadataTree";
       				$selectOptions = "metadataArray=codes~forSelect=1~groupSelect=0~itemSelect=1~loaded=true";
       			}
      			if ($this->arrayField=="groups") {
       				$selectClass = "MetadataTree";
        			$selectOptions = "metadataArray=codes~forSelect=1~groupSelect=1~itemSelect=0~loaded=true~showItems=0";
      			}
      			break;       		
       		case "panels":
       			$selectClass = "MetadataTree";
       			$selectOptions = "metadataArray=modules~forSelect=1~groupSelect=0~itemSelect=1~loaded=true";
       			break;       			       		
       		case "userconfig":
       			$selectClass = "MetadataTree";
       			if ($this->arrayField=="roles")
       				$selectOptions = "metadataArray=roles~forSelect=1~groupSelect=0~itemSelect=1~loaded=true";
       			else
       				$selectOptions = "metadataArray=modules~forSelect=1~groupSelect=0~itemSelect=1~loaded=true";
       			break;       			       		
        }
        $id = $this->getId();
        $str .= $id."tbl.columns = new Array;\n";
        $str .= $id."tbl.columns[0] = new Array;\n";
        $str .= $id."tbl.columns[0]['name'] = 'fieldName';\n";
        $str .= $id."tbl.columns[0]['title'] = 'Имя';\n";
        $str .= $id."tbl.columns[0]['class'] = 'cell';\n";
        $str .= $id."tbl.columns[0]['properties'] = 'width=50%';\n";
        $str .= $id."tbl.columns[0]['control'] = 'string';\n";
        $str .= $id."tbl.columns[0]['control_properties'] = 'input_class=input1,width=100%,selectClass=".$selectClass.",selectOptions=".str_replace("=","xoxoxo",$selectOptions)."';\n";
        $str .= $id."tbl.columns[0]['must_set'] = true;\n";
        $str .= $id."tbl.columns[0]['unique'] = true;\n";
        $str .= $id."tbl.columns[0]['readonly'] = false;\n";
        
        
        $str .= $id."tbl.rows = new Array;\n";
        $str .= $id."tbl.rows[0] = new Array;\n";
        $str .= $id."tbl.rows[0]['class'] = '';\n";
        $str .= $id."tbl.rows[0]['properties'] = '';\n";
        $str .= $id."tbl.rows[0]['cells'] = new Array;\n";
        $str .= $id."tbl.rows[0]['cells'][0] = new Array;\n";
        $str .= $id."tbl.rows[0]['cells'][0]['properties'] = '';\n";
        $str .= $id."tbl.rows[0]['cells'][0]['value'] = 'Значение';\n";
        $str .= $id."tbl.rows[0]['cells'][0]['control'] = 'header';\n";
        $str .= $id."tbl.rows[0]['cells'][0]['class'] = 'header';\n";
        $str .= $id."tbl.rows[0]['cells'][0]['control_properties'] = 'input_class=input1,deactivated=false';\n";


        $str .= $id."tbl.emptyrow = new Array;\n";
        $str .= $id."tbl.emptyrow['class'] = '';\n";
        $str .= $id."tbl.emptyrow['properties'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'] = new Array;\n";
        $str .= $id."tbl.emptyrow['cells'][0] = new Array;\n";
        $str .= $id."tbl.emptyrow['cells'][0]['properties'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][0]['control'] = 'string';\n";
        $str .= $id."tbl.emptyrow['cells'][0]['value'] = '';\n";

		$arr = array();
		if ($this->arrayField=="all") {
			if (isset($GLOBALS["models"][$this->fieldName]))
				foreach ($GLOBALS["models"][$this->fieldName] as $key=>$value) {
					if ($key!="file" and $key!="metaTitle" and $key!="groups")
						$arr[] = $value;
				}
		}
		else if ($this->metadataArray=="userconfig") {
		 	$arr = array_keys(@$GLOBALS[$this->metadataArray][$this->arrayField]);		 	
		} else if ($this->metadataArray!="panels") {
			$arr = @$GLOBALS[$this->metadataArray][$this->fieldName][$this->arrayField];
		} else {
			$arr = @array_keys(@$GLOBALS[$this->metadataArray][$this->fieldName][$this->arrayField]);
		}
		
		$c=1;
		if (is_array($arr)) {
	        foreach ($arr as $value) {
	            $str .= $id."tbl.rows[$c] = new Array;\n";
	            $str .= $id."tbl.rows[$c]['class'] = '';\n";
	            $str .= $id."tbl.rows[$c]['properties'] = '';\n";
	            $str .= $id."tbl.rows[$c]['cells'] = new Array;\n";
	            $str .= $id."tbl.rows[$c]['cells'][0] = new Array;\n";
	            $str .= $id."tbl.rows[$c]['cells'][0]['control'] = 'string';\n";
	            $str .= $id."tbl.rows[$c]['cells'][0]['class'] = 'cell';\n";
	            $str .= $id."tbl.rows[$c]['cells'][0]['value'] = '".@$value."';\n";
				$c++;
	        }
		}
        $result["{data}"] = $str;
        return $result;
    }   
}
?>