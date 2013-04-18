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

class MetadataFieldParamsTable extends DataTable {
    
    function construct($params) {
        parent::construct($params);
        global $Objects;
        $this->usersList = "";
        $this->metadataArray = "fields";
        $this->clientClass = "MetadataFieldParamsTable";
        $this->parentClientClasses = "DataTable~Entity";        
    }

    function getArgs() {
        $result = parent::getArgs();
        global $Objects;       
        $id = $this->getId(); 
        $fields = $GLOBALS[$this->metadataArray];
        $str = "";
        $str .= $id."tbl.columns = new Array;\n";
        $str .= $id."tbl.columns[0] = new Array;\n";
        $str .= $id."tbl.columns[0]['name'] = 'fieldName';\n";
        $str .= $id."tbl.columns[0]['title'] = 'Имя';\n";
        $str .= $id."tbl.columns[0]['class'] = 'cell';\n";
        $str .= $id."tbl.columns[0]['properties'] = 'width=50%';\n";
        $str .= $id."tbl.columns[0]['control'] = 'string';\n";
        $str .= $id."tbl.columns[0]['control_properties'] = 'input_class=input1';\n";
        $str .= $id."tbl.columns[0]['must_set'] = true;\n";
        $str .= $id."tbl.columns[0]['unique'] = true;\n";
        $str .= $id."tbl.columns[0]['readonly'] = false;\n";

        $str .= $id."tbl.columns[1] = new Array;\n";
        $str .= $id."tbl.columns[1]['name'] = 'fieldValue';\n";
        $str .= $id."tbl.columns[1]['title'] = 'Значение';\n";
        $str .= $id."tbl.columns[1]['class'] = 'cell';\n";
        $str .= $id."tbl.columns[1]['properties'] = 'width=50%';\n";
        $str .= $id."tbl.columns[1]['control'] = 'string';\n";
        $str .= $id."tbl.columns[1]['control_properties'] = 'input_class=input1';\n";
        $str .= $id."tbl.columns[1]['must_set'] = true;\n";
        $str .= $id."tbl.columns[1]['unique'] = true;\n";
        $str .= $id."tbl.columns[1]['readonly'] = false;\n";
        
       
        $str .= $id."tbl.rows = new Array;\n";
        $str .= $id."tbl.rows[0] = new Array;\n";
        $str .= $id."tbl.rows[0]['class'] = '';\n";
        $str .= $id."tbl.rows[0]['properties'] = '';\n";
        $str .= $id."tbl.rows[0]['cells'] = new Array;\n";
        $str .= $id."tbl.rows[0]['cells'][0] = new Array;\n";
        $str .= $id."tbl.rows[0]['cells'][0]['properties'] = '';\n";
        $str .= $id."tbl.rows[0]['cells'][0]['value'] = 'Имя';\n";
        $str .= $id."tbl.rows[0]['cells'][0]['control'] = 'header';\n";
        $str .= $id."tbl.rows[0]['cells'][0]['class'] = 'header';\n";
        $str .= $id."tbl.rows[0]['cells'][0]['control_properties'] = 'input_class=input1,deactivated=false';\n";

        $str .= $id."tbl.rows[0]['cells'][1] = new Array;\n";
        $str .= $id."tbl.rows[0]['cells'][1]['properties'] = '';\n";
        $str .= $id."tbl.rows[0]['cells'][1]['value'] = 'Значение';\n";
        $str .= $id."tbl.rows[0]['cells'][1]['control'] = 'header';\n";
        $str .= $id."tbl.rows[0]['cells'][1]['class'] = 'header';\n";
        $str .= $id."tbl.rows[0]['cells'][1]['control_properties'] = 'input_class=input1,deactivated=false';\n";

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
        $str .= $id."tbl.emptyrow['cells'][1]['value'] = '';\n";
		$c=1;
        if (isset($fields[$this->fieldName])) {
            foreach ($fields[$this->fieldName]["params"] as $key=>$value) {
            	if ($key=="title" or $key=="metaTitle")
            		continue;
                $str .= $id."tbl.rows[$c] = new Array;\n";
                $str .= $id."tbl.rows[$c]['class'] = '';\n";
                $str .= $id."tbl.rows[$c]['properties'] = '';\n";
                $str .= $id."tbl.rows[$c]['cells'] = new Array;\n";
                $str .= $id."tbl.rows[$c]['cells'][0] = new Array;\n";
                $str .= $id."tbl.rows[$c]['cells'][0]['control'] = 'string';\n";
                $str .= $id."tbl.rows[$c]['cells'][0]['class'] = 'cell';\n";
                $str .= $id."tbl.rows[$c]['cells'][0]['value'] = '".@$key."';\n";

                $str .= $id."tbl.rows[$c]['cells'][1] = new Array;\n";
                $str .= $id."tbl.rows[$c]['cells'][1]['properties'] = '';\n";
                $str .= $id."tbl.rows[$c]['cells'][1]['class'] = 'cell';\n";
                $str .= $id."tbl.rows[$c]['cells'][1]['value'] = '".@$value."';\n";                    
				$c++;
            }
        }
        $result["{data}"] = $str;
        return $result;
    }   
}
?>