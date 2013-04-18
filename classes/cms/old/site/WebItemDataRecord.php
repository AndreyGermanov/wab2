<?php
/**
 * Данный класс управляет значением одного поля элемента web-сайта. Значения
 * полей элементов записываются в таблицу. Каждое поле создает запись в
 * этой таблице. Каждая запись содержит следующие поля:
 *
 * id - уникальный идентификатор записи
 * value - значение записи (тип зависит от типа метаданных этого поля)
 * author - автор, который ее создал
 * metadata_id - идентификатор поля из таблицы метаданных, которому принадлежит
 *               это значение
 * item_id - идентификатор элемента Web-сайта, которому принадлежит это значение
 * tag - произвольные данные, привязанные к элементу
 *
 * Также, этот класс реализует интерфейс Timestampable, который добавляет поля
 * временных меток created и updated, в которые записывается время создания и
 * время последней модификации поля.
 *
 * Данный класс является абстрактным. Он определяет модель хранения записи. Реально
 * используются его потомки WebItemIntDataRecord, WebItemStringDataRecord, WebItemDateDataRecord,
 * WebItemBooleanDataRecord, WebItemClobDataRecord и WebItemBlobDataRecord,WebItemItemDataRecord.
 * Данные хранятся в соответсвующих таблицах data_int, data_string, data_date, data_boolean,
 * data_clob и data_blob.
 * 
 */

class WebItemDataRecord extends Doctrine_Record {

    function setTableDefinition() {
        $this->setTableName("data");
        $this->hasColumn("metadata_id",'integer');
        $this->hasColumn("item_id",'integer');
        $this->hasColumn("author",'string',50);
        $this->hasColumn("tag",'string',255);
    }

    public function setUp() {

        $this->hasOne("WebItemMetadataRecord as metadata",array(
            'local' => "metadata_id",
            'foreign' => "id",
            'onDelete' => "CASCADE"
            )
        );

        $this->hasOne("WebItemRecord as item",array(
            'local' => "item_id",
            'foreign' => "id",
            'onDelete' => "CASCADE"
            )
        );

        $this->actAs("Timestampable");

    }

}
?>
