<?php
/**
 * Данный класс управляет полем данных типа "Images" для элемента
 * Web-сайта. Является потомком класса WebItemDataRecord, который
 * определяет модель хранения данных поля элемента.
 *
 * Данные хранятся в таблице data_images.
 *
 *
 */
class WebItemImagesDataRecord extends WebItemDataRecord{

    function setTableDefinition() {
        parent::setTableDefinition();
        $this->hasColumn("value",'string',255);
        $this->setTableName("data_images");
    }

    function setUp() {
        parent::setUp();
        $this->actAs('Searchable', array('fields'=>array('value')));
    }
}
?>