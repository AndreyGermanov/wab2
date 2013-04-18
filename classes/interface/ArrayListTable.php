<?php
class ArrayListTable extends DataTable {
    
    function construct($params) {
        parent::construct($params);
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
			$app->initModules();
		$this->skinPath = $app->skinPath;
        $this->cacheDepsList = "";
        $this->handler = "scripts/handlers/interface/ArrayListTable.js";
        $this->icon = $this->skinPath."images/Tree/list.png";
        $this->template = "templates/interface/ArrayListTable.html";
        $this->width = "450";
        $this->height = "400";
        $this->overrided = "width,height";
        $this->clientClass = "ArrayListTable";
        $this->parentClientClasses = "DataTable~Entity";        
    }

    function getArgs() {
        $result = parent::getArgs();
        global $Objects;
        $id = $this->getId();
        $str = "";
        $str .= $id."tbl.columns = new Array;\n";
        
        $str .= $id."tbl.columns[0] = new Array;\n";
        $str .= $id."tbl.columns[0]['name'] = 'fieldName';\n";
        $str .= $id."tbl.columns[0]['title'] = 'Значение';\n";
        $str .= $id."tbl.columns[0]['class'] = 'cell';\n";
        $str .= $id."tbl.columns[0]['properties'] = 'width=100%';\n";
        $str .= $id."tbl.columns[0]['control'] = 'string';\n";
        $str .= $id."tbl.columns[0]['control_properties'] = '".str_replace('~',',',$this->itemPrototype)."';\n";
        $str .= $id."tbl.columns[0]['must_set'] = true;\n";
        $str .= $id."tbl.columns[0]['unique'] = true;\n";
        $str .= $id."tbl.columns[0]['readonly'] = false;\n";

        $str .= $id."tbl.rows = new Array;\n";
        $str .= $id."tbl.rows[0] = new Array;\n";
        $str .= $id."tbl.rows[0]['class'] = '';\n";
        $str .= $id."tbl.rows[0]['properties'] = '';\n";
        $str .= $id."tbl.rows[0]['cells'] = new Array;\n";
        $str .= $id."tbl.rows[0]['cells'][0] = new Array;\n";
        $str .= $id."tbl.rows[0]['cells'][0]['properties'] = 'class=header';\n";
        $str .= $id."tbl.rows[0]['cells'][0]['value'] = 'Значение';\n";
        $str .= $id."tbl.rows[0]['cells'][0]['control'] = 'header';\n";
        $str .= $id."tbl.rows[0]['cells'][0]['control_properties'] = 'input_class=input1,deactivated=false';\n";

        $str .= $id."tbl.emptyrow = new Array;\n";
        $str .= $id."tbl.emptyrow['class'] = '';\n";
        $str .= $id."tbl.emptyrow['properties'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'] = new Array;\n";
        $str .= $id."tbl.emptyrow['cells'][0] = new Array;\n";
        $str .= $id."tbl.emptyrow['cells'][0]['properties'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][0]['control'] = 'entity';\n";
        $str .= $id."tbl.emptyrow['cells'][0]['control_properties'] = '".str_replace("~",",",$this->itemPrototype)."';\n";
        $str .= $id."tbl.emptyrow['cells'][0]['value'] = '';\n";

        if ($this->values != "") {
            $values = explode("~",$this->values);
            $c=1;
            foreach ($values as $per_item) {          
//            	$arr = explode("_",$per_item);
//            	$per_item = array_shift($arr)."_".$this->module_id."_".array_pop($arr);        
				$str .= $id."tbl.rows[$c] = new Array;\n";
				$str .= $id."tbl.rows[$c]['class'] = '';\n";
				$str .= $id."tbl.rows[$c]['properties'] = '';\n";
				$str .= $id."tbl.rows[$c]['cells'] = new Array;\n";
				$str .= $id."tbl.rows[$c]['cells'][0] = new Array;\n";
				$str .= $id."tbl.rows[$c]['cells'][0]['properties'] = '';\n";
				$str .= $id."tbl.rows[$c]['cells'][0]['control'] = 'entity';\n";
				$str .= $id."tbl.rows[$c]['cells'][0]['control_properties'] = '".str_replace("~",",",$this->itemPrototype)."';\n";
				$str .= $id."tbl.rows[$c]['cells'][0]['class'] = 'cell';\n";
				$str .= $id."tbl.rows[$c]['cells'][0]['value'] = '".$per_item."';\n";
				$c++;
            }
        }
        $result["{data}"] = $str;
        return $result;
    }  
    
    function getPresentation() {
		return "Список элементов";
	} 
}
?>