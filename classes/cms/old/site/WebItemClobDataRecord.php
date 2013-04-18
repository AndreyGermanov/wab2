<?php
/**
 * Данный класс управляет полем данных типа "clob" для элемента
 * Web-сайта. Является потомком класса WebItemDataRecord, который
 * определяет модель хранения данных поля элемента.
 *
 * Данные хранятся в таблице data_clob.
 *
 *
 */
class WebItemClobDataRecord extends WebItemDataRecord{

    function setTableDefinition() {
        parent::setTableDefinition();
        $this->hasColumn("value",'clob');
        $this->setTableName("data_clob");
    }

    function setUp() {
       parent::setUp();
       $this->actAs('Searchable', array('fields'=>array('value')));
    }
}
?>