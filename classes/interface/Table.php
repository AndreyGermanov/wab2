<?php
/*
 * Класс для элемента интерфейса "Таблица"
 *
 */
class Table extends WABEntity {

    public $column_properties = array();
    public $row_properties = array();
    public $cell_properties = array();

    function construct($params) {
        $this->module_id = array_shift($params);
        $this->module_id.= "_".array_shift($params);
        if (count($params)>1) {
            array_pop($params);
        }
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $this->skinPath = $app->skinPath;
        $this->width = "";
        $this->height = "";
        $this->object_id = implode("_",$params);
        $this->template = "templates/interface/Table.html";
        $this->css = $this->skinPath."styles/Table.css";
        $this->handler = "scripts/handlers/interface/Table.js";       
        $this->clientClass = "Table";
        $this->parentClientClasses = "Entity";        
    }

    function init() {
        
    }
    function getId() {
        return "Table_".$this->module_id."_".$this->object_id;
    }

    function getProperties($entity_type,$number="",$property_name="") {
        $_POST["ajax"] = true;
        $res = $this->$entity_type;
        if ($number!="") {            
            $num_array = explode(",",$number);
            if (count($num_array)>1) {
                $row_number = $num_array[0];
                $col_number = $num_array[1];
                if ($col_number!="" and isset($this->$res[$row_number])) {
                    $type = $this->$res[$row_number][$col_number];
                }
                else if (isset($res[$row_number]))
                    $type = $res[$row_number];
            }
            else
                $type = $res[$number];

            if (isset($type)) {
                    if ($property_name!="") {
                        $result = $type[$property_name];
                        if (isset($_POST["ajax"]))
                            echo $result;
                        return $result;
                    }
                    else                        
                        $result = $type;
            }
                else
                    $result = "";

                if (isset($_POST["ajax"])) {
                    if ($result=="")
                        echo $result;
                    else {
                        $string = array();
                        foreach($result as $key=>$value) {
                            $string[count($string)] = $key.":".$value;
                        }
                        $string=implode("|",$string);
                        echo $string;
                        return $string;
                    }
                }
                return $result;
        }
        else
        {
            $string = array();
            if (isset($_POST["ajax"])) {
                foreach($this->$entity_type as $key=>$value) {
                    $string[count($string)] = $this->getColumnProperties($entity_type,$key);
                }
                $string = implode("~",$string);
                echo $string;
                return $string;
            }
            return $res;
        }
    }

    function setProperties($entity_type,$value,$number="",$property_name="") {
        if ($number!="") {
            $number_array = explode(",",$number);
            $result = array();
            if (count($number_array)>1) {
                $row_num = $number_array[0];
                $col_num = $number_array[1];
                //echo $entity_type;
                if ($col_num!="") {
                    if ($property_name!="") {
                        $result[$row_num][$col_num][$property_name] = $value;
                    }
                    else {
                        if (isset($_POST["ajax"])) {
                            $fields = explode("|",$value);
                            foreach ($fields as $field) {
                                $parts = explode(":",$field);
                                $result[$row_num][$col_num][$parts[0]] = $parts[1];
                            }
                        }
                        else
                        {
                            $result[$row_num]= array();
                            $result[$row_num][$col_num] = $value;
                        }
                    }
                }
            }
            else {
                if ($property_name!="") {
                    $result[$number][$property_name] = $value;
                }
                else {
                    if (isset($_POST["ajax"])) {
                        $fields = explode("|",$value);
                        foreach ($fields as $field) {
                            $parts = explode(":",$field);
                            $result[$number][$parts[0]] = $parts[1];
                        }
                    }
                    else
                        $result[$number] = $value;
                }
            }
            $this->$entity_type = $result;
        }
        else {
            $columns = explode("~",$value);
            foreach ($columns as $key=>$value)
                $this->setColumnProperties($entity_type,$value,$key);
        }
    }

    function getColumnProperties($column_number="",$property_name="") {
        return $this->getProperties("column_properties",$column_number,$property_name);
    }

    function setColumnProperties($value,$column_number="",$property_name="r") {
        $this->setProperties("column_properties",$value,$column_number,$property_name);
    }

    function getRowProperties($row_number="",$property_name="") {
        return $this->getProperties("row_properties",$row_number,$property_name);
    }

    function setRowProperties($value,$row_number="",$property_name="") {
        $this->setProperties("row_properties",$row_number,$property_name);
    }

    function getCellProperties($cell_number="",$property_name="") {
        return $this->getProperties("cell_properties",$cell_number,$property_name);
    }

    function setCellProperties($value,$cell_number,$property_name="") {
        $this->setProperties("cell_properties",$value,$cell_number,$property_name);
    }

    function getArgs() {
        $result = parent::getArgs();
        $result["{row_properties}"] = str_replace("'","\'",implode("~",$this->row_properties));
        $res = array();
        for ($counter=0;$counter<count($this->cell_properties);$counter++) {
            $res[count($res)] = $counter."#".implode("~",$this->cell_properties[$counter]);
        }
        $result["{cell_properties}"] = str_replace("'","\'",implode("&",$res));
        return $result;
    }
}
?>