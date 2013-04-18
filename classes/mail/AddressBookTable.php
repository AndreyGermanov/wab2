<?php
/**
 * Класс для управления таблицей, отображающей адресную книгу
 *
 * @author andrey
 */
class AddressBookTable extends Table {

    function construct($params) {
        parent::construct($params);
        $this->clientClass = "AddressBookTable";
        $this->parentClientClasses = "Table~Entity";        
        $this->init();
    }

    function init() {
        $this->rows = 1;
        $this->cols = 1;
        $this->width= "100%";
        $this->height = "100%";
        global $Objects;
        $addrbook = $Objects->get("AddressBook_".$this->module_id);
        $addrbook->loadDefaults();
        $arr = explode("|",$addrbook->getDefaultFields());
        $counter = 1;
        $this->row_properties[0] = "id:row0";
        $this->cell_properties[0][0] = "id:col0_0|class:header|innerHTML:Поля адресной книги|editable:false|unique:false|must_set:false";
        foreach($arr as $value) {
            $this->row_properties[$counter] = "id:row".$counter;
            $this->cell_properties[$counter][0] = "id:col".$counter."_0|class:cell|innerHTML:".$value."|editable:true|unique:true|must_set:true";
            $counter++;
        }
    }
}
?>