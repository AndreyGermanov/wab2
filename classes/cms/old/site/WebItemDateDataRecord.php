<?php
/**
 * Данный класс управляет полем данных типа "Date" для элемента
 * Web-сайта. Является потомком класса WebItemDataRecord, который
 * определяет модель хранения данных поля элемента.
 *
 * Данные хранятся в таблице data_date.
 *
 *
 */
class WebItemDateDataRecord extends WebItemDataRecord{

    function setTableDefinition() {
        parent::setTableDefinition();
        $this->hasColumn("value",'timestamp');
        $this->setTableName("data_date");
    }

    function setUp() {
        parent::setUp();
        $this->actAs('Searchable', array('fields'=>array('value')));
    }
}
?>