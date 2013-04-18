<?php
/**
 * Данный класс предназначен для отображения таблицы перенаправления портов,
 * которая отображается на закладке "Перенаправление портов" окна свойств
 * хоста.
 *
 * Данные берутся из поля portsString, которое имеет следующий формат:
 * ПортИнтернет~ТипПортаИнтернет~ПортХоста~ТипПортаХоста|ПортИнтернет~...
 * 
 * Функция getArgs() генерирует скрипт JavaScript, который строит таблицу
 * из значения этого поля. Генерируемое значение помещается в переменную {data},
 * которая кладется в JavaScript-обработчик этого класса. Таким образом скрипт
 * исполняется и строит таблицу.
 * 
 * @author andrey
 */
class PortsRedirectTable extends DataTable {
    function construct($params) {
        parent::construct($params);
        $this->portsString = "";
        $this->handler = "scripts/handlers/interface/DataTable.js";
        $this->clientClass = "PortsRedirectTable";
        $this->parentClientClasses = "DataTable~Entity";     
        $this->clientObjectId = str_replace(".","_",$this->getId());   
    }

    function getArgs() {
        $result = parent::getArgs();
        $id = str_replace(".","_",$this->getId());
        $str = "";
        $str .= $id."tbl.columns = new Array;\n";
        
        $str .= $id."tbl.columns[0] = new Array;\n";
        $str .= $id."tbl.columns[0]['name'] = 'outPortNumber';\n";
        $str .= $id."tbl.columns[0]['title'] = 'Порт Интернет';\n";
        $str .= $id."tbl.columns[0]['class'] = 'cell';\n";
        $str .= $id."tbl.columns[0]['properties'] = 'width=25%';\n";
        $str .= $id."tbl.columns[0]['control'] = 'integer';\n";
        $str .= $id."tbl.columns[0]['control_properties'] = 'deactivated=true,input_class=input1';\n";
        $str .= $id."tbl.columns[0]['must_set'] = true;\n";
        $str .= $id."tbl.columns[0]['unique'] = true;\n";
        $str .= $id."tbl.columns[0]['readonly'] = false;\n";

        $str .= $id."tbl.columns[1] = new Array;\n";
        $str .= $id."tbl.columns[1]['name'] = 'outPortType';\n";
        $str .= $id."tbl.columns[1]['title'] = 'Тип';\n";
        $str .= $id."tbl.columns[1]['class'] = 'cell';\n";
        $str .= $id."tbl.columns[1]['properties'] = 'width=25%';\n";
        $str .= $id."tbl.columns[1]['control'] = 'list,tcp~udp|tcp~udp';\n";
        $str .= $id."tbl.columns[1]['control_properties'] = 'deactivated=true,input_class=input1';\n";
        $str .= $id."tbl.columns[1]['must_set'] = true;\n";
        $str .= $id."tbl.columns[1]['unique'] = false;\n";
        $str .= $id."tbl.columns[1]['readonly'] = false;\n";

        $str .= $id."tbl.columns[2] = new Array;\n";
        $str .= $id."tbl.columns[2]['name'] = 'inPortNumber';\n";
        $str .= $id."tbl.columns[2]['title'] = 'Порт хоста';\n";
        $str .= $id."tbl.columns[2]['class'] = 'cell';\n";
        $str .= $id."tbl.columns[2]['properties'] = 'width=25%';\n";
        $str .= $id."tbl.columns[2]['control'] = 'integer';\n";
        $str .= $id."tbl.columns[2]['control_properties'] = 'deactivated=true,input_class=input1';\n";
        $str .= $id."tbl.columns[2]['must_set'] = true;\n";
        $str .= $id."tbl.columns[2]['unique'] = false;\n";
        $str .= $id."tbl.columns[2]['readonly'] = false;\n";

        $str .= $id."tbl.columns[3] = new Array;\n";
        $str .= $id."tbl.columns[3]['name'] = 'inPortType';\n";
        $str .= $id."tbl.columns[3]['title'] = 'Тип';\n";
        $str .= $id."tbl.columns[3]['class'] = 'cell';\n";
        $str .= $id."tbl.columns[3]['properties'] = 'width=25%';\n";
        $str .= $id."tbl.columns[3]['control'] = 'list,tcp~udp|tcp~udp';\n";
        $str .= $id."tbl.columns[3]['control_properties'] = 'deactivated=true,input_class=input1';\n";
        $str .= $id."tbl.columns[3]['must_set'] = true;\n";
        $str .= $id."tbl.columns[3]['unique'] = false;\n";
        $str .= $id."tbl.columns[3]['readonly'] = false;\n";

        $str .= $id."tbl.rows = new Array;\n";
        $str .= $id."tbl.rows[0] = new Array;\n";
        $str .= $id."tbl.rows[0]['class'] = '';\n";
        $str .= $id."tbl.rows[0]['properties'] = '';\n";
        $str .= $id."tbl.rows[0]['cells'] = new Array;\n";
        $str .= $id."tbl.rows[0]['cells'][0] = new Array;\n";
        $str .= $id."tbl.rows[0]['cells'][0]['properties'] = '';\n";
        $str .= $id."tbl.rows[0]['cells'][0]['value'] = 'Порт Интернет';\n";
        $str .= $id."tbl.rows[0]['cells'][0]['control'] = 'header';\n";
        $str .= $id."tbl.rows[0]['cells'][0]['control_properties'] = 'input_class=input1,deactivated=true';\n";
        $str .= $id."tbl.rows[0]['cells'][1] = new Array;\n";
        $str .= $id."tbl.rows[0]['cells'][1]['properties'] = '';\n";
        $str .= $id."tbl.rows[0]['cells'][1]['value'] = 'Тип';\n";
        $str .= $id."tbl.rows[0]['cells'][1]['control'] = 'header';\n";
        $str .= $id."tbl.rows[0]['cells'][1]['control_properties'] = 'input_class=input1,deactivated=true';\n";
        $str .= $id."tbl.rows[0]['cells'][2] = new Array;\n";
        $str .= $id."tbl.rows[0]['cells'][2]['properties'] = '';\n";
        $str .= $id."tbl.rows[0]['cells'][2]['value'] = 'Порт хоста';\n";
        $str .= $id."tbl.rows[0]['cells'][2]['control'] = 'header';\n";
        $str .= $id."tbl.rows[0]['cells'][2]['control_properties'] = 'input_class=input1,deactivated=true';\n";
        $str .= $id."tbl.rows[0]['cells'][3] = new Array;\n";
        $str .= $id."tbl.rows[0]['cells'][3]['properties'] = '';\n";
        $str .= $id."tbl.rows[0]['cells'][3]['value'] = 'Тип';\n";
        $str .= $id."tbl.rows[0]['cells'][3]['control'] = 'header';\n";
        $str .= $id."tbl.rows[0]['cells'][3]['control_properties'] = 'input_class=input1,deactivated=true';\n";

        $str .= $id."tbl.emptyrow = new Array;\n";
        $str .= $id."tbl.emptyrow['class'] = '';\n";
        $str .= $id."tbl.emptyrow['properties'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'] = new Array;\n";
        $str .= $id."tbl.emptyrow['cells'][0] = new Array;\n";
        $str .= $id."tbl.emptyrow['cells'][0]['properties'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][0]['control'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][0]['control_properties'] = 'deactivated=true,input_class=input1';\n";
        $str .= $id."tbl.emptyrow['cells'][0]['value'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][1] = new Array;\n";
        $str .= $id."tbl.emptyrow['cells'][1]['properties'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][1]['value'] = 'tcp';\n";
        $str .= $id."tbl.emptyrow['cells'][1]['control_properties'] = 'deactivated=true,input_class=input1';\n";
        $str .= $id."tbl.emptyrow['cells'][2] = new Array;\n";
        $str .= $id."tbl.emptyrow['cells'][2]['properties'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][2]['value'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][2]['control_properties'] = 'deactivated=true,input_class=input1';\n";
        $str .= $id."tbl.emptyrow['cells'][3] = new Array;\n";
        $str .= $id."tbl.emptyrow['cells'][3]['properties'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][3]['value'] = 'tcp';\n";
        $str .= $id."tbl.emptyrow['cells'][3]['control_properties'] = 'deactivated=true,input_class=input1';\n";

        if ($this->portsString != "") {
            $portsArray = explode("|",$this->portsString);
            $c=1;
            foreach ($portsArray as $port_item) {
                $item = explode("~",$port_item);
                $outPortNumber = $item[0];
                $outPortType = $item[1];
                $inPortNumber = $item[2];
                $inPortType = $item[3];
                

                $str .= $id."tbl.rows[$c] = new Array;\n";
                $str .= $id."tbl.rows[$c]['class'] = '';\n";
                $str .= $id."tbl.rows[$c]['properties'] = '';\n";
                $str .= $id."tbl.rows[$c]['cells'] = new Array;\n";
                $str .= $id."tbl.rows[$c]['cells'][0] = new Array;\n";
                $str .= $id."tbl.rows[$c]['cells'][0]['properties'] = '';\n";
                $str .= $id."tbl.rows[$c]['cells'][0]['class'] = 'cell';\n";
                $str .= $id."tbl.rows[$c]['cells'][0]['value'] = '$outPortNumber';\n";

                $str .= $id."tbl.rows[$c]['cells'][1] = new Array;\n";
                $str .= $id."tbl.rows[$c]['cells'][1]['properties'] = '';\n";
                $str .= $id."tbl.rows[$c]['cells'][1]['class'] = 'cell';\n";
                $str .= $id."tbl.rows[$c]['cells'][1]['value'] = '$outPortType';\n";

                $str .= $id."tbl.rows[$c]['cells'][2] = new Array;\n";
                $str .= $id."tbl.rows[$c]['cells'][2]['properties'] = '';\n";
                $str .= $id."tbl.rows[$c]['cells'][2]['class'] = 'cell';\n";
                $str .= $id."tbl.rows[$c]['cells'][2]['value'] = '$inPortNumber';\n";

                $str .= $id."tbl.rows[$c]['cells'][3] = new Array;\n";
                $str .= $id."tbl.rows[$c]['cells'][3]['properties'] = '';\n";
                $str .= $id."tbl.rows[$c]['cells'][3]['class'] = 'cell';\n";
                $str .= $id."tbl.rows[$c]['cells'][3]['value'] = '$inPortType';\n";                
                $c++;
            }
        }
        $result["{data}"] = $str;
        return $result;
    }
}
?>