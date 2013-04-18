<?php
/**
 * Данный класс управляет полем данных типа "String" для элемента
 * Web-сайта. Является потомком класса WebItemDataRecord, который
 * определяет модель хранения данных поля элемента.
 *
 * Данные хранятся в таблице data_string.
 *
 *
 */
class WebItemStringDataRecord extends WebItemDataRecord{

    function setTableDefinition() {
        parent::setTableDefinition();
        $this->hasColumn("value",'string',255);
        $this->setTableName("data_string");
    }

    function setUp() {
        parent::setUp();
        $this->actAs('Searchable', array('fields'=>array('value')));
    }
}
?>