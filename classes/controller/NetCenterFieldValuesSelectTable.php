<?php
/**
 * Класс, обрабатывающий таблицу возможных значений свойства хоста.
 * Используется отчетом NetCenterReport для установки отбора по значениям
 * свойств.
 *
 * Для загрузки принимает в качетсве параметра имя свойства хоста в переменной
 * field_name, а также список  выбранных значений свойства в параметре values_list.
 * Значения свойств представляют из себя строку, разделенную символом ~.
 *
 * Класс выдает таблицу из двух колонок. В первой колонке находится флажок,
 * в который можно установить галочку. (Для строк, имеющих значения из values_list
 * этот флажок изначально установлен). Во второй колонке находится текстовое
 * представление значения свойства.
 *
 * Эта таблица передается клиентской части приложения в виде строки data, имеющей
 * формат:
 *
 * checked/not checked~значение поля|checked-not checked~значение поля|
 *
 * На базе этой переменной клиентская часть программы по шаблону генерирует
 * таблицу
 *
 * @author andrey
 */
class NetCenterFieldValuesSelectTable extends WABEntity {

    public $subnets;
    
    function construct($params) {

        $this->module_id = $params[0]."_".$params[1];
        $this->name = $params[2];
        $this->field_name = "name";
        $this->values_list = "";

        $this->template = "templates/interface/Table.html";
        $this->css = "styles/Mailbox.css";
        $this->handler = "scripts/handlers/controller/NetCenterFieldValuesSelectTable.js";

        global $Objects;
        $this->subnets = $Objects->get("DhcpSubnets_".$this->module_id."_Subnets");
        if (!$this->subnets->loaded)
            $this->subnets->load();
        
        $this->clientClass = "NetCenterFieldValuesSelectTable";
        $this->parentClientClasses = "Entity";                
    }

    function getId() {
        return "NetCenterFieldValuesSelectTable_".$this->module_id."_".$this->name;
    }

    function getArgs() {

        global $Objects;
        
        $result = parent::getArgs();
        $values = explode("~",$this->values_list);
        $arr = array();
        $strings = array();
        foreach($this->subnets->subnets as $subnet) {
            if (!$subnet->hosts_loaded)
                $subnet->loadHosts(true);
            $hosts_arr = $subnet->hosts;
            $hosts = $hosts_arr;
            foreach ($hosts_arr as $host) {
                $obj_groups = explode(",",$host->objectGroup);
                if (count($obj_groups)>1) {
                    $host->objectGroup = $obj_groups[0];
                    array_shift($obj_groups);
                    foreach($obj_groups as $obj_group) {
                        $host_copy = clone $host;
                        $host_copy->objectGroup=$obj_group;
                        $hosts[] = $host_copy;
                    }
                }
            }            
            foreach($hosts as $host) {
                if ($this->field_name=="accessRules") {
                    $value_keys = array();
                    $value_values = array();
                    foreach($host->accessRules as $key=>$val) {
                        if ($val["read_only"]=="yes") {
                            $value_keys[count($value_keys)] = $key."-".$val["path"]."-".$val["read_only"];
                            $value_values[count($value_values)]= $key."-".$val["path"]."-чтение";
                        }
                        else {
                            $value_keys[count($value_keys)] = $key."-".$val["path"]."-".$val["read_only"];
                            $value_values[count($value_values)] = $key."-".$val["path"]."-запись";
                        }
                    }
                    $value = implode("<br>",$value_keys)."~".implode("<br>",$value_values);
                } else {
                    $value = $host->fields[$this->field_name];
                    $value = str_replace("|","\n",$value);
                }
                if ($value=="allow booting")
                    $value = "allow booting~да";
                if ($value=="deny booting")
                    $value = "deny booting~нет";
                if ($value=="false")
                    $value = "false~нет";
                if ($value=="true")
                    $value = "true~да";
                if ($this->field_name=="objectGroup") {
                    $obj_group = $Objects->get("ObjectGroup_".$this->module_id."_".$value);
                    if (!$obj_group->loaded)
                            $obj_group->load();
                    if ($obj_group->loaded)
                        $value = $value."~".$obj_group->name;
                    else
                        $value = "0~Вне групп";
                }
                if ($this->field_name=="subnet_name") {
                    $value = $value."~".$subnet->title."(".$value.")";
                }
                if ($this->field_name=="host_type") {
                    $value = $value."~".$host->host_types[$value];
                }
                if (array_search($value,$arr)===FALSE and $value!="" and $value!="~") {
                    $arr[count($arr)] = str_replace("\n","<br>",$value);
                }
            }
        }
        foreach($arr as $item) {
            if ($item=="")
                continue;
            $item1 = explode("~",$item);
            
            $item1 = $item1[0];
            if (array_search($item1,$values)!==FALSE)
                $strings[count($strings)] = "checked~".$item;
            else
                $strings[count($strings)] = "not checked~".$item;
        }
        $result["{data}"] = str_replace("\n","<br>",str_replace("'","\'",implode("|",$strings)));
        return $result;
    }
}
?>