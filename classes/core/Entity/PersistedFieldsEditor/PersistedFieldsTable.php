<?php
/**
 * Данный класс предназначен для редактирования полей сущности, которые
 * будут записываться в базу данных. Этот класс отображает список этих полей в
 * виде таблицы DataTable, позволяя менять наименования полей, их типы и другие
 * параметры. При редактировании, изменения сохраняются в формате строки
 * WABEntity->persistedFields.
 *
 * @author andrey
 */
class PersistedFieldsTable extends DataTable {

    function construct($params) {
        parent::construct($params);
        global $defaultCacheDataAdapter;
        $this->adapter = $defaultCacheDataAdapter;
        $this->persistedFields = "";
        $this->handler = "scripts/handlers/PersistedFieldsEditor/PersistedFieldsTable.js";
    }

    function getArgs() {
        $result = parent::getArgs();
        $id = str_replace("~","",$this->getId());
        $str = "";
        $str .= $id."tbl.columns = new Array;\n";
        
        $str .= $id."tbl.columns[0] = new Array;\n";
        $str .= $id."tbl.columns[0]['name'] = 'fieldName';\n";
        $str .= $id."tbl.columns[0]['title'] = 'Имя поля';\n";
        $str .= $id."tbl.columns[0]['class'] = 'cell';\n";
        $str .= $id."tbl.columns[0]['properties'] = 'width=40%';\n";
        $str .= $id."tbl.columns[0]['control'] = 'string';\n";
        $str .= $id."tbl.columns[0]['control_properties'] = 'deactivated=true,input_class=input1';\n";
        $str .= $id."tbl.columns[0]['must_set'] = true;\n";
        $str .= $id."tbl.columns[0]['unique'] = true;\n";
        $str .= $id."tbl.columns[0]['readonly'] = false;\n";

        $str .= $id."tbl.columns[1] = new Array;\n";
        $str .= $id."tbl.columns[1]['name'] = 'fieldType';\n";
        $str .= $id."tbl.columns[1]['title'] = 'Тип поля';\n";
        $str .= $id."tbl.columns[1]['class'] = 'cell';\n";
        $str .= $id."tbl.columns[1]['properties'] = 'width=40%';\n";
        $str .= $id."tbl.columns[1]['control'] = 'list,integer~decimal~string~boolean~text~date~list~listedit~entity~file~path~array|Целое число~Дробное число~Строка~Логическое(да/нет)~Текст~Дата/время~Выбор из списка~Выбор из списка с редактированием~Сущность~Выбор файла~Выбор каталога~Список значений';\n";
        $str .= $id."tbl.columns[1]['control_properties'] = 'deactivated=true,input_class=input1';\n";
        $str .= $id."tbl.columns[1]['must_set'] = true;\n";
        $str .= $id."tbl.columns[1]['unique'] = false;\n";
        $str .= $id."tbl.columns[1]['readonly'] = false;\n";

        $str .= $id."tbl.columns[2] = new Array;\n";
        $str .= $id."tbl.columns[2]['name'] = 'fieldProperties';\n";
        $str .= $id."tbl.columns[2]['title'] = 'Параметры';\n";
        $str .= $id."tbl.columns[2]['class'] = 'cell';\n";
        $str .= $id."tbl.columns[2]['properties'] = 'width=20%';\n";
        $str .= $id."tbl.columns[2]['control'] = 'hidden';\n";
        $str .= $id."tbl.columns[2]['control_properties'] = 'showValue=true,width=100%,editorType=WABWindow';\n";
        $str .= $id."tbl.columns[2]['must_set'] = false;\n";
        $str .= $id."tbl.columns[2]['unique'] = false;\n";
        $str .= $id."tbl.columns[2]['readonly'] = false;\n";

        $str .= $id."tbl.columns[3] = new Array;\n";
        $str .= $id."tbl.columns[3]['name'] = 'oldFieldName';\n";
        $str .= $id."tbl.columns[3]['title'] = 'old';\n";
        $str .= $id."tbl.columns[3]['class'] = 'hidden';\n";
        $str .= $id."tbl.columns[3]['properties'] = 'width=10%';\n";
        $str .= $id."tbl.columns[3]['control'] = 'cell';\n";
        $str .= $id."tbl.columns[3]['control_properties'] = '';\n";
        $str .= $id."tbl.columns[3]['must_set'] = false;\n";
        $str .= $id."tbl.columns[3]['unique'] = false;\n";
        $str .= $id."tbl.columns[3]['readonly'] = false;\n";

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
        $str .= $id."tbl.rows[0]['cells'][0]['control_properties'] = 'input_class=input1,deactivated=true';\n";

        $str .= $id."tbl.rows[0]['cells'][1] = new Array;\n";
        $str .= $id."tbl.rows[0]['cells'][1]['properties'] = '';\n";
        $str .= $id."tbl.rows[0]['cells'][1]['class'] = 'header';\n";
        $str .= $id."tbl.rows[0]['cells'][1]['value'] = 'Тип поля';\n";
        $str .= $id."tbl.rows[0]['cells'][1]['control'] = 'header';\n";
        $str .= $id."tbl.rows[0]['cells'][1]['control_properties'] = '';\n";

        $str .= $id."tbl.rows[0]['cells'][2] = new Array;\n";
        $str .= $id."tbl.rows[0]['cells'][2]['properties'] = '';\n";
        $str .= $id."tbl.rows[0]['cells'][2]['class'] = 'header';\n";
        $str .= $id."tbl.rows[0]['cells'][2]['value'] = 'Параметры';\n";
        $str .= $id."tbl.rows[0]['cells'][2]['control'] = 'header';\n";
        $str .= $id."tbl.rows[0]['cells'][2]['control_properties'] = '';\n";

        $str .= $id."tbl.emptyrow = new Array;\n";
        $str .= $id."tbl.emptyrow['class'] = '';\n";
        $str .= $id."tbl.emptyrow['properties'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'] = new Array;\n";
        $str .= $id."tbl.emptyrow['cells'][0] = new Array;\n";
        $str .= $id."tbl.emptyrow['cells'][0]['properties'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][0]['control'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][0]['control_properties'] = 'deactivated=true,input_class=input1';\n";
        $str .= $id."tbl.emptyrow['cells'][0]['value'] = 'ИмяПоля';\n";
        $str .= $id."tbl.emptyrow['cells'][1] = new Array;\n";
        $str .= $id."tbl.emptyrow['cells'][1]['properties'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][1]['value'] = 'string';\n";
        $str .= $id."tbl.emptyrow['cells'][1]['control_properties'] = 'deactivated=true,input_class=input1';\n";
        $str .= $id."tbl.emptyrow['cells'][2] = new Array;\n";
        $str .= $id."tbl.emptyrow['cells'][2]['properties'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][2]['control_properties'] = 'deactivated=true,divname=".$this->parent_object_id."_divi,destroyDiv=true,showValue=true,width=100%,editorType=WABWindow,editorObject=PersistedFieldsEditor_,entity_id=".$this->parent_object_id."';\n";
        $str .= $id."tbl.emptyrow['cells'][3] = new Array;\n";
        $str .= $id."tbl.emptyrow['cells'][3]['properties'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][3]['class'] = 'hidden';\n";
        $str .= $id."tbl.emptyrow['cells'][3]['value'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][3]['control'] = 'plaintext';\n";
        $str .= $id."tbl.emptyrow['cells'][3]['control_properties'] = '';\n";
        //$str .= "tbl.emptyrow['cells'][2]['control_properties'] = '';\n";

        if ($this->persistedFields != "") {
            $persistedArray = explode("#",$this->persistedFields);
            $c=1;
            foreach ($persistedArray as $per_item) {
                $item = explode("|",$per_item);
                $name = $item[0];
                $type = $item[1];
                if (isset($item[2]) and $item[2]!="") {
                    $options_array = explode("~",$item[2]);
                    $opt_array = array();
                    foreach ($options_array as $opt) {
                        $opt_parts = explode("=",$opt);
                        $opt_array[$opt_parts[0]] = @$opt_parts[1];
                    }
                    if (!isset($opt_array["type"])) {
                        $type = $item[1];
                        $opt_array["type"] = $type;
                    }                    
                    $options_array = array();
                    foreach($opt_array as $key=>$value) {
                        $options_array[] = $key."=".$value;
                    }
                    $options = implode("~",$options_array);
                } else {
                    $options = "";
                    if (isset($item[1]))
                        $type=$item[1];
                    else
                        $type="string";
                    $options = "type=".$type;
                }

                $str .= $id."tbl.rows[$c] = new Array;\n";
                $str .= $id."tbl.rows[$c]['class'] = '';\n";
                $str .= $id."tbl.rows[$c]['properties'] = '';\n";
                $str .= $id."tbl.rows[$c]['cells'] = new Array;\n";
                $str .= $id."tbl.rows[$c]['cells'][0] = new Array;\n";
                $str .= $id."tbl.rows[$c]['cells'][0]['properties'] = '';\n";
                $str .= $id."tbl.rows[$c]['cells'][0]['class'] = 'cell';\n";
                $str .= $id."tbl.rows[$c]['cells'][0]['value'] = '$name';\n";
//                $str .= "tbl.rows[$c]['cells'][0]['control_properties'] = '';\n";

                $str .= $id."tbl.rows[$c]['cells'][1] = new Array;\n";
                $str .= $id."tbl.rows[$c]['cells'][1]['properties'] = '';\n";
                $str .= $id."tbl.rows[$c]['cells'][1]['class'] = 'cell';\n";
                $str .= $id."tbl.rows[$c]['cells'][1]['value'] = '$type';\n";
//                $str .= "tbl.rows[$c]['cells'][1]['control_properties'] = '';\n";

                $str .= $id."tbl.rows[$c]['cells'][2] = new Array;\n";
                $str .= $id."tbl.rows[$c]['cells'][2]['properties'] = '';\n";
                $str .= $id."tbl.rows[$c]['cells'][2]['class'] = 'cell';\n";
                $str .= $id."tbl.rows[$c]['cells'][2]['value'] = '$options';\n";
                $str .= $id."tbl.rows[$c]['cells'][2]['control_properties'] = 'deactivated=true,divname=".$this->parent_object_id."_contentDiv,destroyDiv=true,showValue=true,width=100%,editorType=WABWindow,editorObject=PersistedFieldsEditor_".$name.",entity_id=".$this->parent_object_id.",fieldName=".$name."';\n";

                $str .= $id."tbl.rows[$c]['cells'][3] = new Array;\n";
                $str .= $id."tbl.rows[$c]['cells'][3]['properties'] = '';\n";
                $str .= $id."tbl.rows[$c]['cells'][3]['class'] = 'hidden';\n";
                $str .= $id."tbl.rows[$c]['cells'][3]['value'] = '$name';\n";
                $str .= $id."tbl.rows[$c]['cells'][3]['control'] = 'plaintext';\n";
                $str .= $id."tbl.rows[$c]['cells'][3]['control_properties'] = '';\n";
                $c++;
            }
        }
        $result["{data}"] = $str;
        return $result;
    }
}
?>
