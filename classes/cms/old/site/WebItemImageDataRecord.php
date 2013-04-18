<?php
/**
 * Данный класс управляет полем данных типа "Image" для элемента
 * Web-сайта. Является потомком класса WebItemDataRecord, который
 * определяет модель хранения данных поля элемента.
 *
 * Данные хранятся в таблице data_image.
 *
 *
 */
class WebItemImageDataRecord extends WebItemDataRecord{

    function setTableDefinition() {
        parent::setTableDefinition();
        $this->hasColumn("value",'string',255);
        $this->setTableName("data_image");
    }

    function setUp() {
        parent::setUp();
        $this->actAs('Searchable', array('fields'=>array('value')));
    }
}
?>