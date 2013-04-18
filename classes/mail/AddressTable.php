<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AddressBookTable
 *
 * @author andrey
 */
class AddressTable extends Table {

    function construct($params) {
        parent::construct($params);
        $this->module_id = $params[0]."_".$params[1];
        array_shift($params);array_shift($params);
        $this->address = implode("_",$params);
        $this->clientClass = "AddressTable";
        $this->parentClientClasses = "Table~Entity";        
        $this->init();
    }

    function init() {
        $this->rows = 1;
        $this->cols = 1;
        $this->width= "100%";
        $this->height = "100%";
        global $Objects;
        $addr = $Objects->get("Address_".$this->module_id."_".$this->address);
        $addr->load();
        $arr = $addr->getFields();
        $arr = explode("~",$arr);
        $counter = 1;
        $this->row_properties[0] = "id:row0";
        $this->cell_properties[0][0] = "id:col0_0|class:header|innerHTML:Поле|editable:false|unique:false|must_set:false|width:10%~id:col0_1|class:header|innerHTML:Значение|editable:false|unique:false|must_set:false|width:90%";
        foreach($arr as $value) {
            $parts = explode("|",$value);
            $this->row_properties[$counter] = "id:row".$counter;
            $this->cell_properties[$counter][0] = "id:col".$counter."_0|class:cell|innerHTML:".$parts[0]."|editable:true|unique:true|must_set:true~id:col".$counter."_0|class:cell|innerHTML:".@$parts[1]."|editable:true|unique:false|must_set:false";
            $counter++;
        }
    }

    function getId() {
        return "Table_".$this->module_id."_".$this->address;
    }
}
?>